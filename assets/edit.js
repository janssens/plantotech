import 'dropzone';
import $ from 'jquery';

$('.editable')
    .on('click','[data-type="attribute"].show',function(){
        var $that = $(this);
        let url = attribute_edit_url_template.replace('__attribute_id__',$that.data('id'));
        $.ajax({
            method: "GET",
            url: url
        }).done(function( ret ) {
            $that.replaceWith(ret);
        });
    })
    .on('click','[data-type="property"].show',function(){
        var $that = $(this);
        let url = property_edit_url_template.replace('__property_id__',$that.data('id'));
        $.ajax({
            method: "GET",
            url: url
        }).done(function( ret ) {
            $that.html(ret).removeClass('show')
                .find('.dropzone').dropzone({});
        });
    });

function attributeLineEditSubmit(form){
    $.ajax({
        method: "POST",
        url: $(form).attr('action'),
        data: $(form).serialize()
    }).done(function( ret ) {
        $(form).parent().replaceWith(ret);
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
        $(form).parent().html(ret).addClass('show');
    });
    return false;
}
function sourceLineSubmit(form){
    $.ajax({
        method: "POST",
        url: $(form).attr('action'),
        data: $(form).serialize()
    }).done(function( ret ) {
        $(form).closest('[data-type="property"]').html(ret).addClass('show');
    });
    return false;
}
function newImageUrl(that){
    let $ul = $(that).parent('form').find('ul');
    $ul.find('li:first').clone().appendTo($ul).value('');
}