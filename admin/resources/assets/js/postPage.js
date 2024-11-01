/* New JS Code Standard */

jQuery(document).ready(function($){



    $('.attach_calculator').on('click', function (e) {


        var availableCalculators = JSON.parse($('#availableCalculators').val());

        var productId = $('#productId').val();
        var simulatorId = $('#calculator').val();

        //check if the user has selected a calculator
        if (simulatorId == '') {
            //do nothing
        }else {
            var productsIds = JSON.parse(availableCalculators[simulatorId].products);
            attachSimulator(productId, simulatorId, productsIds);
        }


    });

    $('.remove_calculator').on('click', function (e) {

        var productIdToRemove = $('#productId').val();
        removeSimulator(productIdToRemove);

    });


});


/**
 * param productId, the id of the product that the user is attaching a calculator
 * param simulatorId, the calculator id to be assigned to the product
 * param productsIds, the list of products id of the new calculator
 *
 * return void
 * */
function attachSimulator(productId, simulatorId, productsIds) {

    requestAjaxCalculatePrice = $.ajax({
        method: "POST",
        async: this.asyncAjaxCalculatePrice,
        url: $('#ajaxUrl').val()+ "?action=awspricecalculator_ajax_attach_calculator" + "&id=" + productId + "&simulatorid=" + simulatorId ,
        dataType: 'json',
        data: {'selectedCalculatorProducts': productsIds},

        success: function(result, status, xhrRequest) {

            //pass the available calculators to the hidden field
            $('#availableCalculators').val(JSON.stringify(result));

            //dynamically change the name of the assigned calculator
            $('#selected_calculator').text(result[simulatorId].name);

            //show the remove calculator button
            $('.remove_calculator').show();


        },
        error: function(xhrRequest, status, errorMessage)  {

        }
    });


}

/**
 * param productId, the id of the product that the user removing the calculator
 * return void
 * */
function removeSimulator(productId) {

    requestAjaxCalculatePrice = $.ajax({
        method: "POST",
        async: this.asyncAjaxCalculatePrice,
        url: $('#ajaxUrl').val()+ "?action=awspricecalculator_ajax_remove_calculator" + "&id=" + productId ,
        dataType: 'json',
        data: {},

        success: function(result, status, xhrRequest) {

            //remove the calculator name from the selected one place
            $('#selected_calculator').text('');

            //hide the remove calculator button
            $('.remove_calculator').hide();

            //pass the available calculators to the hidden field
            $('#availableCalculators').val(JSON.stringify(result));

        },
        error: function(xhrRequest, status, errorMessage)  {

        }
    });


}
