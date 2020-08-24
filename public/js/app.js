jQuery(function ($){
    $('.tag > .close').click(function (e){
        e.preventDefault();
        let $tag = $(this).closest('.tag');
        let k = $tag.data('key');
        let v = $tag.data('value');
        // console.log(k);
        // console.log(v);
        $('#filter_'+k+' option[data-f-value='+v+']').removeAttr("selected");
        $('#filters_form').submit();
    })
});