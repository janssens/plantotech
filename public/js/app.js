jQuery(function ($){
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
});

function ajaxSubmit($form){
    let data = $form.serialize();
    $('#list').animate({'opacity':0},500);
    $('#filters').animate({'opacity':0},500);
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
        }
    });
}