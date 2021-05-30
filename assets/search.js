import $ from 'jquery';

$('#filters').click(function (e){
    if ($(e.target).is('.close')){
        e.preventDefault();
        let $tag = $(e.target).closest('.tag');
        let k = $tag.data('key');
        let v = $tag.data('value');
        $('[name^="'+k+'"] option[data-f-value="'+v+'"]').prop("selected",false); // in all form
        $('input[name^="'+k+'"][data-f-value="'+v+'"]').prop("checked",false); // in all form
        ajaxSubmit($('#form_filter form'));
    }
})
$('#filters_list a').click(function (e){
    e.preventDefault();
    let key = $(this).attr('href').substr(1);
    $('.fblock.'+key).show();
    $('.fblock:not(.'+key+')').hide();
    $('#form_filter [type=submit]').show();
    $('#filters_list a').removeClass('active');
    $(this).addClass('active');
});
$('#form_filter form input').click(function (e){
    ajaxSubmit($('#form_filter form'));
});
// $('.fblock').each(function (){
//     let counter = $(this).find(':selected').length;
//     console.log($(this).attr('id')+':'+counter);
// });
window.addEventListener('popstate', function(e){
    if(e.state)
        ajaxSubmit($('#form_filter form'),e.state.data + '&' + '_ajax=1');
});


function ajaxSubmit($form,data){
    if (typeof data === 'undefined')
        data = $form.serialize();
    $('#list').animate({'opacity':0},500);
    $('#filters').animate({'opacity':0},500);
    let raw_url = window.location.href.split('?')[0];
    let full_url = raw_url;
    $.ajax({
        method: "POST",
        url: $form.attr('action'),
        data: data,
        dataType: 'json'
    })
    .done(function( ret ) {
        if (ret.success){
            $('#list').html(ret.data.list).animate({'opacity':1},100);
            $('#filters').html(ret.data.filters).animate({'opacity':1},100);
            let data_string = removeURLParameter(data,'_ajax');
            data_string = removeURLParameter(data_string,'rusticity_v');
            if (data_string){
                full_url += '?' + data_string;
            }
            if (typeof embed == 'undefined'){
                window.history.pushState({data: data_string}, '',  full_url);
            }else{
                window.history.pushState({data: data_string}, '',  full_url);
                let myPushState = new CustomEvent("mypushstate", {detail:{state: data_string}});
                window.dispatchEvent(myPushState);
            }
        }
    });
}

function removeURLParameter(query, parameter) {
    var prefix = encodeURIComponent(parameter) + '=';
    var pars = query.split(/[&;]/g);
    //reverse iteration as may be destructive
    for (var i = pars.length; i-- > 0;) {
        //idiom for string.startsWith
        if (pars[i].lastIndexOf(prefix, 0) !== -1) {
            pars.splice(i, 1);
        }
    }
    return pars.join('&');
}