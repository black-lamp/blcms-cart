$(document).ready(function () {

    var inputs = $('#delivery-methods input[type="radio"]');

    inputs.change(function() {
        if(this.checked) {

            var element = this;
            $.ajax({
                type: "GET",
                url: '/shop/cart/get-delivery-method',
                data: 'id=' + this.value,

                success: function (data) {
                    var method = $.parseJSON(data).method;

                    var div = document.createElement('div');
                    $(div).addClass('col-md-8 delivery-info');
                    var img = document.createElement('img');
                    $(img).attr('src', method.image_name);
                    $(img).addClass('delivery-logo');
                    var field = data.field;



                    div.appendChild(img);

                    var ul = $(element).parent().parent().parent();

                    var oldDeliveryInfo = $('.delivery-info');
                    oldDeliveryInfo.remove();
                    $(div).insertAfter(ul);


                }
            });

        }
    });

});