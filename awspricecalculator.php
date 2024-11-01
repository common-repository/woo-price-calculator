<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

require_once('admin/resources/lib/eos/Stack.php');
require_once('admin/resources/lib/eos/Parser.php');


require_once('admin/awsframework/Helper/FrameworkHelper.php');
        
class AWSPriceCalculator {
    
	var $plugin_label           = "Woo Price Calculator";
	var $plugin_code            = "woo-price-calculator";
        var $plugin_dir             = "woo-price-calculator";
        var $plugin_db_version      = null;

        var $view = array();
        
        var $wsf = null;
        var $db;
        
        var $fieldHelper;
        var $calculatorHelper;
        
        var $fieldModel;
        
	public function __construct($plugin_db_version){
            
            global $wpdb;

            $this->wpdb                 = $wpdb;
            $this->plugin_db_version    = $plugin_db_version;
            
            add_action( 'save_post', array($this, 'save_post'));
            
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            
            add_action('admin_menu', array( $this, 'register_submenu_page'),99);

            add_action('woocommerce_before_add_to_cart_button', array($this, 'product_meta_end'));

            add_filter('woocommerce_cart_item_price', array($this, 'cartItemPrice'), 1, 3);
            add_filter('woocommerce_cart_item_price_html', array($this, 'woocommerce_cart_item_price_html'), 1, 3);
            add_filter('woocommerce_cart_product_subtotal', array($this, 'woocommerce_cart_product_subtotal'), 10, 4 ); 
            
            add_action('woocommerce_before_calculate_totals', array($this, 'woocommerce_before_calculate_totals'), 10, 1);
            add_action('woocommerce_add_to_cart', array($this, 'add_to_cart_callback'), 10, 6);
            add_action('woocommerce_add_cart_item_data', array($this, 'woocommerce_add_cart_item_data'), 10, 4);

            add_action('woocommerce_cart_item_removed', array($this, 'action_woocommerce_cart_item_removed'), 10, 2 );

            add_action('woocommerce_add_order_item_meta', array($this, 'action_woocommerce_add_order_item_meta'), 1, 3 );
            add_action('woocommerce_checkout_update_order_meta', array($this, 'action_woocommerce_checkout_update_order_meta'), 10, 2);
            add_action('woocommerce_checkout_order_processed', array($this, 'action_woocommerce_checkout_order_processed'), 10, 1 );
                    
            add_action( 'add_meta_boxes', array($this, 'order_add_meta_boxes'));
            
            add_action('wp_ajax_awspricecalculator_ajax_callback', array($this, 'ajax_callback'));
            add_action('wp_ajax_nopriv_awspricecalculator_ajax_callback', array($this, 'ajax_callback'));

            /* Setting a very low priority, because for a tip I need to add to cart the product */
            add_filter('woocommerce_add_to_cart_validation', array($this, 'filter_woocommerce_add_to_cart_validation'), 100, 3);
            add_filter('woocommerce_add_to_cart_redirect', array($this, 'filter_woocommerce_add_to_cart_redirect'));
            add_filter('woocommerce_get_price_html', array($this, 'filter_woocommerce_get_price_html'), 10, 2);
            add_filter('woocommerce_cart_item_name', array($this, 'filter_woocommerce_cart_item_name'), 20, 3);
            

            
            

            
            add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'woocommerce_loop_add_to_cart_link'), 10, 2 );
            
            add_action('plugins_loaded', array($this, 'action_plugins_loaded'));
            
            add_action('init', array($this, 'wp_init'), 1);


            /* Assign calculators from product admin page panel, custom tabs, ajax calls for remove and assign calculator */
            add_filter( 'woocommerce_product_data_tabs', array($this,'wpc_custom_product_tabs'));
            add_action( 'woocommerce_product_data_panels', array($this,'wpc_product_data_panel'));
            add_action('wp_ajax_awspricecalculator_ajax_attach_calculator', array($this,'ajax_attach_calculator'));
            add_action('wp_ajax_awspricecalculator_ajax_remove_calculator', array($this,'ajax_remove_calculator'));

            
            //NOT WORKING FOR NOW: add_filter('woocommerce_cart_shipping_packages', array($this, 'woocommerce_cart_shipping_packages'));
            
           // add_filter('admin_footer_text', array($this, 'filter_admin_footer_text'));
            
            $this->wsf               = new WSF\Helper\FrameworkHelper($this->plugin_dir, plugin_dir_path( __DIR__ ), "wordpress");
            
            $this->wsf->setVersion($plugin_db_version);
            
            $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));
            
            $this->calculatorHelper = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
            $this->fieldHelper      = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
            $this->themeHelper      = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'ThemeHelper', array($this->wsf));
            $this->cartHelper       = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CartHelper', array($this->wsf));
            $this->orderHelper      = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'OrderHelper', array($this->wsf));
            $this->pluginHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'PluginHelper', array($this->wsf));
            $this->productHelper    = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'ProductHelper', array($this->wsf));
            $this->ecommerceHelper  = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));
            
            $this->fieldModel       = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
            $this->calculatorModel  = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
            $this->settingsModel    = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'SettingsModel', array($this->wsf));
            
            /* Meglio lasciarlo sempre per ultimo affinchè siano istanziati gli oggetti */
            $this->pluginHelper->pluginUpgrade($this->plugin_db_version);
            
            /* Può eseguire azioni prima di qualsiasi altra stampa HTML impostando raw=1 */
            if($this->wsf->requestValue("page") == "woo-price-calculator" && $this->wsf->requestValue("raw") == true){
                $this->wsf->execute("awspricecalculator", true, '\\AWSPriceCalculator\\Controller');
            }
	}
        
        
        function filter_woocommerce_order_item_quantity_html($html, $item){
            return $html;
        }

        /* The quantity of order (It is used for example for inventory management) */
        function filter_woocommerce_order_item_quantity($quantity, $order, $item){
            return $quantity;
        }

        function filter_woocommerce_cart_item_quantity($product_quantity, $cartItemKey, $cartItem){
            global $woocommerce;

            if(!empty($cartItem['simulator_id'])){
                $productId      = $cartItem['product_id'];
                $product        = new WC_Product($productId);
                $calculator     = $this->calculatorHelper->get_simulator_for_product($productId);
                
                if(!empty($calculator)){
                    if(!empty($calculator->overwrite_quantity)){
                        $data    = $cartItem['simulator_fields_data'];
                        return $cartItem['quantity'];
                    }
                }
            }
            
            return $product_quantity;
        }

        /* Change Arguments for the Quantity Field */
        function filter_woocommerce_quantity_input_args($args, $product){
            
            $productId                      = $product->get_id();
            $calculator                     = $this->calculatorHelper->get_simulator_for_product($productId);
            
            if(!empty($calculator)){

                if(!empty($calculator->overwrite_quantity)){
                    /* Hide the quantity field */
                    $args['max_value'] = 1;
                    $args['min_value'] = 1;
                }
                
            }
            
            return $args;
        }
        
        /*
        function woocommerce_cart_shipping_packages($packages){
            
            foreach($packages as $packageIndex => $package){
                $cartContents   = $package['contents'];
                
                foreach($cartContents as $cartItemKey => $cartItem){
                    $packages[$packageIndex]['contents'][$cartItemKey]['data']->set_weight(90);
                    $packages[$packageIndex]['contents'][$cartItemKey]['data']->apply_changes();
                }
            }
            
            return $packages;
        }
        */
        
        public function wp_init() {
            if(!session_id()){
                session_start();
            }
        }

        /* FedEx WooCommerce Extension Compatibility */
        function wf_fedex_packages($ships){

            $shipsClone     = $ships;
            
            foreach (WC()->cart->get_cart() as $cart_item_key => $values){
                if(isset($values['simulator_id'])){
                    $productId                      = $values['product_id'];
                    $quantity                       = $values['quantity'];
                    
                    $calculator                     = $this->calculatorHelper->get_simulator_for_product($productId);
                    
                    if(!empty($calculator)){
                        $calculatorFieldsData    = $values['simulator_fields_data'];
                        $this->calculatorHelper->calculate_price($productId, $calculatorFieldsData, false, $calculator->id, $outputResults);
                        
                        foreach($shipsClone as $shipIndex => $shipData){
                            if(
                                $shipData['packed_products'][0]->id == $productId && 
                                $shipData['GroupPackageCount'] == $quantity
                                ){
                                
                                    /* E' stato impostato il campo di overwrite per il peso */
                                    if(!empty($calculator->overwrite_weight)){
                                        $weight = $outputResults[$calculator->overwrite_weight];
                                        $ships[$shipIndex]['Weight']['Value']       = $outputResults[$calculator->overwrite_weight];
                                    }

                                    /* E' stato impostato il campo di overwrite per la lunghezza */
                                    if(!empty($calculator->overwrite_length)){ 
                                        $ships[$shipIndex]['Dimensions']['Length']  = $outputResults[$calculator->overwrite_length];
                                    }

                                    /* E' stato impostato il campo di overwrite per la larghezza */
                                    if(!empty($calculator->overwrite_width)){ 
                                        $ships[$shipIndex]['Dimensions']['Width']   = $outputResults[$calculator->overwrite_width];
                                    }

                                    /* E' stato impostato il campo di overwrite per l'altezza */
                                    if(!empty($calculator->overwrite_height)){ 
                                        $ships[$shipIndex]['Dimensions']['Height']  = $outputResults[$calculator->overwrite_height];
                                    }
                                    
                                    unset($shipsClone[$shipIndex]);
                                    break;
                                    
                            }
                        }
                    }
                    
                    
                }
            }
            
            return $ships;
        }
        
        /*
         * Eseguita al salvataggio di un post
         */
        function save_post($postId) {
            $post       = get_post($postId);

            if($post->post_type == "product"){
                /* Controllo duplicato Calcolatori, visualizzare errore */
            }
    
        }
               
        /*
         * Cambia la visualizzazione del pulsante Add to cart presente nell'archivio
         */
        function woocommerce_loop_add_to_cart_link($link, $product){
            $calculator  = $this->calculatorHelper->get_simulator_for_product($product->get_id());
            
            if(!empty($calculator)){
                $link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
                    esc_url(get_permalink($product->get_id())),
                    esc_attr($product->get_id()),
                    esc_attr($product->get_sku()),
                    esc_attr(isset( $quantity ) ? $quantity : 1),
                    esc_attr($product->get_type()),
                    esc_html($this->wsf->mixTrans('ecommerce.shop.choose_an_option'))
                );
            }
            
            return $link;
        }

        function admin_enqueue_scripts($hookSuffix){
            $this->pluginHelper->adminEnqueueScripts($this->plugin_code, $hookSuffix);
        }
        
        function wp_enqueue_scripts(){
            $this->pluginHelper->frontEnqueueScripts($this->plugin_code, get_the_ID());
        }
        
        /*
         * Modifica il prezzo nella pagina del prodotto e nelle pagine dello shop
         * 
         * Visualizzo il prezzo all'inizio, prendo i valori di default per calcolare il prezzo di partenza
         */
        function filter_woocommerce_get_price_html($productPrice, $product){
            
            if($product->post_type == "product"){
                $productId	= $product->get_id();
            }else if($product->post_type == "product_variation"){
                $productId	= $product->get_parent_id();
            }else{
                $productId	= null;
            }

            if(!empty($productId)){
                $simulator  = $this->calculatorHelper->get_simulator_for_product($productId);

                if(!empty($simulator)){
                    /* 
                     * Evito di visualizzare il prezzo nel backend, se ci sono tanti prodotti
                     * il programma ci impiega molto tempo a visualizzare la pagina
                     * Ma in ogni caso faccio visualizzare il plugin in caso di richiesta POST
                     * perchè ci potrebbero essere dei plugin che richiedono il prezzo come ad esempio
                     * YITH WooCommerce Quick View
                     */
                    if(!is_admin() || $this->wsf->isPost() == true){
                        try{
                            
                            $outputResults      = null;
                            $conditionalLogic   = null;
                            $errors             = null;
                            $fieldValues        = $this->calculatorHelper->getProductPageUserData($simulator);
                            $checkErrors        = $this->calculatorHelper->hasToCheckErrors($simulator);
                            $page               = $this->ecommerceHelper->getPageType();
                            
                            $price		= $this->calculatorHelper->calculate_price(
                                $productId, 
                                $fieldValues, 
                                true, 
                                $simulator->id, 
                                $outputResults, 
                                $conditionalLogic, 
                                $checkErrors, 
                                $errors,
                                $priceRaw, //Not used here
                                $page
                            );
                            
                            $price                = apply_filters('awspc_filter_wc_get_price_html', $price, array(
                                'productId'         => $productId,
                                'userData'          => $fieldValues,
                                'calculator'        => $simulator,
                                'outputResults'     => $outputResults,
                                'conditionalLogic'  => $conditionalLogic,
                                'checkErrors'       => $checkErrors,
                                'errors'            => $errors,
                                'productPrice'      => $productPrice,
                                'priceRaw'          => $priceRaw,
                                'page'              => $page,
                            ));
                            
                            //$price              = null;
                        }catch (\Exception $ex) {
                            $price              = "Error: {$ex->getMessage()}";
                        }

                        return "<span class=\"woocommerce-Price-amount amount\">{$price}</span>";
                    }else{
                        return "Calculator Price";
                    }
                }
            }
		
            return $productPrice;
        }
        
        /*
         * Modifica il nome del prodotto nella pagina del carrello
         */
        function filter_woocommerce_cart_item_name($productTitle, $cartItem, $cartItemKey){

            
            return $productTitle;
        }
        
        /*
         * Eseguito in review-order.php per rivedere l'ordine
         */
        
        /*
         * Eseguito dopo l'acquisto, nel dettaglio dell'ordine
         */
        

        
        function filter_admin_footer_text () {
            echo "";
        } 

        /*
         * Dopo che è stato aggiunto un prodotto reindirizza direttamente
         * al checkout
         */
        function filter_woocommerce_add_to_cart_redirect() {

            $product_id = $this->wsf->requestValue('add-to-cart');
            if(!empty($product_id)){
                $simulator = $this->calculatorHelper->get_simulator_for_product($product_id);

                if(!empty($simulator)){
                    if($simulator->redirect == 1){
                        return wc_get_checkout_url();
                    }
                }
            }

        } 

        /*
         * Attivazione dell'internazionalizzazione
         */
        function action_plugins_loaded() {
            load_plugin_textdomain($this->plugin_code, false, dirname( plugin_basename(__FILE__) ) . '/lang' );
        }

        /*
         * Validazione dei campi del simulatore all'aggiunta del prodotto
         * nel carrello
         */
        function filter_woocommerce_add_to_cart_validation($bool, $product_id, $quantity){
            $simulator = $this->calculatorHelper->get_simulator_for_product($product_id);

            if(!empty($simulator)){
                $requestFields      = $this->calculatorHelper->getFieldsFromRequest($product_id, $simulator);

                $this->calculatorHelper->calculate_price(
                        $product_id, 
                        $requestFields['data'], 
                        false, 
                        $simulator->id,
                        $outputResults, 
                        $conditionalLogic,
                        true,
                        $errors,
                        $priceRaw,
                        "add-to-cart");
                
                if(count($errors) != 0){
                    foreach($errors as $fieldId => $fieldErrors){
                        foreach($fieldErrors as $errorMessage){
                            wc_add_notice($errorMessage, "error");
                        } 
                    }
                    
                    return false;
                }
  
                /*
                 * Aggiungo il prodotto. Il prodotto inserito di default da WC
                 * sarà cancellato nella funzione add_to_cart_callback
                 */
                if($bool == true){
                    /*
                     * Svuota il carello prima di ogni aggiunta di prodotto (Se l'opzione è attiva)
                     */
                    if($simulator->empty_cart == 1){
                        $this->ecommerceHelper->emptyCart();
                    }
                }
            }
            
            return $bool;
        }
        

        /*
         * Aggiungo ulteriori informazioni nell'ordine che mi saranno utili in futuro.
         * 
         * Potrei anche utilizzare direttamente questo metodo per salvare i dati
         * della tabella "woopricesim_simulations" nell'ordine.
         */
        function action_woocommerce_add_order_item_meta($item_id, $values, $cart_item_key){

            if(isset($values['simulator_id'])){
                
                /* Adding cart item key to the order info */
            	wc_add_order_item_meta($item_id, "_wpc_cart_item_key", $cart_item_key);
            }
        }
        
        
        /*
         * Eseguito prima di effettuare il checkout
         * 
         * E' possibile prendere le informazioni inserite dall'utente in fase 
         * di checkout
         */
        function action_woocommerce_checkout_update_order_meta($orderId, $values){
        }
        
        /*
         * Salvataggio della simulazione nel database, quando l'utente proccede
         * all'ordine
         */
        function action_woocommerce_checkout_order_processed($order_id){
            $orderData                  = array();
            $simulatorsDataBackup       = array();
            $foundSimulators            = false;
            
            foreach (WC()->cart->get_cart() as $cart_item_key => $values){
                if(isset($values['simulator_id'])){
                    $foundSimulators                = true;
                    $simulatorId                    = $values['simulator_id'];
                    
                    $orderData[$cart_item_key]      = $values;
                    
                    if(!array_key_exists($simulatorId, $simulatorsDataBackup)){
                        $simulatorsDataBackup[$simulatorId]     = $this->calculatorModel->get($simulatorId);
                    }
                }
            }

            if($foundSimulators === true){
                $this->calculatorModel->saveSimulation($order_id, $orderData, $simulatorsDataBackup);
            }
        }
    
        /*
         * Aggiunta di un blocco negli Ordini
         */
        public function order_add_meta_boxes(){
            add_meta_box( 
                'woocommerce-order-my-custom', 
                "Price Calculator", 
                array($this,'order_simulation'), 
                'shop_order', 
                'normal', 
                'default' 
            );
        }
        
        
        /*
         * Visualizzazione di tutte le simulazioni per quell'ordine
         */
        public function order_simulation($order){
            echo $this->orderHelper->calculatorOrder($order->ID);
        }
        
        /*
         * Eseguito alla rimozione di un prodotto dal carrello
         */
        function action_woocommerce_cart_item_removed($cart_item_key, $instance){

        }

        /*
         * Eseguito per gli elementi nel carrello
         */
        function cartItemPrice($product_name, $values, $cart_item_key){
            global $woocommerce;

            $product    = $this->ecommerceHelper->getProductById($values['product_id']);
            $cartItem   = $woocommerce->cart->get_cart_item($cart_item_key);
            
            if(isset($cartItem['simulator_id'])){
                $calculatorId           = $cartItem['simulator_id'];
                $fieldsData             = $cartItem['simulator_fields_data'];
                
                $price                  = $this->calculatorHelper->calculate_price($values['product_id'], $fieldsData, true, $calculatorId, $outputResults, $conditionalLogic);
                $calculator             = $this->calculatorModel->get($calculatorId);
                $simulatorFieldsIds     = $this->calculatorHelper->get_simulator_fields($calculator->id);
                $simulatorFields        = $this->fieldHelper->get_fields_by_ids($simulatorFieldsIds);

                /*
                   * controllo se ci sta un problema su i campi dell calcolatore,
                   * in caso di si, rimuovo il prodotto dal carrello
                   */
                foreach($simulatorFields as $simulatorKey => $simulatorField) {

                    if ($simulatorField == null) {
                        WC()->cart->remove_cart_item($cart_item_key);
                        return null;
                    }
                }


                /* Non faccio vedere il tasto modifica nel carrello dropdown */
                if(is_cart() == true){
                    $calculatorFieldsIds    = $this->calculatorHelper->get_simulator_fields($calculator->id);
                    $calculatorFields       = $this->fieldHelper->get_fields_by_ids($calculatorFieldsIds);
                    $defaultThemeData       = $this->themeHelper->getDefaultThemeData($calculator, $simulatorFields, $fieldsData);
                    
                    return $this->wsf->getView('awspricecalculator', 'cart/edit.php', true, array(
                        'product'               => $product,
                        'cartItemKey'           => $cart_item_key,
                        'price'                 => $price,
                        'cartEditButtonClass'   => $this->settingsModel->getValue("cart_edit_button_class"),
                        'cartEditButtonPosition'=> $this->settingsModel->getValue("cart_edit_button_position"),
                        'modal'             =>  $this->wsf->getView('awspricecalculator', 'product/product.php', true, array(
                            'product'               => $product,
                            'simulator'             => $calculator,
                            'data'                  => $defaultThemeData,
                            'priceFormat'           => $this->ecommerceHelper->getPriceFormatForJs(),
                            'outputResults'         => $this->calculatorHelper->getOutputResultsPart($calculator, $outputResults),
                            'conditionalLogic'      => $conditionalLogic,
                        )) . $this->wsf->getView("awspricecalculator", "product/footer_data.php", true, array(
                            'product'               => $product,
                            'simulator'             => $calculator,
                            'data'                  => $defaultThemeData,
                            'imagelist_modals'      => $this->wsf->getView("awspricecalculator", 'partial/imagelist_modal.php', true, array(
                                'simulator_fields'  => $calculatorFields,
                                'fieldHelper'       => $this->fieldHelper,
                                'cartItemKey'       => $cart_item_key,
                                'data'              => $defaultThemeData,
                            )),
                        )),
                    ));
                }else{
                    return $price;
                }

            }
            
            return $product_name;
        }

        /*
         * Eseguito per gli elementi nel carrello (Versione HTML)
         * 
         * Questo prezzo viene anche visualizzato nel carrello dropdown
         */
        function woocommerce_cart_item_price_html($cart_price, $values, $cart_item_key){
            global $woocommerce;
            $product = new \WC_Product($values['product_id']);

            $cartItem   = $woocommerce->cart->get_cart_item($cart_item_key);
            
            if(isset($cartItem['simulator_id'])){
                $calculatorId   		= $cartItem['simulator_id'];
                $fieldsData     		= $cartItem['simulator_fields_data'];
                $price                  = $this->calculatorHelper->calculate_price($values['product_id'], $fieldsData);
                $calculator             = $this->calculatorModel->get($calculatorId);
                $simulatorFieldsIds     = $this->calculatorHelper->get_simulator_fields($calculator->id);
                $simulatorFields        = $this->fieldHelper->get_fields_by_ids($simulatorFieldsIds);
                    
                return $price;

            }
            
            return $cart_price;
        }
        
        /*
         * Eseguito nella visualizzazione del sotto totale del prodotto nel carrello
         */
        function woocommerce_cart_product_subtotal($product_subtotal, $product, $quantity, $cart_object){
            
            /* Woo Discount Rules: Does it make sense to create a checkbox in Settings for this? */
            //$this->cartHelper->updateCartByCartObject($cart_object);
            
            return $product_subtotal; 
        }
        
        /*
         * Eseguito all'aggiunta di un prodotto nel carrello
         * 
         * |Imposto la quantità sul carrello|
         * $woocommerce->cart->set_quantity($cart_item_key, 100, true);
         * 
         * |Ricalcola i totali del carrello|
         * $woocommerce->cart->calculate_totals();
         */
	public function add_to_cart_callback($cartItemKey, $productId, $quantity, $variationId, $variation, $cartItem){
                global $woocommerce;
                
                $calculator = $this->calculatorHelper->get_simulator_for_product($productId);
                
                if(!empty($calculator)){
                                        
                    $simulator_fields_ids = $this->calculatorHelper->get_simulator_fields($calculator->id);
                    $fields = $this->fieldHelper->get_fields_by_ids($simulator_fields_ids);
                    
                    if(!empty($calculator->overwrite_quantity)){
                        $data           = $cartItem['simulator_fields_data'];
                        $product        = new \WC_Product($productId);
                        
                        $quantity       = $this->calculatorHelper->getCalculatorQuantity($calculator, $product, $data);

                        $woocommerce->cart->set_quantity($cartItemKey, $quantity, true);
                    }
                        
                    /*
                     * controllo se ci sta un problema su i campi dell calcolatore,
                     * in caso di si, rimuovo il prodotto dal carrello
                     */
                    foreach($fields as $simulatorKey => $simulatorField) {

                        if ($simulatorField == null) {
                            WC()->cart->remove_cart_item($cart_item_key);

                        }
                    }


                }
	}
	
        /*
         * Adding to the calculator cart item the data of the calculator
         */
        public function woocommerce_add_cart_item_data($cartItemData, $productId, $variationId, $quantity){
            
            $calculator     = $this->calculatorHelper->get_simulator_for_product($productId);
            
            if(!empty($calculator)){
                $requestFields  = $this->calculatorHelper->getFieldsFromRequest($productId, $calculator, false, true);

                /* Calcolo i valori di output */
                $this->calculatorHelper->calculate_price($productId, $requestFields['data'], false, $calculator->id, $outputFieldsData);

                return array_merge($cartItemData, array(
                    'simulator_id'              => $calculator->id,
                    'simulator_fields_data'     => $requestFields['data'],
                    'output_fields_data'        => $outputFieldsData,
                ));
            }
            
            return $cartItemData;
                    
        }
        
        /*
         * Eseguito prima di effettuare il calcolo del totale in cart/checkout
         * Permette di calcolare e cambiare il peso per ogni prodotto
         */
	public function woocommerce_before_calculate_totals($cart_object){
            
            if (sizeof($cart_object->cart_contents ) > 0) {
                foreach ($cart_object->cart_contents as $cartItemKey => $cartItem) {
                    $productId      = $cartItem['product_id'];
                    $product        = new \WC_Product($productId);
                    $calculator     = $this->calculatorHelper->get_simulator_for_product($productId);

                     /* E' un calcolatore */
                    if(!empty($calculator)){
                        $calculatorFieldsData    = $cartItem['simulator_fields_data'];
                        $this->calculatorHelper->calculate_price($productId, $calculatorFieldsData, false, $calculator->id, $outputResults);
                                                
                        /* E' stato impostato il campo di overwrite per il peso */
                        if(!empty($calculator->overwrite_weight)){
                            $cartItem['data']->set_weight($outputResults[$calculator->overwrite_weight]);
                        }
                        
                        /* E' stato impostato il campo di overwrite per la lunghezza */
                        if(!empty($calculator->overwrite_length)){ 
                            $cartItem['data']->set_length($outputResults[$calculator->overwrite_length]);
                        }
                        
                        /* E' stato impostato il campo di overwrite per la larghezza */
                        if(!empty($calculator->overwrite_width)){ 
                            $cartItem['data']->set_width($outputResults[$calculator->overwrite_width]);
                        }
                        
                        /* E' stato impostato il campo di overwrite per l'altezza */
                        if(!empty($calculator->overwrite_height)){ 
                            $cartItem['data']->set_height($outputResults[$calculator->overwrite_height]);
                        }

                        /*
                        $cartItem['data']->apply_changes();
                         * 
                         */
                    }
                }
            }
        
            $this->cartHelper->updateCartByCartObject($cart_object);

            /* Woo Discount Rules: Does it makes sense to add a checkbox in Settings about this? */
            remove_action('woocommerce_before_calculate_totals', array($this, 'woocommerce_before_calculate_totals'), 10);
	}

        /*
         * Funzione richiamata via Ajax per il calcolo in real-time del prezzo
         */
	public function ajax_callback(){
            $this->pluginHelper->ajaxCallback();
	}
        
        /*
         * Visualizzazione del simulatore nella scheda prodotto
         */
	public function product_meta_end(){
            echo $this->productHelper->productPage(get_the_ID());
	}

        /*
         * Aggiunge una voce al menù di WooCommerce
         */
	public function register_submenu_page() {
    		add_submenu_page('woocommerce', 
                        $this->plugin_label, 
                        $this->plugin_label, 
                        'manage_woocommerce', 
                        $this->plugin_code, 
                        array($this, 'submenu_callback')
                        ); 
	}

        /*
         * Show the back-end of the plugin
         */
	public function submenu_callback() {
            echo $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller');
	}


    /**
     * Woocommerce hook
     *
     * Used to add a custom tab in the product post page || product edit page
     *
     * @param $tabs, the default tabs in the product post page || product edit page
     * @return mixed
     */
    public function wpc_custom_product_tabs( $tabs) {

        $tabs['calculator'] = array(
            'label'    => 'Calculator',
            'target'   => 'calculator_product_data',
            'priority' => 51,
        );

        return $tabs;

    }

    /**
     * Woocommerce hook
     *
     * A hook to be able to add html elements when entered inside the specific tab
     * in the product post page || product edit page
     *
     * @return void
     */
    public function wpc_product_data_panel(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'calculator', 'customTabProductPage');

    }


    /**
     *Function called from ajax to add a new calculator to a product from the product post page
     *
     * @return void
     */
    public function ajax_attach_calculator(){
        $post = $this->wsf->getPost();

        $productId = $this->wsf->requestValue('id');
        $simulatorId = $this->wsf->requestValue('simulatorid');

        $this->calculatorHelper->addAjaxProductToCalculator($productId,$simulatorId,$post['selectedCalculatorProducts']);

    }

    /**
     *Function called from ajax to remove the calculator from a product in the product post page
     *
     * @return void
     */
    public function ajax_remove_calculator(){
        $productId = $this->wsf->requestValue('id');
        $this->calculatorHelper->removeAjaxProductToCalculator($productId);

    }
        

}
