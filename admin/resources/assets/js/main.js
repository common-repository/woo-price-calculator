/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 **/

jQuery(document).ready(function($){
    
    var requestAjaxCalculatePrice;

    WooPriceCalculator = {
        
        formContainerSelectors:         [],
        page:                           null,
        waitForCalculation:             false,
        asyncAjaxCalculatePrice:        true,
        disableAjaxPriceProductPage:    null,
        
        init: function(){

            //activated when used bundled with WooCommerce Composite Product plugin
            if($(".composite_form").length){
                $('.composite_price').ready(function() {
                    setTimeout(function () {
                        WooPriceCalculator.calculateProductPrice($('.wpc-product-form'))
                    }, 10);
                });
            }
            
            $('.woo-price-calculator-tooltip').tooltipster({
                animation: 'fade',
                contentAsHTML: true,
                multiple: true,
                theme: 'tooltipster-shadow',
                touchDevices: true,
                'maxWidth': 300
            });

            if($('.wpc-cart-form').length){
                $('.wpc-cart-form').each(function(index, element){
                    $('.wpc-cart-edit', element).click(function(){
                        var productId           = $('.wpc_product_id', element).val();
                        var simulatorId         = $('.wpc_simulator_id', element).val();
                        var cartItemKey         = $(element).attr('data-cart-item-key');
                        var remodalInst         = $('[data-remodal-id="wpc_cart_item_' + cartItemKey + '"]').remodal();
                        var editButtons         = $('[data-remodal-target="wpc_cart_item_' + cartItemKey + '"]');

                        //Evito che i prodotti si aggiungano automaticamente ad ogni calcolo (add-to-cart)
                        var data                = $(element).find(WooPriceCalculator.getFieldSelector(), element).serialize();

                        var quantity            = 0;
                        if(WooPriceCalculator.getTargetEcommerce() == "woocommerce"){
                            quantity            = parseInt($("input[name='cart[" + cartItemKey + "][qty]']").val());
                        }else if(WooPriceCalculator.getTargetEcommerce() == "hikashop"){
                            quantity            = parseInt($("input[name='item[" + cartItemKey + "]']").val());
                        }

                        $('.cart_item .product-price').html(WooPriceCalculator.htmlLoadingImage("awspricecalculator_loading"));

                        WooPriceCalculator.ajaxEditCartItem(cartItemKey, productId, simulatorId, quantity, data);
                        remodalInst.close();
                    });
                });
            }

            /*
             * Inizializzazione dei componenti per data & ora
             */
            $('.awspc-field-widget').each(function(index, element){
                
                var fieldId             = $(element).attr('data-id');
                var fieldContainer      = $(".awspc-field", element);
                
                if(fieldId == ""){
                    WooPriceCalculator.alertError("Class '.awspc-field-widget' is applied but no 'data-id' has been found");
                    WooPriceCalculator.alertError("Check also the fields you need in the theme are also selected in the calculator");
                    WooPriceCalculator.alertError($(element).html());
                }
                                
                var options             = JSON.parse($('#' + fieldId + "_options").val());

                var date_format         = 'Y-m-d';
                var time_format         = 'H:i:s';
                var datetime_format     = 'Y-m-d H:i:s';
                        
                if(options['date']){
                    if(options['date']['date_format']){
                        date_format         = options['date']['date_format'];
                    }
                    
                    if(options['date']['time_format']){
                        time_format         = options['date']['time_format'];
                    }
                    
                    if(options['date']['datetime_format']){
                        datetime_format     = options['date']['datetime_format'];
                    }
                }

                $(".aws_price_calc_date input", element).xdsoft_datetimepicker({
                    timepicker: false,
                    format: date_format,
                    lazyInit: true,
                    validateOnBlur: false,
                    allowBlank: true,
                    scrollInput: false,
                    closeOnDateSelect: true,
                });

                $(".aws_price_calc_time input", element).xdsoft_datetimepicker({
                    datepicker: false,
                    format: time_format,
                    lazyInit: true,
                    validateOnBlur: false,
                    allowBlank: true,
                    scrollInput: false,
                });

                $(".aws_price_calc_datetime input", element).xdsoft_datetimepicker({
                    format: datetime_format,
                    lazyInit: true,
                    validateOnBlur: false,
                    allowBlank: true,
                    scrollInput: false,
                });

                if(fieldContainer.hasClass('aws_price_calc_numeric')){
                    var field               = $('input', fieldContainer);
                    var decimals            = options['numeric']['decimals'];
                    var decimalSeparator    = options['numeric']['decimal_separator'];

                    if(decimals){
                        decimals            = parseInt(decimals);
                    }else{
                        decimals            = 2;
                    }

                    /* If Number of Decimals = 0, this means no decimals */
                    if(decimals == 0){
                        decimalSeparator    = false;
                    }

                    $(field).numeric({
                        decimalPlaces:  decimals,
                        decimal:        decimalSeparator,
                    });
                }

            });

            WooPriceCalculator.initFieldEvents();

            /*
             * Controllo qualsiasi richiesta ajax eseguita nel carrello.
             * In questo modo so se è stato aggiornato
             */
            setTimeout(function() {
                $('.remodal').remodal();
            }, 500);

            if(WPC_HANDLE_SCRIPT.is_cart == true){
                $(document).ajaxComplete(function(event, xhr, settings) {
                    if($('.woocommerce .cart_item').length){
                        //Rinizializzo i modal
                        $('.remodal').remodal();
                    }
                });
            }

            this.initThirdPartPluginsCompatibility();
        },

        /* Make WPC Compatible with other plugins (JS) */
        initThirdPartPluginsCompatibility: function(){
            
            /* Woo Calculate Shipping In Product Page [Plugin] */
            $(".ewc_calc_shipping").click(function(){
                $("#calc_shipping_postcode").trigger("blur");
            });
        },
        
        setAsyncAjaxCalculatePrice: function(asyncAjaxCalculatePrice){
            this.asyncAjaxCalculatePrice    = asyncAjaxCalculatePrice;
        },
        
        setWaitForCalculation: function(waitForCalculation){
          this.waitForCalculation   = waitForCalculation;
        },
        
        getPage: function(){
            if(this.page == null){
                return WPC_HANDLE_SCRIPT.page_type;
            }

            return this.page;
        },
        
        setPage: function(page){
            this.page       = page;
        },
        
        hideOutputFields: function(){
            $('.awspc-output-result-row').hide();
        },

        showOutputFields: function(){
            $('.awspc-output-result-row').show();
        },
        
        loadingOutputFields: function(){
            $(".awspc-output-result-price-value").html(WooPriceCalculator.htmlLoadingImage("awspricecalculator_loading"));
        },

        hidePrice: function(cartItemKey){
            var priceSelector       = WooPriceCalculator.getPriceSelector();

            if(cartItemKey != null){
                var cartModalContainer  = $('[data-cart-item-key="' + cartItemKey + '"]');

                $('.wpc-cart-item-price', cartModalContainer).hide();
                $('.wpc-cart-edit', cartModalContainer).prop('disabled', true);

            }else{
                $(priceSelector).hide();
                $('form[name="hikashop_product_form"] .hikashop_product_price_main').hide();
            }

            WooPriceCalculator.hideOutputFields();
        },

        showPrice: function(cartItemKey){
            var priceSelector       = WooPriceCalculator.getPriceSelector();

            if(cartItemKey != null){
                var cartModalContainer  = $('[data-cart-item-key="' + cartItemKey + '"]');

                $('.wpc-cart-item-price', cartModalContainer).show();
                $('.wpc-cart-edit', cartModalContainer).prop('disabled', false);
            }else{
                $(priceSelector).show();
                $('form[name="hikashop_product_form"] .hikashop_product_price_main').show();
            }

            WooPriceCalculator.showOutputFields();

        },

        setFieldError: function(element, error){
            $(element).html(error);
        },

        alertError: function(message){
            if(this.getHideAlertErrors() == false){
                alert("AWS Price Calculator Error: " + message);
            }
        },

        setDisableAjaxPriceProductPage: function(status){
            WooPriceCalculator.disableAjaxPriceProductPage   = status;
        },
        
        getDisableAjaxPriceProductPage: function(){
            var disableAjaxPriceProductPage     = WPC_HANDLE_SCRIPT.disable_ajax_price_product_page;
            
            if(WooPriceCalculator.disableAjaxPriceProductPage == null){
                return disableAjaxPriceProductPage;
            }
            
            return WooPriceCalculator.disableAjaxPriceProductPage;
        },
        
        /* Get the selector for the product price */
        getPriceSelector: function(){

            /* Get Ajax Class from settings */
            var singleProductAjaxHookClass      = WPC_HANDLE_SCRIPT.single_product_ajax_hook_class;
            var disableAjaxPriceProductPage     = WooPriceCalculator.getDisableAjaxPriceProductPage();
            
            if(disableAjaxPriceProductPage == true){
                return null;
            }
            
            /* Checking if user has defined WooCommerce Price classes to hook */
            if(singleProductAjaxHookClass){
                if($(singleProductAjaxHookClass).length){
                    return singleProductAjaxHookClass;
                }else{
                    WooPriceCalculator.alertError("Class not found, see Settings > Single Product Ajax Hook Class");
                }
            }

            /* If not, I will try standard classes: */

            /*
             * Bisogna evitare che il prezzo sia aggiornato dove non si deve
             * all'interno della stessa pagina
             */

            // check if the product is using Woocommerce Composite Products plugin
            if($(".composite_form").length){
                $(".composite_price").hide();
                return '.price';
            }

            if($(".product .summary .price .woocommerce-Price-amount").length){
                return '.product .summary .price .woocommerce-Price-amount';
            }

            if($(".single-product .product_infos .price .woocommerce-Price-amount").length){
                return ".single-product .product_infos .price .woocommerce-Price-amount";
            }

            if($(".product .summary .price").length){
                return '.product .summary .price';
            }

            if($(".single-product .product_infos .price").length){
                return ".single-product .product_infos .price";
            }

            if($(".wpc-cart-form .price").length){
                return '.wpc-cart-form .price';
            }

            if($(".product .price-box .amount").length){
                return '.product .price-box .amount';
            }

            if($(".product-details .product-item_price .price").length){
                return '.product-details .product-item_price .price';
            }

            if($('form[name="hikashop_product_form"] .hikashop_product_price').length){
                return 'form[name="hikashop_product_form"] .hikashop_product_price';
            }

            if($('.product-main .product-page-price').length){
                return '.product-main .product-page-price';
            }

            WooPriceCalculator.alertError("Unable to select Ajax WooCommerce Price class, read: https://altoswebsolutions.com/documentation/9-the-price-doesn-t-change");
        },

        getFieldSelector: function(){
            return  '.awspc-field input, ' +
                    '.awspc-field select, ' +
                    '.awspc-custom-data'
                ;
        },

        htmlLoadingImage: function(cssClass){
            return "<img class=\"" + cssClass + "\" src=\"" + WPC_HANDLE_SCRIPT.resources_url + "/assets/images/ajax-loader.gif\" />";
        },

        conditionalLogic: function(logic, cartItemKey){

            $.each(logic, function(fieldId, displayField){
                var fieldContainer  = $('.awspc-field-row[data-field-id="' + fieldId + '"]');

                if(displayField == 1){
                    $(fieldContainer).show();
                }else{
                    $(fieldContainer).hide();
                }
            });
        },

        getFieldContainer: function(fieldId, cartItemKey){
            if(cartItemKey != null){
                var cartModalContainer  = $('[data-cart-item-key="' + cartItemKey + '"]');
                var fieldContainer      = $("[data-id='" + fieldId + "']", cartModalContainer);
            }else{
                var fieldContainer      = $("form.cart [data-id='" + fieldId + "'], form[name=\"hikashop_product_form\"] [data-id='" + fieldId + "']");
            }

            return fieldContainer;
        },

        decodePrice: function(priceToDecode){
            return WooPriceCalculator.decodeHtml(WooPriceCalculator.decodeUtf8(priceToDecode));
        },
        
        ajaxCalculatePrice: function(productId, simulatorId, cartItemKey, data, outputEl, formContainer, compositePrice){

            WooPriceCalculator.showPrice(cartItemKey);
            WooPriceCalculator.loadingOutputFields();
                        
            /* WooPriceCalculator.showProductImageLoading(); */
            $(document).trigger("awspcAjaxCalculatePrice");

            $(outputEl).html(WooPriceCalculator.htmlLoadingImage("awspricecalculator_loading"));

            if(formContainer){
                $(".awspc-field-error", formContainer).html("");
            }else{
                $(".awspc-field-error").html("");
            }

            if(this.waitForCalculation == false){
                if(requestAjaxCalculatePrice && requestAjaxCalculatePrice.readyState != 4){
                    requestAjaxCalculatePrice.abort();
                }
            }

            var calculatePriceUrl = (compositePrice >0) ? WPC_HANDLE_SCRIPT.ajax_url + "&id=" + productId + "&simulatorid=" + simulatorId + "&compositeBasePrice=" + compositePrice : WPC_HANDLE_SCRIPT.ajax_url + "&id=" + productId + "&simulatorid=" + simulatorId;

            requestAjaxCalculatePrice = $.ajax({
                method: "POST",
                async: this.asyncAjaxCalculatePrice,
                url: calculatePriceUrl + "&page=" + this.getPage(),
                dataType: 'json',
                data: data,

                success: function(result, status, xhrRequest) {

                    $(document).trigger("awspcAjaxCalculatePriceSuccess", {
                        'result':           result,
                        'formContainer':    formContainer,
                    });
                    
                    /* WooPriceCalculator.productImageLogic(result.productImageLogic); */
                    WooPriceCalculator.conditionalLogic(result.conditionalLogic, cartItemKey);

                    if(result.errorsCount == 0){

                        $.each(result.outputFields, function(fieldId, data){
                            var fieldName       = data.fieldName;
                            var field           = data.field;
                            var fieldSelector   = "." + fieldName;
                            var value           = data.value;
                            
                            $(fieldSelector)
                                    .find(".awspc-output-result-value")
                                    .html(value);
                            
                        });
                       

                        $(outputEl).html(WooPriceCalculator.decodePrice(result.price));
                        $(outputEl).show();
                        
                        WooPriceCalculator.showOutputFields();
                    }else{
                        WooPriceCalculator.hidePrice(cartItemKey);

                        $.each(result.errors, function(fieldId, fieldErrors){
                            $.each(fieldErrors, function(index, fieldError){

                                var error               = $(".awspc-field-error", WooPriceCalculator.getFieldContainer(fieldId, cartItemKey));
                                
                                $(error).html(fieldError);
                            });
                        });
                    }

                    $('.wpc-product-form').show();
                    
                    $(document).trigger("awspcAjaxCalculatePriceSuccessEnd", {
                        'result':           result,
                        'formContainer':    formContainer,
                    });
                },
                
                error: function(xhrRequest, status, errorMessage)  {
                    //alert("Sorry, an error occurred");
                    console.log("AWS Price Calculator Error: " + errorMessage);
                }
            });
        },

        ajaxAddCartItem: function(productId, calculatorId, quantity, data){
            $.ajax({
                method: "POST",
                async: false,
                cache: false,
                url: WPC_HANDLE_SCRIPT.ajax_url + "&id=" + productId +
                "&simulatorid=" + calculatorId +
                "&wpc_action=add_cart_item" +
                "&quantity=" + quantity,

                data: data,

                success: function(result, status, xhrRequest){
                    
                    $(document).trigger("awspcAjaxAddCartItemSuccess", {
                        'result':           result,
                    });
                },
                error: function(xhrRequest, status, errorMessage){
                    
                    $(document).trigger("awspcAjaxAddCartItemError", {
                        'errorMessage':           errorMessage,
                    });
                    
                    console.log("Error: " + errorMessage);
                }
            });
        },
        
        ajaxEditCartItem: function(cartItemKey, productId, simulatorId, quantity, data){
			
            /* If quantity is not defined, quantity = 1 */
            if(isNaN(quantity)){
                quantity	= 1;
                console.log("WPC: No defined quantity, get 1");
            }
			
            $.ajax({
                method: "POST",
                url: WPC_HANDLE_SCRIPT.ajax_url + "&id=" + productId +
                "&simulatorid=" + simulatorId +
                "&wpc_action=edit_cart_item" +
                "&cart_item_key=" + cartItemKey +
                "&quantity=" + quantity,

                data: data,

                success: function(result, status, xhrRequest){
                    location.reload();

                    //console.log(result);
                },
                error: function(xhrRequest, status, errorMessage){
                    console.log("Error: " + errorMessage);
                }
            });
        },

        wooCommerceUpdateCart: function(){
            $('[name="update_cart"]').trigger('click');
        },

        calculatePrice: function(element){
            /* API: awspcBeforeCalculatePrice */
            $(document).trigger("awspcBeforeCalculatePrice");

            /* Si potrebbe anche fare che sia l'utente ad impostare la classe di cambio del prezzo, nel caso sia utilizzati plugin che modificano la parte del prezzo */
            if(WPC_HANDLE_SCRIPT.is_cart == true){
                if($('.wpc-cart-form').length){
                    WooPriceCalculator.calculateCartPrice();
                }
            }else{
                
                
                var formContainerSelector   = WooPriceCalculator.getFormContainerSelector();
                var formContainer           = $(element).closest(".wpc-product-form");

                $(document).trigger("awspcBeforeProductCalculatePrice", {
                    'formContainer':    formContainer,
                });

                WooPriceCalculator.calculateProductPrice(formContainer);

                $(document).trigger("awspcAfterProductCalculatePrice", {
                    'formContainer':    formContainer,
                });


            }

            /* API: awspcAfterCalculatePrice */
            $(document).trigger("awspcAfterCalculatePrice");
        },

        calculateCartPrice: function(){
            var element             = window.wpcCurrentCartItem;
            var productId           = $('.wpc_product_id', element).val();
            var simulatorId         = $('.wpc_simulator_id', element).val();
            //Evito che i prodotti si aggiungano automaticamente ad ogni calcolo (add-to-cart)
            var data                = $(element).find(WooPriceCalculator.getFieldSelector(), element).serialize();
            var cartItemKey         = $(element).attr('data-cart-item-key');

            //console.log(data);

            WooPriceCalculator.ajaxCalculatePrice(productId, simulatorId, cartItemKey, data, $('.price', element).first());

        },

        getProductIdInProductPage: function(){
            return $('.wpc_product_id').val();
        },
        
        getCalculatorIdInProductPage: function(){
            return $('.wpc_simulator_id').val();
        },
        
        getFormDataInProductPage: function(formContainer){
            return $(formContainer).find(this.getFieldSelector(), formContainer).serialize();
        },
        
        calculateProductPrice: function(formContainer){
            var productId           = this.getProductIdInProductPage();
            var simulatorId         = this.getCalculatorIdInProductPage();
            var strictPriceSelector = $(formContainer).attr('data-strict-price-selector');

            var priceSelector       = WooPriceCalculator.getPriceSelector();

            if (priceSelector == '.price'){
                var jPriceSelector = $(priceSelector).first();
            } else if(strictPriceSelector == true){
                var jPriceSelector  = $(priceSelector, formContainer);
            }else{
                var jPriceSelector  = $(priceSelector)
            }

            //Evito che i prodotti si aggiungano automaticamente ad ogni calcolo (add-to-cart)
            var data                = this.getFormDataInProductPage(formContainer);

            var compositePrice = priceSelector == '.price' ? parseFloat($('.composite_price').find('.price').find('.woocommerce-Price-amount').text()) : 0;

            
            WooPriceCalculator.ajaxCalculatePrice(productId, simulatorId, null, data, jPriceSelector, formContainer, compositePrice);
        },

        addFormContainerSelector: function(selector){
            this.formContainerSelectors.push(selector);
        },
        
        getFormContainerSelector: function(){
            var retSelector     = null;
            
            if($("form.cart .wpc-product-form").length){
                return "form.cart .wpc-product-form";
            }
            
            if($('[name="hikashop_product_form"] .wpc-product-form').length){
                return '[name="hikashop_product_form"] .wpc-product-form';
            }
            
            $.each(this.formContainerSelectors, function(index, selector){
                if($(selector).length){
                    retSelector     = selector;
                }
            });
            
            return retSelector;
        },

        getTargetEcommerce: function(){
            return WPC_HANDLE_SCRIPT.target_ecommerce;
        },

        encodeUtf8: function(s){
            return encodeURIComponent(s);
        },

        decodeUtf8: function(s){
            return decodeURIComponent(s);
        },

        initFieldEvents: function(){
            var timeout             = false;
            var writingTimeout      = 250;

            if(WPC_HANDLE_SCRIPT.is_cart == true){
                $(document).on('opening', '.remodal', function (){
                    window.wpcCurrentCartItem    = $(this);
                    WooPriceCalculator.calculateCartPrice();

                    //console.log('Cart Item has been opened: ' + $(this).attr('data-cart-item-key'));
                });
            }

            $(document).on('keyup', '.aws_price_calc_numeric input', function(){
                if(timeout){
                    clearTimeout(timeout);
                }

                var element     = this;
                timeout = setTimeout(function () {
                    WooPriceCalculator.calculatePrice(element);
                }, writingTimeout);
            });

            /* Per gli elementi di tipo Range */
            $(document).on('change', '.aws_price_calc_numeric input[type=range]', function(){
                if(timeout){
                    clearTimeout(timeout);
                }

                var element     = this;
                timeout = setTimeout(function () {
                    WooPriceCalculator.calculatePrice(element);
                }, writingTimeout);
            });

            $(document).on('keyup', '.aws_price_calc_text input', function(){
                if(timeout){
                    clearTimeout(timeout);
                }

                var element     = this;
                timeout = setTimeout(function () {
                    WooPriceCalculator.calculatePrice(element);
                }, writingTimeout);
            });

            $(document).on('change', '.aws_price_calc_date input', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            $(document).on('change', '.aws_price_calc_time input', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            $(document).on('change', '.aws_price_calc_datetime input', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            $(document).on('change', '.aws_price_calc_picklist select', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            $(document).on('change', '.aws_price_calc_radio input', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            $(document).on('change', '.aws_price_calc_checkbox input', function(){
                WooPriceCalculator.calculatePrice(this);
            });

            /* Image List: Mouseover on a modal element */
            $(document).on('mouseover', '.awspc-modal-imagelist-row', function(){
                $('.awspc-modal-imagelist-row').removeClass('awspc-modal-imagelist-hover');
                $(this).addClass('awspc-modal-imagelist-hover');
            });

            /* Image List: Click on a modal element */
            $(document).on('click', '.awspc-modal-imagelist-row', function(){
                /* Getting al elements I need */
                var cartItemKey        = $(this).attr('data-cart-item-key');
                var imagelistId        = $(this).attr('data-imagelist-id');
                var label              = $(this).attr('data-label');
                var itemId             = $(this).attr('data-item-id');
                var modalSelector      = $('[data-remodal-id="awspc_modal_imagelist_' + imagelistId + '"]');
                var remodalInst        = $(modalSelector).remodal();
                var clickedImageSrc    = $(this).find('img').attr('src');
                var hiddenSelector     = $('[data-id="aws_price_calc_' + imagelistId +'"] input[type="hidden"]');
                var buttonSelector     = $('[data-id="aws_price_calc_' + imagelistId + '"] button');
                var textSelector       = $(buttonSelector).find('.awspc_modal_imagelist_text');
                var imageSelector      = $(buttonSelector).find('img');

                /* Hidding old clicked images */
                $(modalSelector).find('.awspc-modal-imagelist-row').removeClass('awspc-modal-imagelist-clicked');

                /* Changing button image */
                $(textSelector).html(label);
                $(imageSelector).attr('src', clickedImageSrc);
                $(hiddenSelector).val(itemId);

                /* Changing style */
                $(this).removeClass('awspc-modal-imagelist-hover');
                $(this).addClass('awspc-modal-imagelist-clicked');

                /* Close Modal */
                remodalInst.close();

                /* If the client is in cart, I re-open the item modal popup */
                if(cartItemKey){
                    $('[data-remodal-id="wpc_cart_item_' + cartItemKey + '"]').remodal().open();
                }

                /* Recalculate price */
                WooPriceCalculator.calculatePrice(hiddenSelector);
            });


        },

        encodeHtml: function(value){
            return $('<div/>').text(value).html();
        },

        decodeHtml: function(value){
            return $('<div/>').html(value).text();
        },
        
        getCartUrl: function(){
            return WPC_HANDLE_SCRIPT.cart_url;
        },
        
        /*
         * 
         * @param {type} number: the number to format
         * @param {type} c: decimals
         * @param {type} d: decimals separator
         * @param {type} t: thousand separator
         * @returns {String} the number formated
         */
	formatNumber: function(number, c, d, t){
		var n = number, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
		j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 },

        formatPrice: function(price){
            var phpPrice        = $(".awspc-price-format").val();
            var countDecimals   = 0;
            var currency        = "";
            
            phpPrice            = this.decodePrice(phpPrice);

            var myRegexp = /([^0-9]*)([9])([\.|,]?)([9]{3})([\.|,]?)([1]*)([^0-9]*)/g;
            
            var match           = myRegexp.exec(phpPrice);
            
            var currencyPrefix      = match[1];
            var thousandSeparator   = match[3];
            var decimalSeparator    = match[5];
            var decimals            = (match[6].match(/1/g) || []).length;
            var currencySuffix      = match[7];

            /*
            alert(currencyPrefix);
            alert(thousandSeparator);
            alert(decimalSeparator);
            alert(decimals);
            alert(currencySuffix);
            */

            return currencyPrefix +
                    this.formatNumber(price, decimals, decimalSeparator, thousandSeparator) +
                    currencySuffix;
            
            
        },
        
        getHideAlertErrors: function(){
            return WPC_HANDLE_SCRIPT.hide_alert_errors;
        }
       

    };

    WooPriceCalculator.init();

});