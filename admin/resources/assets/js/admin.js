/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 **/

/* New JS Code Standard */
jQuery(document).ready(function($){

    WooPriceCalculator = {
        init: function(){
            WooPriceCalculator.initUtilities();
            WooPriceCalculator.initMediaManager();
            WooPriceCalculator.initCodeMirror();
        },
        
        initCodeMirror: function(){
            /*
            var textAfterFieldCodeMirror = CodeMirror.fromTextArea(document.getElementById("text_after_field"), {
                lineNumbers : true,
                mode: 'htmlmixed',
            });
            
            textAfterFieldCodeMirror.setSize(400, 100);
            */
        },
        
        initUtilities: function(){
          $("button[data-href]").click(function(){
                var dataHref      = $(this).attr('data-href');
                window.location = dataHref;
          });
          
          $('.chosen-select').chosen({
                width: "100%",
                allow_single_deselect: true
          });
        },
        
        /* Init the Query Builder for various purposes */
        setQueryBuilder: function (selector){
            if($(selector).length){
                $(selector).queryBuilder({
                    plugins: [
                        'sortable',
                    ],

                    sortable: true,
                    allow_empty: true,

                    filters: JSON.parse($(selector).attr('data-filters')),

                    readonly_behavior: {
                        sortable: false
                    }
                });
            }
        },
        
        implodeElements: function(elementsSelector){
            return $(elementsSelector).map(function() {
                var value   = $(this).html();

                var regExp = /.* \[([^)]+)\]/;
                var matches = regExp.exec(value);

                if(matches != null){
                    var fieldId = matches[1].replace('aws_price_calc_', '');
                }

                return fieldId;
            }).get().join(',');
        },
        
        queryBuilderSetRules: function(queryBuilderSelector, StringRules){
            if(StringRules && StringRules != null && StringRules != '' && StringRules != 'null'){
                $(queryBuilderSelector).queryBuilder('setRules', JSON.parse(StringRules));
            }else{
                $(queryBuilderSelector).queryBuilder('reset');
            }

            $('.rule-filter-container > select').chosen({
                width: "200px",
                allow_single_deselect: true
            });

            $('.rule-filter-container > select').trigger("chosen:updated");
            $('.rule-value-container > select').trigger("chosen:updated");

        },
        
        /* Init the Media Manager Library of Wordpress */
        initMediaManager: function(){
            $('.awspc_media_manager').click(function(e) {
                var imagePreviewSelector     = $(this).attr('data-image-preview-selector');
                var imageSelector            = $(this).attr('data-image-selector');
                var imageEmpty               = $(this).attr('data-empty-img');

                e.preventDefault();
                var image_frame;
                if(image_frame){
                    image_frame.dispose();
                }

                // Define image_frame as wp.media object
                image_frame = wp.media({
                    title: 'Select Media',
                    multiple : false,
                    library : {
                        type : 'image',
                    }
                });

                /* On Image Library open */
                image_frame.on('open', function() {
                    // On open, get the id from the hidden input
                    // and select the appropiate images in the media manager
                    var selection     =  image_frame.state().get('selection');
                    ids               = $(imageSelector).val().split(',');

                    ids.forEach(function(id) {
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add( attachment ? [ attachment ] : [] );
                    });

                });

                /* On Image Library close */
                image_frame.on('close', function() {
                    // On close, get selections and save to the hidden input
                    // plus other AJAX stuff to refresh the image preview
                    var selection       =  image_frame.state().get('selection');

                    selection.each(function(attachment) {
                        //console.log(attachment);

                        if(attachment['attributes'].url){
                            $(imagePreviewSelector).attr('src', attachment['attributes'].url);
                            $(imageSelector).val(attachment['attributes'].url);
                        }else{
                            $(imagePreviewSelector).attr('src', imageEmpty);
                            $(imageSelector).val("");
                        }

                    });

                });

                image_frame.open();
            });
        }
    };

    WooPriceCalculator.init();

});

