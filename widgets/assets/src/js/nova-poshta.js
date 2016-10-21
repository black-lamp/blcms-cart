$(document).ready(function () {

    var novaPoshtaWidget = $("#nova-poshta");

    // $(novaPoshtaWidget).change(function () {
    //     var selectedArea = $('#nova-poshta-area').val();
    //
    // });

    var postOfficeField = $('#order-delivery_post_office');

    $(novaPoshtaWidget).change(function () {
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