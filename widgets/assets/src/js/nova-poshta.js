$(document).ready(function () {

    var novaPoshtaButton = $('#delivery-methods input[value="2"]');

    if (!novaPoshtaButton.checked) {
        $('#delivery_post_office').parent().hide();
    }

    novaPoshtaButton.change(function () {
        if(this.checked){
            $('#delivery_post_office').parent().show();
        }
        else{
            $('#delivery_post_office').parent().hide();
        }
    });
    

    
    radioButtons.each(function() {
        if ($(this).attr('value') == 1) {
            if (this.checked) {
                $('#delivery_post_office').parent().hide();
            }
        }
        if ($(this).attr('value') == 1) {
            if (this.checked) {
                $('#delivery_post_office').parent().hide();
            }
        }

    });

});