/* Old JS Code Standard */
jQuery(document).ready(function($){
    var selected_cell           = null;
    var sortableEditElement     = null;

    var sortableItems           = null;
    var sortableItemsData       = null;
    var language                = $('html').attr('lang');

    /*
     * Solo se la lingua esiste
    */
    if(language == "it-IT"){
        $('.wsf-bs .data-table').DataTable({
            "language": {
                "url": WPC_HANDLE_SCRIPT.siteurl + "/wp-content/plugins/woo-price-calculator/lib/DataTables-1.10.12/lang/" + language + ".json"
            }
        });
    }else{
        $('.wsf-bs .data-table').DataTable();
    }

    $('#awspricecalculator_select_products_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": WPC_HANDLE_SCRIPT.ajax_products_url,
    });

    $(document).on('click', '#awspricecalculator_select_products_table .select-product', function(){
        var productId           = $(this).attr('data-product-id');
        var productName         = $(this).attr('data-product-name');
        var remodalInst         = $('[data-remodal-id="select-products-modal"]').remodal();

        $('#products_select').append("<option value=\"" + productId + "\">" + productName + "</option>");

        remodalInst.close();
    });

    $('#products_select option').dblclick(function() {
        $("#products_select option[value='" + $(this).val() + "']").remove();
    });


    $('.wsf-bs-tooltip').tooltip();

    $(".excel-container .worksheet").mousedown( function(e) {
       mouseX = e.pageX; 
       mouseY = e.pageY;
    });

    /* Visualizzazione video delle feature del pro */
    $(document).on('opening', '.awspc-upgrade-modal', function () {
        $(this).find("video").get(0).pause();
        $(this).find("video").get(0).currentTime = '0';
        $(this).find("video").get(0).play();
    });

    $('.woo-price-calculator-tooltip').tooltipster({
        animation: 'fade',
        contentAsHTML: true,
        multiple: true,
        theme: 'tooltipster-shadow',
        touchDevices: true
    });

    $('.wpc-multiselect').multiSelect({ keepOrder: true });

    /* 
     * Durante il reupload del file Excel avviso il cliente che il file attuale
     * sarà sovrascritto
     */
    $("#awspc_upload_spreadsheet").submit(function(){
        /* Controllo se si tratta di un re-upload */
        if(!$("#awspc_upload_spreadsheet").attr('data-calculator-id').length == 0){
            var confirmResult = confirm("This action will overwrite the current spreadsheet. Do you want to continue?");

            return confirmResult;
        }

        /* Non si tratta di un reupload ma di un upload */
        return true;
    });


    $('#calculator_submit').click(function(){

        $('#field_orders').val(WooPriceCalculator.implodeElements('#field_container .ms-elem-selection.ms-selected span'));
        $('#output_field_orders').val(WooPriceCalculator.implodeElements('#output_field_container .ms-elem-selection.ms-selected span'));
        
        /* Seleziono tutti i prodotti per poterli salvare (trucco JS) */
        $('#products_select option').prop('selected', true);

        $('#calculator_form').submit();
    });

    $('#wpc_load_mapping_button').click(function(){
        if($('#output_field_price').length){
            $('#cell_next_form').submit();
        }else{
            alert('You need to select the output price field');
        }


    });

    $('#field_regex_modal_ok').click(function(){
        $("#text_regex").val($("#field_regex_modal_list").val());

        $("#field_regex_modal").modal('hide');
    });

    wpcInitCellMapping();
    wpcInitFields();
    wpcInitCalculator();
    wpcInitCalculatorConditionalLogic();
    /* wpcInitCalculatorProductImageLogic(); */

    /* Inizializzazione delle liste ordinabili (Campi Picklist e Radio) */
    wpcSortableElement("#picklist_items_sortable", "#picklist_items", "#items_list_id");
    wpcSortableElement("#imagelist_items_sortable", "#imagelist_items", "#items_list_id");
    wpcSortableElement("#radio_items_sortable", "#radio_items", "#items_list_id");
    wpcSortableEvents("#items_list_id");
    wpcUpdateSortableData("#picklist_items_sortable", "#picklist_items", "#items_list_id");
    wpcUpdateSortableData("#radio_items_sortable", "#radio_items", "#items_list_id");
    wpcUpdateSortableData("#imagelist_items_sortable", "#imagelist_items", "#items_list_id");

    function wpcUpdateSortableData(selector, dataSelector, idSelector){
        $(dataSelector).val("[]");

        $(selector + ' li').each(function(i){
            var id                  = $(this).attr('data-id');
            var label               = $(this).attr('data-label');
            var value               = $(this).attr('data-value');
            var order_details       = $(this).attr('data-order-details');
            var tooltip_message     = $(this).attr('data-tooltip-message');
            var tooltip_position    = $(this).attr('data-tooltip-position');
            var default_option      = $(this).attr('data-default-option');

            var image               = $(this).attr('data-image');

            if(value == '' || value == undefined){
                value = label;
            }

            var data    = JSON.parse($(dataSelector).val());

            data.push({
                "id": id,
                "label": label,
                "value": value,
                "order_details" : order_details,
                "tooltip_message": tooltip_message,
                "tooltip_position": tooltip_position,
                "default_option": default_option,

                "image": image
            });

            $(dataSelector).val(JSON.stringify(data));
        });
    }

    function wpcSortableEvents(idSelector){
        $(document).on('click', '#field_list_modal_ok', function(){
            var id                  = $("#field_list_modal_id").val();

            var fieldType           = $("#field_type").val();
            var label               = $("#field_list_modal_label").val();
            var value               = $("#field_list_modal_value").val();
            var order_details       = $("#field_list_modal_order_details").val();
            var tooltip_message     = $("#field_list_modal_tooltip_message").val();
            var tooltip_position    = $("#field_list_modal_tooltip_position").val();
            var default_option      = $("#field_list_modal_default_option").val();

            /* Image List */
            var imageListImage      = $("#field_list_modal_image").val();

            if(value == '' || value == undefined){
                value = 0;
            }

            /* Label is mandatory */
            if(label == ''){
                alert('Error: Label can\'t be null');
                return;
            }

            /* If it's a Image List, then Image is mandatory */
            if(fieldType == "imagelist" && imageListImage == ''){
                alert('Error: Image is mandatory for Image List field');
                return;
            }

            /* Adding new Item */
            if($("#field_list_mode").val() == "add"){
                var el = document.createElement('li');

                $(el).attr('data-id', $(idSelector).val());

                $(el).attr('data-value', value);
                $(el).attr('data-label', label);
                $(el).attr('data-order-details', order_details);
                $(el).attr('data-tooltip-message', tooltip_message);
                $(el).attr('data-tooltip-position', tooltip_position);

                $(el).attr('data-image', imageListImage);

                if(default_option == 1){
                    $(sortableItems + ' li').each(function(i){
                        $(this).attr('data-default-option', '0');
                    });
                }

                $(el).attr('data-default-option', default_option);

                if(fieldType == "imagelist" || (fieldType == "radio" && imageListImage.length != 0)){
                    var imageHtml      = ' <img class="sortable-item-image" src="' + imageListImage + '" />';
                }else{
                    var imageHtml      = '';
                }

                el.innerHTML = '<a class="btn btn-danger js-remove" data-sortable-items="' + sortableItems + '" data-sortable-items-data="' + sortableItemsData + '">' +
                    '<i class="fa fa-times"></i>' +
                    '</a> ' +
                    '<a class="btn btn-primary sortable-edit" data-sortable-items="' + sortableItems + '" data-sortable-items-data="' + sortableItemsData + '">' +
                    '<i class="fa fa-pencil"></i>' +
                    '</a>' + imageHtml + ' ' + label + ' <i>[Value: ' + value + ']</i>';

                $(sortableItems).append(el);

                $(idSelector).val(parseInt($(idSelector).val())+1);

            }

            /* Editing a Item */
            if($("#field_list_mode").val() == "edit"){

                $(sortableEditElement).attr('data-id', id);

                $(sortableEditElement).attr('data-value', value);
                $(sortableEditElement).attr('data-label', label);
                $(sortableEditElement).attr('data-order-details', order_details);
                $(sortableEditElement).attr('data-tooltip-message', tooltip_message);
                $(sortableEditElement).attr('data-tooltip-position', tooltip_position);

                $(sortableEditElement).attr('data-image', imageListImage);

                if(default_option == 1){
                    $(sortableItems + ' li').each(function(i){
                        $(this).attr('data-default-option', '0');
                    });
                }
                $(sortableEditElement).attr('data-default-option', default_option);

                if(fieldType == "imagelist" || (fieldType == "radio" && imageListImage.length != 0)){
                    var imageHtml      = ' <img class="sortable-item-image" src="' + imageListImage + '" />';
                }else{
                    var imageHtml      = '';
                }

                $(sortableEditElement).html('<a class="btn btn-danger js-remove" data-sortable-items="' + sortableItems + '" data-sortable-items-data="' + sortableItemsData + '">' +
                    '<i class="fa fa-times"></i>' +
                    '</a> ' +
                    '<a class="btn btn-primary sortable-edit" data-sortable-items="' + sortableItems + '" data-sortable-items-data="' + sortableItemsData + '">' +
                    '<i class="fa fa-pencil"></i>' +
                    '</a>' + imageHtml + ' ' + label + ' <i>[Value: ' + value + ']</i>');

            }

            wpcUpdateSortableData(sortableItems, sortableItemsData);

            $('#field_list_modal').modal('hide');

        });
    }


    function wpcSortableElement(selector, dataSelector, idSelector){

        if($(selector).get(0) != undefined){
            var sortableList = Sortable.create($(selector).get(0), {
                animation: 150,
                filter: '.js-remove',

                onFilter: function (evt){
                    evt.item.parentNode.removeChild(evt.item);
                    wpcUpdateSortableData(selector, dataSelector, idSelector);
                },

                onAdd: function (evt) {
                    wpcUpdateSortableData(selector, dataSelector, idSelector);
                },

                onEnd: function(evt){
                    wpcUpdateSortableData(selector, dataSelector, idSelector);
                },

            });

            $('.field_list_add').click(function(){

                $("#field_list_mode").val("add");

                sortableItems       = $(this).attr("data-sortable-items");
                sortableItemsData   = $(this).attr("data-sortable-items-data");

                $("#field_list_modal_id").val("");
                $("#field_list_modal_label").val("");
                $("#field_list_modal_value").val("");
                $("#field_list_modal_order_details").val("");
                $("#field_list_modal_tooltip_message").val("");
                $("#field_list_modal_tooltip_position").val("none");
                $("#field_list_modal_default_option").val(0);

                /* Image List */
                $("#field_list_modal_image").val("");
                //$("#field_list_modal_image_preview").attr('src', imageListImage);
                $("#field_list_modal_image_preview").imageSelector("clear");

                showFieldListModal();
            });

            $(document).on('click', selector + " .sortable-edit", function(){
                $("#field_list_mode").val("edit");

                sortableItems       = $(this).attr("data-sortable-items");
                sortableItemsData   = $(this).attr("data-sortable-items-data");

                var element         = $(this).parent();
                sortableEditElement = element;

                var id                  = $(element).attr('data-id');
                var label               = $(element).attr('data-label');
                var value               = $(element).attr('data-value');
                var order_details       = $(element).attr('data-order-details');
                var tooltip_message     = $(element).attr('data-tooltip-message');
                var tooltip_position    = $(element).attr('data-tooltip-position');
                var default_option      = $(element).attr('data-default-option');

                /* Image List */
                var imageListImage      = $(element).attr('data-image');
                var imageEmpty          = $("#field_list_modal_image_preview").attr('data-empty-img');

                if(value == '' || value == undefined){
                    value = label;
                }

                $("#field_list_modal_id").val(id);

                $("#field_list_modal_label").val(label);
                $("#field_list_modal_value").val(value);
                $("#field_list_modal_order_details").val(order_details);
                $("#field_list_modal_tooltip_message").val(tooltip_message);
                $("#field_list_modal_tooltip_position").val(tooltip_position);
                $("#field_list_modal_default_option").val(default_option);

                /* Image List */
                if(imageListImage){
                    $("#field_list_modal_image").val(imageListImage);
                    $("#field_list_modal_image_preview").attr('src', imageListImage);
                }else{
                    $("#field_list_modal_image").val("");
                    $("#field_list_modal_image_preview").attr('src', imageEmpty);
                }

                showFieldListModal();
            });
        }

    }

    function showFieldListModal(){
        var fieldType   = $('#field_type').val();

        $(".modal-radio-elements").hide();
        $(".modal-image-block").hide();

        if(fieldType == "radio"){
            $(".modal-image-block label").removeClass("required");
            $(".modal-image-block").show();
            $(".modal-radio-elements").show();
        }else if(fieldType == "imagelist"){
            $(".modal-image-block label").addClass("required");
            $(".modal-image-block").show();
        }

        $('#field_list_modal').modal('show');
    }

    function wpcInitCellMapping(){

        $(document).ready(function(){

            if($("[data-type='price']").length){
                $("#cell_type_price_div").hide();
            }

            $("[data-type='input']").each(function(index, element){
                var fieldId                  = $(element).attr("data-field-id");
                var listInputFieldsElement   = $("#cell_type_select_input option[value='" + fieldId + "']");
            });

            $("[data-type='output']").each(function(index, element){
                var fieldId                  = $(element).attr("data-field-id");
                var listInputFieldsElement   = $("#cell_type_select_output option[value='" + fieldId + "']");
            });
            
            $("[data-type='error']").each(function(index, element){
                var fieldId                  = $(element).attr("data-field-id");
                var listInputFieldsElement   = $("#cell_type_select_error option[value='" + fieldId + "']");
            });
            
        });

        $('.cell').click(function(){

            if($(this).hasClass('disabled') == false){
                $('.cell').removeClass('cell_not_selected');
                $('.cell').removeClass('cell_selected');

                if($(this).hasClass('cell_type_selected')){
                    $("#cell_type_none_div").show();
                }else{
                    $("#cell_type_none_div").hide();
                    $(this).addClass('cell_selected');
                }

                selected_cell = $(this);

                $('#cell_type').css({
                    'top': (mouseY) + 'px',
                    'left': (mouseX) + 'px'
                }).appendTo('body');

                /*
                 * Se non è possibile nessuna opzione nascondo il form e mostro
                 * un messaggio; altrimenti mostro il form e nascondo il messaggio
                 */
                if($("#cell_type_none_div").css('display') == 'none' &&
                    $("#cell_type_input_div").css('display') == 'none' &&
                    $("#cell_type_output_div").css('display') == 'none' &&
                    $("#cell_type_error_div").css('display') == 'none'){

                    $("#cell_type_form").hide();
                    $("#cell_type_no_content").show();

                }else{
                    $("#cell_type_form").show();
                    $("#cell_type_no_content").hide();
                }
                //alert($('#cell_type').height()/2);

                if(selected_cell.attr('data-type') == "input"){
                    $('#cell_type_select_input').val(selected_cell.attr('data-field-id'));
                    $("#cell_type_input").prop("checked", true);
                }else if(selected_cell.attr('data-type') == "output"){
                    $('#cell_type_select_output').val(selected_cell.attr('data-field-id'));
                    $("#cell_type_output").prop("checked", true);
                }else if(selected_cell.attr('data-type') == "price"){
                    $("#cell_type_price").prop("checked", true);
                }else if(selected_cell.attr('data-type') == "error"){
                    $('#cell_type_select_error').val(selected_cell.attr('data-field-id'));
                    $("#cell_type_error").prop("checked", true);
                }else{
                    $("#cell_type_none").prop("checked", true);
                }

                $('#cell_type').show();
            }

        });

        $('#cell_type_submit').click(function(){
            $('#cell_type').hide();

            selected_mode                = $('input[name=cell_type_mode]:checked', '#cell_type_form').val();
            coordinates                  = $(selected_cell).attr('data-coordinates');

            if(selected_mode == "none"){
                field_id                     = $(selected_cell).attr('data-field-id');

                if($(selected_cell).attr('data-type') == "output"){
                    cellResetOutput();
                }else if($(selected_cell).attr('data-type') == "input"){
                    cellResetInput(selected_cell);
                }else if($(selected_cell).attr('data-type') == "error"){
                    cellResetError(selected_cell);
                }else if($(selected_cell).attr('data-type') == "price"){
                    cellResetPrice();
                }

                /* Cancello i campi data e tolgo la selezione */
                $(selected_cell).attr('data-type', "");
                $(selected_cell).attr('data-field-id', "");
                $(selected_cell).removeClass('cell_type_selected');

                /* Cancello anche l'hidden */
                $("#cell_next_form input[value='" + coordinates + "']").remove();

            }else if(
                    selected_mode == "input" || 
                    selected_mode == "output" ||
                    selected_mode == "error"){
                
                $(selected_cell).addClass('cell_type_selected');

                field_id                   = $('#cell_type_select_' + selected_mode).val();
                var listInputFieldsElement = $("#cell_type_select_" + selected_mode + " option[value='" + field_id + "']");

                /* Cancello campi con stesse coordinate */
                $(".input_mapping_fields").each(function(index, element){
                    if(coordinates == $(element).val()){
                        $(element).remove();
                    }
                });
                
                $(".output_mapping_fields").each(function(index, element){
                    if(coordinates == $(element).val()){
                        $(element).remove();
                    }
                });
                
                $(".error_mapping_fields").each(function(index, element){
                    if(coordinates == $(element).val()){
                        $(element).remove();
                    }
                });
                
                /* Aggiungo il campo coordinate */
                if(selected_mode == "input"){
                    $("#cell_next_form").append('<input class="input_mapping_fields" type="hidden" id="input_field_' + field_id + '[]" name="input_field_' + field_id + '[]" value="' + coordinates + '" />');
                }else if(selected_mode == "error"){
                    $("#cell_next_form").append('<input class="error_mapping_fields" type="hidden" id="error_field_' + field_id + '[]" name="error_field_' + field_id + '[]" value="' + coordinates + '" />');
                }else if(selected_mode == "output"){
                    if ($('#output_field_' + field_id).length) {
                        $('#output_field_' + field_id).val(coordinates);

                        /* Tolgo la selezione se questo campo di output è stato selezionato in precedenza */
                        var oldCell     = $('.cell[data-type="output"][data-field-id="' + field_id + '"]')
                        $(oldCell).removeClass('cell_type_selected');
                        $(oldCell).attr('data-type', '');
                        $(oldCell).attr('data-field-id', '');

                    }else{
                        $("#cell_next_form").append('<input class="output_mapping_fields" type="hidden" id="output_field_' + field_id + '" name="output_field_' + field_id + '" value="' + coordinates + '" />');
                    }
                }

                if($(selected_cell).attr('data-type') == "output"){
                    cellResetOutput(selected_cell);
                }else if($(selected_cell).attr('data-type') == "price"){
                    cellResetPrice();
                }

                /* Memorizzo nella cella l'ID del campo */
                $(selected_cell).attr('data-type', selected_mode);
                $(selected_cell).attr('data-field-id', field_id);

            }else if(selected_mode == "price"){

                $(selected_cell).addClass('cell_type_selected');

                $("#cell_type_price_div").hide();
                $("#cell_next_form").append('<input type="hidden" id="price" name="price" value="' + coordinates + '" />');

                if($(selected_cell).attr('data-type') == "input"){
                    cellResetInput(selected_cell);
                }else if($(selected_cell).attr('data-type') == "output"){
                    cellResetOutput(selected_cell);
                }

                $(selected_cell).attr('data-type', "price");

            }

            /* Nascondo la selezione del campo di input se non ci sono più campi
             * da selezionare */
            if($('#cell_type_select_input option').size() <= 0){
                $('#cell_type_input_div').hide();
            }else{
                $('#cell_type_input_div').show();
            }

            if($('#cell_type_select_error option').size() <= 0){
                $('#cell_type_error_div').hide();
            }else{
                $('#cell_type_error_div').show();
            }
            
            if($('#cell_type_select_output option').size() <= 0){
                $('#cell_type_output_div').hide();
            }else{
                $('#cell_type_output_div').show();
            }

            $("#cell_type_input").prop("checked", true);
            $("#cell_type_error").prop("checked", true);
            $("#cell_type_output").prop("checked", true);

            //alert($("#cell_next_form").html());
        });
    }

    function cellResetError(selected_cell){
        var field_id                     = $(selected_cell).attr('data-field-id');

        $("#cell_type_error_div").show();

        $("#error_field_" + field_id).remove();
    }
    
    function cellResetInput(selected_cell){
        var field_id                     = $(selected_cell).attr('data-field-id');

        $("#cell_type_input_div").show();

        $("#input_field_" + field_id).remove();
    }

    function cellResetOutput(selected_cell){
        var field_id                     = $(selected_cell).attr('data-field-id');

        $("#cell_type_output_div").show();

        $("#output_field_" + field_id).remove();

    }

    function cellResetPrice(){
        $("#price").remove();
        $("#cell_type_price_div").show();

    }

    function awsShowFieldMode(value){
        jQuery("#input_fields_options").hide();
        jQuery("#output_fields_options").hide();

        if(value == "output"){
            jQuery("#output_fields_options").show();
        }else if(value == "input"){
            jQuery("#input_fields_options").show();
        }
    }

    function wpcInitFields(){
        jQuery(".wpc-numeric").numeric({decimal: false});

        jQuery(".wpc-numeric-decimals").numeric({
            decimal: ".",
        });

        wpcShowFieldPanel(jQuery('#field_type').val());

        jQuery('#field_type').change(function() {
            wpcShowFieldPanel(jQuery(this).val());

        });

        awsShowFieldMode(jQuery('#field_mode').val());

        jQuery('#field_mode').change(function(){
            awsShowFieldMode(jQuery(this).val());
        });

        /* Visualizzo il modal per aggiungere le voci */

        /*
        $('#wpc_options_radio_add').click(function(){
            window.wpcShowFieldChoiseMode = "add";
            wpcShowFieldChoiseModal("wpc_options_radio_list");
        });
        
        $('#wpc_options_radio_edit').click(function(){
            if($("#wpc_options_radio_list option:selected").length){
                window.wpcShowFieldChoiseMode = "edit";
                wpcShowFieldChoiseModal("wpc_options_radio_list");
            }else{
                alert("Error: Please select an item");
            }

        });
        
        $('#wpc_options_radio_remove').click(function(){
            $("#wpc_options_radio_list option:selected").remove();
        });
        
        $('#field_choise_modal_ok').click(function(){
            var value   = $("#field_choise_modal_value").val();
            var text    = $("#field_choise_modal_text").val();
            
            if(text == ""){
                alert("Error: Text is mandatory");
            }else{
            
                if(window.wpcShowFieldChoiseMode == "add"){
                    $('#wpc_options_radio_list').append($('<option>', {
                        value:  value,
                        text:   text
                    }));

                    $('#field_choise_modal').modal("hide"); 

                }else if(window.wpcShowFieldChoiseMode == "edit"){
                    $("#wpc_options_radio_list option:selected").text(text);
                    $("#wpc_options_radio_list option:selected").val(value);

                    $('#field_choise_modal').modal("hide"); 
                }
            }
        });
        */

        $("#wpc_field_form_submit").click(function(){
            //$("#wpc_options_radio_list option").prop('selected', true);
            $("#wpc_field_form").submit();
        });

    }

    function wpcShowFieldChoiseModal(listSelector){
        if(window.wpcShowFieldChoiseMode == "add"){
            $("#field_choise_modal_text").val("");
            $("#field_choise_modal_value").val("");
        }else if(window.wpcShowFieldChoiseMode == "edit"){
            $("#field_choise_modal_text").val($("#" + listSelector + " option:selected").text());
            $("#field_choise_modal_value").val($("#" + listSelector + " option:selected").val());
        }

        $('#field_choise_modal').modal("show");
    }

    function wpcShowFieldPanel(value){
        jQuery("#checkbox_options").hide();
        jQuery("#picklist_options").hide();
        jQuery("#imagelist_options").hide();
        jQuery("#numeric_options").hide();
        jQuery("#text_options").hide();
        jQuery("#radio_options").hide();
        jQuery("#format").hide();
        jQuery("#date_format").hide();
        jQuery("#time_format").hide();
        jQuery("#date_time_format").hide();

        if(value == "checkbox"){
            jQuery("#checkbox_options").show();
        }else if(value == "picklist"){
            jQuery("#picklist_options").show();
        }else if(value == "imagelist"){
            jQuery("#imagelist_options").show();
        }else if(value == "numeric"){
            jQuery("#numeric_options").show();
        }else if(value == "text"){
            jQuery("#text_options").show();
        }else if(value == "radio"){
            jQuery("#radio_options").show();
        }else if (value == "date"){
            jQuery("#format").show();
            jQuery("#date_format").show();
        }else if (value == "time"){
            jQuery("#format").show();
            jQuery("#time_format").show();
        }else if (value == "datetime"){
            jQuery("#format").show();
            jQuery("#date_time_format").show();
        }
    }

    function wpcInitCalculatorConditionalLogic(){

        $('.wpc-conditional-logic-multiselect').multiSelect({
            selectableHeader: "<div class='awspc-conditional-logic-multiselect-header'>Show Field</div>",
            selectionHeader: "<div class='awspc-conditional-logic-multiselect-header'>Hide Field</div>",
            keepOrder: true
        });

        if($('.query-builder').length){
            $('.query-builder').each(function( index ) {
                var fieldId             = $(this).attr('data-field-id');

                if(fieldId){
                    var fieldRulesJsonId    = '#field_rules_json_' + fieldId;
                    var fieldRulesSqlId     = '#field_rules_sql_' + fieldId;
                    var queryBuilderId      = '#query_builder_' + fieldId;

                    WooPriceCalculator.setQueryBuilder(queryBuilderId);

                    //alert($(fieldRulesJsonId).val());
                    //alert($(fieldRulesSqlId).val());

                    WooPriceCalculator.queryBuilderSetRules(queryBuilderId, $(fieldRulesJsonId).val());

                }
            });
        }

        $(document).on('click', '.edit-conditional-logic-rules', function(){
            var fieldId             = $(this).attr('data-field-id');
            var modalId             = '#conditionalLogicRuleModal_' + fieldId;
            var queryBuilderId      = '#query_builder_' + fieldId;

            $(modalId).modal('show');
        });

        $('.conditional-logic-modal-confirm').click(function(){
            var fieldId             = $(this).attr('data-field-id');
            var modalId             = '#conditionalLogicRuleModal_' + fieldId;
            var queryBuilderId      = '#query_builder_' + fieldId;
            var fieldRulesJsonId    = '#field_rules_json_' + fieldId;
            var fieldRulesSqlId     = '#field_rules_sql_' + fieldId;

            var rulesJson           = $(queryBuilderId).queryBuilder('getRules');
            var rulesSql            = $(queryBuilderId).queryBuilder('getSQL', false);

            /*
             * Se è presente un'errore aspetto che l'utente lo risolva
             */
            if ($(".has-error")[0]){
                return;
            } else {
                console.log(rulesJson);
                console.log(rulesSql.sql);

                $(fieldRulesJsonId).val(JSON.stringify(rulesJson));
                $(fieldRulesSqlId).val(rulesSql.sql);

                $(modalId).modal('hide');
            }



        });
    }

    function wpcInitCalculator(){

        $('.awspc-multiselect-calculator-fields').multiSelect({
            selectableHeader: "<div class='awspc-multiselect-calculator-fields-header'><i class='fa fa-list'></i> Click to select field</div>",
            selectionHeader: "<div class='awspc-multiselect-calculator-fields-header'><i class='fa fa-mouse-pointer'></i> Drag & Drop to change fields order</div>",
            keepOrder: true
        });

        /* Abilita il sortable nel multiselect */
        $("#field_container div.ms-selection ul.ms-list").sortable({helper: 'clone'});

        $('#addFieldFormulaModalAdd').click(function(){
            insertAtCursor("calculatorFormula", $("#addFieldFormulaModalSelect").val());

            $('#addFieldFormulaModal').modal('hide');
        });

        $('#addFieldFormula').click(function(){

            /* Controllo che si possano aggiungere solo i campi inseriti */
            $("#addFieldFormulaModalSelect option").each(function(){
                var option          = $(this);
                var selectValue     = $(this).val();
                var found           = false;

                $('#fields :selected').each(function(i, selected){
                    if(selectValue == "$aws_price_calc_" + $(selected).val()){
                        found = true;
                    }
                });

                if(found == true){
                    option.show();
                }else{
                    option.hide();
                }

            });



            $('#addFieldFormulaModal').modal('show');
        })

    }

    function insertAtCursor(myField, myValue){
        myField = document.getElementById(myField);

        //IE support
        if (document.selection) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
        }
        //MOZILLA and others
        else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)
                + myValue
                + myField.value.substring(endPos, myField.value.length);
        } else {
            myField.value += myValue;
        }
    }

    /* Image Selector Plugin Helper */
    $.fn.imageSelector = function(action) {
        if(action == "clear"){
            var emptyImg    = $(this).attr('data-empty-img');
            $(this).attr('src', emptyImg);
        }
    };

    $("#import_items_from_file").submit(function(){
        if($(this).attr('field_id') != "") {
            var confirmResult = confirm("This action will overwrite the current items. Do you want to continue?");
            return confirmResult;
        }else return true;
    });

    $('#cancel_import').click(function () {
        window.history.back();
    })

});


