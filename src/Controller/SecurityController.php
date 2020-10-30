<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $userRepository;
    private $csrfTokenManager;
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        MailerInterface $mailer
    )
    {
        $this->userRepository = $userRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request,AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error){
            $session = $request->getSession();
            $session->getFlashBag()->add('error', $error->getMessageKey());
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',array('last_username' => $lastUsername));
    }

    /**
     * @Route("/login/activate/{token}", name="app_activate")
     */
    public function activate(Request $request,$token)
    {
        $user = $this->userRepository->findOneBy(array('confirmation_token'=>$token));
        $session = $request->getSession();
        if ($user){
            $user->setIsActive(true);
            $user->setConfirmationToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $session->getFlashBag()->add('success', 'Votre compte a bien été activé, bienvenu 🙂');
        }else{
            $session->getFlashBag()->add('warning', 'Aucun utilisateur associé à cette clef 😥');
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     *
     * @Route("/login/reset_password/{token}", name="app_reset_password")
     * @param Request $request
     * @param $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request,$token,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->userRepository->findOneBy(array('rp_token'=>$token));
        $session = $request->getSession();
        if (!$user){
            $session->getFlashBag()->add('warning', 'Aucun utilisateur associé à cette clef 😥');
            return $this->redirectToRoute('app_login');
        }
        $now = new \DateTime('now');
        if ($now > date_add($user->getRpTokenCreatedAt(),date_interval_create_from_date_string('1 day'))){
            $user->setRpTokenCreatedAt(null);
            $user->setRpToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $session->getFlashBag()->add('error', 'Cette url a expirée 😿');
            return $this->redirectToRoute('app_login');
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('reset_password', $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            if ($request->request->get('password')===$request->request->get('password_bis')){
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                ));
                $user->setRpTokenCreatedAt(null);
                $user->setRpToken(null);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $session->set(Security::LAST_USERNAME, $user->getUsername());
                $session->getFlashBag()->add('success', 'Mot de passe changé ! 💪');
            }else{
                $session->getFlashBag()->add('error', 'Oups, il semble que tu n\'ai pas tapé deux fois la même chose ? 😞');
                return $this->render('security/reset_password.html.twig',array('user'=>$user));
            }
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password.html.twig',array('user'=>$user));
    }

    /**
     * @Route("/login/forgot_password", name="app_forgot_password")
     */
    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('forgot_password', $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $email_or_username = $request->request->get('email_or_username');
            $session = $request->getSession();
            if (!$email_or_username){
                $session->getFlashBag()->add('error', 'please provide a username or email');
                return $this->redirectToRoute('app_forgot_password');
            }
            if (strpos($email_or_username,'@')===false){
                $user = $this->userRepository->findOneByUsername($email_or_username);
                if ($user){
                    $user->generateRpToken();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->_sendForgotPassword($user);
                }
                $session->getFlashBag()->add('success', 'Si un utilisateur est bien associé à ce pseudo, un email vient de lui être envoyé 😉');
            }else{
                $user = $this->userRepository->findOneByEmail($email_or_username);
                if ($user){
                    $user->generateRpToken();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->_sendForgotPassword($user);
                }
                $session->getFlashBag()->add('success', 'Si un utilisateur existe avec cet email, un email vient de lui être envoyé 😉');
            }
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/forgot_password.html.twig');
    }

    private function _sendForgotPassword(User $user){
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to($user->getEmail())
            ->subject('Mot de passe oublié ?')
            ->htmlTemplate('emails/forgot_password.html.twig')
            ->textTemplate('emails/forgot_password.txt.twig')
            ->context([
                'user' => $user,
            ]);
        $this->mailer->send($email);
    }

    private function _sendConfirm(User $user){
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to($user->getEmail())
            ->subject('Confirme ton nouveau compte')
            ->htmlTemplate('emails/confirm.html.twig')
            ->textTemplate('emails/forgot_password.txt.twig')
            ->context([
                'user' => $user,
            ]);
        $this->mailer->send($email);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $code = $request->request->get('code');
            $session = $request->getSession();
            if ($code === 'apis_mellifera'){
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                ));
                $user->setConfirmationToken(md5(uniqid()));
                $user->setIsActive(false); //need to validate first
                $session->getFlashBag()->add('success', 'Vérifie tes emails pour valider ton compte :)');
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->_sendConfirm($user);

                return $this->redirectToRoute('home');
            }else{
                $session->getFlashBag()->add('error', 'oups, ce code n\'existe pas ! 😩');
                return $this->render('security/register.html.twig');
            }

        }
        return $this->render('security/register.html.twig');
    }
}
