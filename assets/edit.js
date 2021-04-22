import 'dropzone';
import $ from 'jquery';

let checkboxes = document.querySelectorAll('label.checkbox,label.radio');
Array.from(checkboxes).forEach((el) => {
    el.addEventListener('click',function (event) {
        if (event.target.localName === 'input'){
            let l = event.target.closest('label');
            l.classList.toggle('checked');
            let p = l.closest('div[data-type]'); //wont work on ie.
            if (p){
                Array.from(p.querySelectorAll('label.radio.checked')).forEach((el) => {
                   if (el !== l){
                       el.classList.toggle('checked');
                   }
                });
                let type = p.getAttribute('data-type');
                switch (type){
                    case 'attribute':
                        attributeLineEditSubmit(l.closest("form"));//wont work on ie.
                        break;
                    case 'property':
                        propertyLineEditSubmit(l.closest("form"));//wont work on ie.
                        break;
                    default:
                        break;
                }
            }
        }
    });
});
let textarea = document.querySelectorAll('textarea,input[type=text]:not([data-type=digit])');
Array.from(textarea).forEach((el) => {
    el.addEventListener('keyup',function (event) {
        inputKeyup(this);
    });
});
let numbers = document.querySelectorAll('input[type=number]');
Array.from(numbers).forEach((el) => {
    el.addEventListener('change',function (event) {
        inputKeyup(this);
    });
});

let inputs = document.querySelectorAll("input[data-type=digit]");
Array.from(inputs).forEach((el) => {
    el.addEventListener("keyup", (evt) => {
        const regex = /[^0-9]/ig;
        el.value = evt.target.value.replaceAll(regex,'');
        inputKeyup(evt.target);
    });
});

$(document).on('click','[data-type=delete]',function (event){
    onDeleterClick(this);
});
function onDeleterClick(el){
    let test = confirm('êtes-vous certain de vouloir supprimer cette entrée ?');
    if (test){
        let form = el.closest("form");//wont work on ie.
        let to_delete = document.querySelector(el.getAttribute('data-target'));
        to_delete.classList.add('hidden');
        $.ajax({
            method: "POST",
            url: $(form).attr('action'),
            data: $(form).serialize()
        }).done(function( ret ) {
            if (ret.success){
                to_delete.parentNode.removeChild(to_delete);
            }else{
                to_delete.classList.remove('hidden');
            }
            handleReturn(ret);
        });
    }
    return false;
}
function inputKeyup(el){
    let p = el.closest('div[data-type]'); //wont work on ie.
    if (p){
        let type = p.getAttribute('data-type');
        switch (type){
            case 'attribute':
                attributeLineEditSubmit(el.closest("form"));//wont work on ie.
                break;
            case 'property':
                propertyLineEditSubmit(el.closest("form"));//wont work on ie.
                break;
            default:
                break;
        }
    }
}
//Custom function that changes a select element's option.
function select(selectId, optionValToSelect){
    //Get the select element by it's unique ID.
    var selectElement = document.getElementById(selectId);
    //Get the options.
    var selectOptions = selectElement.options;
    //Loop through these options using a for loop.
    for (var opt, j = 0; opt = selectOptions[j]; j++) {
        //If the option of value is equal to the option we want to select.
        if (opt.value == optionValToSelect) {
            //Select the option and break out of the for loop.
            selectElement.selectedIndex = j;
            break;
        }
    }
}
function attributeLineEditSubmit(form){
    $.ajax({
        method: "POST",
        url: $(form).attr('action'),
        data: $(form).serialize()
    }).done(function( ret ) {
        handleReturn(ret);
    });
    return false;
}
function propertyLineEditSubmit(form){
    const data = new FormData(form);
    const value = Object.fromEntries(data.entries());
    value.multiple = data.getAll("multiple");
    $.ajax({
        method: "POST",
        url: $(form).attr('action'),
        data: value
    }).done(function( ret ) {
        handleReturn(ret);
    });
    return false;
}
$(document).on('click','#add_source',function (event){
    sourceLineSubmit(this.closest('form'));
});
function sourceLineSubmit(form){
    let val = form.querySelector('[name=value]').value;
    if (val.length > 5 && validURL(val)){
        form.classList.add('hidden');
        $.ajax({
            method: "POST",
            url: $(form).attr('action'),
            data: $(form).serialize()
        }).done(function( ret ) {
            $('#sources').replaceWith(ret);
        });
    }else{
        form.querySelector('[name=value]').classList.add('error');
        form.querySelector('[name=value]').addEventListener('keyup',function (){
            this.classList.remove('error');
            this.removeEventListener('keyup',function (){});
        });
    }
    return false;
}
let family = document.getElementById('family');
let familylabel = document.querySelector('label[for=family]');
let family_name = document.getElementById('family_name');
let family_name_submit = document.getElementById('family_name_submit');
familylabel.addEventListener('click',function (event){
    event.target.classList.add('hidden');
    family.classList.remove('hidden');
    family.focus();
});
family_name_submit.addEventListener('click',function (event){
    family_name.classList.add('hidden');
    family_name_submit.classList.add('hidden');
    family.classList.remove('hidden');
    $.ajax({
        method: "POST",
        url: family.closest('form').getAttribute('action'),
        data: $(family.closest('form')).serialize()
    }).done(function( ret ) {
        handleReturn(ret);
        if (ret.success){
            familylabel.innerHTML = family_name.value;
            familylabel.classList.remove('hidden');
            family.classList.add('hidden');
            if (ret.data && ret.data.family_id){
                var opt = document.createElement('option');
                opt.value = ret.data.family_id;
                opt.innerHTML = family_name.value;
                family.appendChild(opt);
                select('family',ret.data.family_id);
                family_name.value = '';
            }
        }else{
            family_name.classList.remove('hidden');
            family_name_submit.classList.remove('hidden');
            family.classList.add('hidden');
        }
    });
});
family.addEventListener('change',function (event){
    let id = family.value;
    if (id === '0'){
        family_name.classList.remove('hidden');
        family_name_submit.classList.remove('hidden');
        family.classList.add('hidden');
    }else{
        $.ajax({
            method: "POST",
            url: family.closest('form').getAttribute('action'),
            data: $(family.closest('form')).serialize()
        }).done(function( ret ) {
            handleReturn(ret);
            familylabel.innerHTML = family.options[family.selectedIndex].text;
            familylabel.classList.remove('hidden');
            family.classList.add('hidden');
        });
    }
});

