/**
 * Created by Gutsulyak Vadim <guts.vadim@gmail.com> on 11.05.2017
 */
$(function() {

    function formChangeHandle() {
        $.get('sum', $('#order-form').serialize(), function(data) {
            $('#costContainer').html(data);
        });
    }

    $('#order-form').change(formChangeHandle);
    $('#order-form input').keyup(formChangeHandle);
    $('#order-form input').on('paste', formChangeHandle);
});