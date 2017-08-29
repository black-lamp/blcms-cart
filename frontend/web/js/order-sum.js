/**
 * Created by Gutsulyak Vadim <guts.vadim@gmail.com> on 11.05.2017
 */
$(function() {

    function formChangeHandle() {
        var prefix = '';
        if(window.currentLang && currentLang === 'uk-ua') {
            prefix = '/uk-ua'
        }

        $.get(prefix + '/cart/order/sum', $('#order-form').serialize(), function(data) {
            $('#costContainer').html(data);
        });
    }

    $('#order-form').change(formChangeHandle);
    $('#order-form input').keyup(formChangeHandle);
    $('#order-form input').on('paste', formChangeHandle);
});