$(document).on('click','#add_img',function (event){
    newImageUrl(this.closest('form'));
});
function newImageUrl(form){
    let val = form.querySelector('[name=value]').value;
    if (val.length > 5 && validURL(val)){
        form.classList.add('hidden');
        let $g = $('#gallery').find('.image-list');
        $g.animate({opacity:0},1000);
        $.ajax({
            method: "POST",
            url: $(form).attr('action'),
            data: $(form).serialize()
        }).done(function( ret ) {
            $g.animate({opacity:1},100,function (){
                $g.replaceWith($('<div></div>').html(ret).find('.image-list'));
                $g.animate({opacity:1},100);
            });
            form.querySelector('input[type=text]').value = '';
            form.classList.remove('hidden');
        });
    }else{
        form.querySelector('[name=value]').classList.add('error');
        form.querySelector('[name=value]').addEventListener('keyup',function (){
            this.classList.remove('error');
            this.removeEventListener('keyup',function (){});
        });
    }
    return false;
}
function handleReturn(json){
    let display_delay = 1000;
    if (json.delay){
        display_delay = json.delay;
    }
    if (json.success){
        let $s = $('#success');
        $s.hide(100,function () {
            $s.find('.message').html('');
        });
        $s.find('.message').html(json.message);
        $s.show();
        setTimeout(function (){
            $s.hide(500,function () {
                $s.find('.message').html('');
            });
        },display_delay);
    }else if(json.error){
        let $e = $('#error');
        $e.hide(100,function () {
            $e.find('.message').html('');
        });
        $e.find('.message').html(json.message);
        $e.show();
        setTimeout(function (){
            $e.hide(500,function () {
                $e.find('.message').html('');
            });
        },display_delay);
    }
}
function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-\\séèà]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}
Dropzone.options.myDropzone = {
    init: function() {
        this.on("complete", function(file) {
            let $g = $('#gallery').find('.image-list');
            $g.animate({opacity:0},1000);
            this.removeFile(file);
            $.ajax({
                method: "POST",
                url: document.querySelector('#my_dropzone').getAttribute('data-refresh'),
                data: {'refresh':true}
            }).done(function( ret ) {
                $g.animate({opacity:1},100,function (){
                    $g.replaceWith($('<div></div>').html(ret).find('.image-list'));
                    $g.animate({opacity:1},100);
                });
            });
        });
    }
};