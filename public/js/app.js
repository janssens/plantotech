jQuery(function ($){
    $('.tag > .close').click(function (e){
        e.preventDefault();
        let $tag = $(this).closest('.tag');
        let k = $tag.data('key');
        let v = $tag.data('value');
        // console.log(k);
        // console.log(v);
        $('[name^="'+k+'"] option[data-f-value="'+v+'"]').removeAttr("selected"); // in all form
        $('input[name^="'+k+'"][data-f-value="'+v+'"]').removeAttr("checked"); // in all form
        $('#form_filter form').submit(); //submit main
    })
    $('#filters_list a').click(function (e){
       let key = $(this).attr('href').substr(1);
       $('.fblock.'+key).show();
       $('.fblock:not(.'+key+')').hide();
       $('#form_filter [type=submit]').show();
    });
    // $('.fblock').each(function (){
    //     let counter = $(this).find(':selected').length;
    //     console.log($(this).attr('id')+':'+counter);
    // });
});