/*This script gets delivery method info used CartController by Blcms-Shop module*/

$(document).ready(function () {

    /*CHECKED ELEMENT SETTINGS*/
    var radioButtons = $('#delivery-methods input[type="radio"]');
    $(radioButtons[0]).attr("checked", "checked");
    getElementInfo($(radioButtons[0]).val());

    /*SELECTED ELEMENT SETTINGS*/
    var inputs = $('#delivery-methods input[type="radio"]');
    inputs.change(function () {
        if (this.checked) {
            var elementValue = this.value;
            getElementInfo(elementValue);
        }
    });
});

/*GETTING METHOD INFO BY IT VALUE*/
function getElementInfo(elementValue) {
    $.ajax({
        type: "GET",
        url: '/cart/cart/get-delivery-method',
        data: 'id=' + elementValue,

        success: function (data) {
            var method = $.parseJSON(data).method;

            /*METHOD LOGO SETTING*/
            var methodLogo = $('#delivery-logo');
            $(methodLogo).attr('src', method.image_name);

            /*METHOD TITLE SETTING*/
            var methodTitle = $('#delivery-title');
            $(methodTitle).text(method.translations[1]['title']);

            /*METHOD DESCRIPTION SETTING*/
            var methodDescription = $('#delivery-description');
            $(methodDescription).html(method.translations[1]['description']);

            var postOfficeField = $('.post-office');
            var addressFields = $('div.address');

            switch (method.show_address_or_post_office) {
                case '0' :
                    postOfficeField.hide();
                    addressFields.hide();
                    break;
                case '1' :
                    postOfficeField.hide();
                    addressFields.show();
                    break;
                case '2' :
                    postOfficeField.show();
                    addressFields.hide();
            }
        }
    });
}