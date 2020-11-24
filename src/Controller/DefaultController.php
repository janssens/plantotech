<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use App\Service\ConfigService;
use App\Service\MailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    private $csrfTokenManager;
    private $mailer;
    private $config;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        MailService $mailer,
        ConfigService $configService
    )
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->mailer = $mailer;
        $this->config = $configService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {

        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('contact', $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $session = $request->getSession();
            if ($request->get('firstname')){ //trap
                $session->getFlashBag()->add('success', 'Hello robot 👋 🤖');
                return $this->render('home/contact.html.twig');
            }
            $name = $request->get('name');
            $email_address = $request->get('email');
            $subject = $request->get('subject');
            $message = $request->get('message');
            if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                $session->getFlashBag()->add('error', 'format d\'email invalide 📧');
                return $this->render('home/contact.html.twig',array('name'=>$name,'subject'=>$subject,'email'=>$email_address,'message'=>$message));
            }
            if (strlen($message)<3 || strlen($subject)<3){
                $session->getFlashBag()->add('error', 'Un message à faire passer ? 📜');
                return $this->render('home/contact.html.twig',array('name'=>$name,'subject'=>$subject,'email'=>$email_address,'message'=>$message));
            }
            $email = (new TemplatedEmail())
                ->from($this->config->getValue('app/main_email'))
                ->to($email_address)
                ->subject('['.$this->config->getValue('app/website_name').'] ton message a bien été transmit')
                ->htmlTemplate('emails/contact_from.html.twig')
                ->textTemplate('emails/contact_from.txt.twig')
                ->context([
                    'name' => $name,
                    'mail' => $email_address,
                    'subject' => $subject,
                    'message' => $message,
                ]);
            if ($e = $this->mailer->sendMail($email)){
                $session->getFlashBag()->add('error', 'oups, l\'email de confirmation na pas pu être envoyé.'.
                    ' On réessaie ensemble dans un moment ?');
                $session->getFlashBag()->add('warning', $e->getMessage());
            }else{
                $email = (new TemplatedEmail())
                    ->from($this->config->getValue('app/main_email'))
                    ->to($this->config->getValue('app/main_email'))
                    ->replyTo($email_address)
                    ->subject('['.$this->config->getValue('app/website_name').'] contact : '.$subject)
                    ->htmlTemplate('emails/contact_to.html.twig')
                    ->textTemplate('emails/contact_to.txt.twig')
                    ->context([
                        'name' => $name,
                        'mail' => $email_address,
                        'subject' => $subject,
                        'message' => $message,
                    ]);
                if ($e = $this->mailer->sendMail($email)){
                    $session->getFlashBag()->add('error', 'oups, l\'email de confirmation na pas pu être envoyé.'.
                        ' On réessaie ensemble dans un moment ?');
                    $session->getFlashBag()->add('warning', $e->getMessage());
                } else{
                    $session->getFlashBag()->add('success', 'Ton message a bien été envoyé, merci :)');
                }
            }
            return $this->redirectToRoute('home');
        }
        $email = '';
        if ($this->isGranted('ROLE_USER'))
            $email = $this->getUser()->getEmail();
        return $this->render('home/contact.html.twig',array('email'=>$email));
    }
}
