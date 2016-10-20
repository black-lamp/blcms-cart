$(document).ready(function () {

    var postOfficeField = $('#order-delivery_post_office');


    $("#nova-poshta").change(function () {
        var selectedValue = $("#useraddress-postoffice").val();
        $(postOfficeField).val(selectedValue);
    });


    // var novaPoshtaButton = $('#delivery-methods input[value="2"]');
    //
    // if (!novaPoshtaButton.checked) {
    //     $('#delivery_post_office').parent().hide();
    // }
    //
    // var novaPoshtaSelecting = $('#nova-poshta');
    //
    // novaPoshtaButton.change(function () {
    //     if(this.checked){
    //         $(novaPoshtaSelecting).show();
    //     }
    //     else{
    //         $(novaPoshtaSelecting).hide();
    //     }
    // });
    

    
    // radioButtons.each(function() {
    //     if ($(this).attr('value') == 1) {
    //         if (this.checked) {
    //             $('#delivery_post_office').parent().hide();
    //         }
    //     }
    //     if ($(this).attr('value') == 1) {
    //         if (this.checked) {
    //             $('#delivery_post_office').parent().hide();
    //         }
    //     }
    //
    // });

});