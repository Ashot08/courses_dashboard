jQuery(document).ready(function(){
    jQuery(document).on('click', '.cd__modal_toggler', function (){
        jQuery('.cd__modal_wrapper').slideToggle();
    })
})

jQuery(document).ready(function(){
    jQuery(document).on('click', '[data-action="cd__copy_key_to_clipboard"]', function(){
        const resultBlock = jQuery(this).parent().find('.cd__key_copy_result');
        jQuery('.cd__key_copy_result').html('');
        navigator.clipboard.writeText(jQuery(this).data('text')).then(
            function() {
                resultBlock.html('Код успешно скопирован в буфер обмена');
                /* clipboard successfully set */

            },
            function() {
                /* clipboard write failed */
                window.alert('Opps! Your browser does not support the Clipboard API')
            }
        )
    })
})

