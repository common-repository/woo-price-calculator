<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

namespace AWSPriceCalculator\Helper;

/*AWS_PHP_HEADER*/

use WSF\Helper\FrameworkHelper;

class ProductHelper {
    
    var $wsf;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
        $this->calculatorHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        $this->themeHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'ThemeHelper', array($this->wsf));
        $this->ecommerceHelper      = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));
    }
    
    /*
     * Check if a calculator has been assigned to the product page
     */
    public function isProductPageCalculator(){
        if(!is_product()){
            return false;
        }
        
        $productId      = get_the_ID();
        $calculator     = $this->calculatorHelper->get_simulator_for_product($productId);
        
        if(empty($calculator)){
            return false;
        }
        
        return true;
    }

    public function productPage($productId){
        $simulator              = $this->calculatorHelper->get_simulator_for_product($productId);
        $calculatorThemePath    = $this->calculatorHelper->getThemePath($simulator);
        
        $displayCalculator      = apply_filters('awspc_filter_display_calculator', $simulator, $productId);
        
        if($displayCalculator === false){
            return "";
        }

        return $this->getCalculatorThemeRender($productId, $simulator, $calculatorThemePath);
    }
    
    /*
     * Visualizzazione del simulatore nella scheda prodotto
     */
    public function getCalculatorThemeRender($productId, $simulator, $themePath = null, $otherViewParams = array()){
        $product        = $this->ecommerceHelper->getProductById($productId);

        if(!empty($simulator)){           
            $simulator_fields_ids   = $this->calculatorHelper->get_simulator_fields($simulator->id);
            $simulator_fields       = $this->fieldHelper->get_fields_by_ids($simulator_fields_ids);
            
            $outputResults          = null;
            $conditionalLogic       = null;
            $errors                 = null;

            if($this->wsf->isPost() == true){
                $fieldValues    = $this->wsf->getPost();
                $themeValues    = $this->wsf->getPost();
            }else{
                $fieldValues    = $this->calculatorHelper->getFieldsDefaultValue($simulator, true);
                $themeValues    = array();
            }
            
            $price  = $this->calculatorHelper->calculate_price(
                    $productId, 
                    $fieldValues, 
                    true, 
                    $simulator->id, 
                    $outputResults, 
                    $conditionalLogic,
                    true,
                    $errors,
                    $priceRaw,
                    "product"
            );
            
            /* Hide errors on Product Page at startup time */
            if($simulator->hide_startup_fields_errors == true){
                $errors     = array();
            }
                            
            /*
             * QUESTA PARTE QUI E MANTENUTA SOLO PER COMPATIBILITA DEI TEMI CUSTOM
             */
            $fields = array();
            foreach($simulator_fields as $key => $v){
                $options    = json_decode($v->options, true);
                $v_id       = $this->fieldHelper->getFieldName($v->id);

                $fields[$v_id] = array(
                    'id'            => $v_id,
                    'label_id'      => $this->fieldHelper->getFieldName("label_{$v->id}"),
                    'label_name'    => $this->wsf->userTrans($v->label),
                    'field_id'      => $this->fieldHelper->getFieldName("input_{$v->id}"),
                    'type'          => $v->type,
                    'class'         => $this->fieldHelper->getFieldName($v->type) 
                );

                if($v->type == "checkbox"){
                        $value = $this->wsf->requestValue($v_id);
                        if($value === "on"){
                            $checked = "checked";
                        }else{
                            $checked = "";
                            if($options['checkbox']['default_status'] == 1){
                                $checked = "checked";
                            }
                        }

                        $fields[$v_id]['value']     = $checked;
                        $fields[$v_id]['html']      = '<input name="' . $v_id . '" class="' . $fields[$v_id]['class'] . '" type="checkbox" ' . $checked . '/>';
                }else if($v->type == "numeric"){
                        $value = $this->wsf->requestValue($v_id);
                        if(empty($value)){
                            $value = $this->themeHelper->getThemeFieldDefaultValue($v, $simulator);
                        }
                        $fields[$v_id]['value']     = $value;
                        $fields[$v_id]['html']      = '<input name="' . $v_id . '" class="' . $fields[$v_id]['class'] . '" type="text" value="' . $value . '" />';

                }else if($v->type == "picklist"){

                        $current_value = $this->wsf->requestValue($v_id);
                        $picklist_items = $this->fieldHelper->get_field_picklist_items($v);

                        $fields[$v_id]['html']      = '<select name="' . $v_id . '" class="' . $fields[$v_id]['class'] . '">';

                        foreach($picklist_items as $index => $item){
                            $selected       = '';
                            $label          = $this->wsf->userTrans($item['label']);

                            if($current_value == $item['id']){
                                $selected = 'selected="selected"';
                            }

                            $fields[$v_id]['html'] .= "<option value=\"{$item['id']}\" {$selected}>{$label}</option>";
                        }

                        $fields[$v_id]['value'] = $item['value'];
                        $fields[$v_id]['html'] .= '</select>';
                }else if($v->type == "text"){
                        $value = $this->wsf->requestValue($v_id);
                        if(empty($value)){
                            $value = htmlspecialchars($this->wsf->decode($options['text']['default_value']));
                        }

                        $fields[$v_id]['value']     = $value;
                        $fields[$v_id]['html']      = '<input name="' . $v_id . '" class="' . $fields[$v_id]['class'] . '" type="text" value="' . 
                                $value . 
                                '" />';
                }
            }

            /*
             * FINE PARTE DI COMPATIBILITA
             */
                        
            $defaultThemeData               = $this->themeHelper->getDefaultThemeData($simulator, $simulator_fields, $themeValues, $errors);
            
            $defaultProductView             = $this->wsf->getView('awspricecalculator', 'product/product.php', true, array(
                'product'               => $product,
                'errors'                => $errors,
                'simulator'             => $simulator,
                'data'                  => $defaultThemeData,
                'outputResults'         => $this->calculatorHelper->getOutputResultsPart($simulator, $outputResults),
                'conditionalLogic'      => $conditionalLogic,
                'otherViewParams'       => $otherViewParams,
                'price'                 => $price,
                'priceRaw'              => $priceRaw,
            ));

            $view   = "";
            
            if($themePath === null){
                $view   = $defaultProductView;
            }else{
                $view   = $this->wsf->requireFile($themePath, array(
                    'errors'                => $errors,
                    'simulator'             => $simulator,
                    'simulator_fields'      => $simulator_fields,
                    'fields'                => $fields,
                    'data'                  => $defaultThemeData,
                    'defaultView'           => $defaultProductView,
                    'otherViewParams'       => $otherViewParams,
                    'price'                 => $price,
                    'priceRaw'              => $priceRaw,
                ));
            }

            
            
            return $view . $this->wsf->getView("awspricecalculator", "product/footer_data.php", true, array(
                'product'               => $product,
                'errors'                => $errors,
                'simulator'             => $simulator,
                'simulator_fields'      => $simulator_fields,
                'fields'                => $fields,
                'data'                  => $defaultThemeData,
                'priceFormat'           => $this->ecommerceHelper->getPriceFormatForJs(),
                'imagelist_modals'      => $this->wsf->getView("awspricecalculator", 'partial/imagelist_modal.php', true, array(
                    'simulator_fields'  => $simulator_fields,
                    'fieldHelper'       => $this->fieldHelper,
                    'cartItemKey'       => null,
                    'data'              => $defaultThemeData,
                )),
                'otherViewParams'       => $otherViewParams,
                'price'                 => $price,
                'priceRaw'              => $priceRaw,
            ));

        }
    }
    
    public function getAjaxProductsTable(){
        $start                                 = $this->wsf->requestValue('start');
        $length                                = $this->wsf->requestValue('length');
        $order                                 = $this->wsf->requestValue('order');
        $search                                = $this->wsf->requestValue('search');
        $searchValue                           = $search['value'];

        //print_r($order);
        $columns                               = array(
            'id',
            'name'
        );

        $orderBy                               = $columns[$order[0]['column']];
        $orderDir                              = $order[0]['dir'];
        $rows                                  = array();

        $productsData                          = $this->ecommerceHelper->getProducts($length, $start, $orderBy, $orderDir, $searchValue);

        foreach($productsData['products'] as $product){
            if(!empty($product['id'])){
                $rows[]     = array(
                    $product['id'],
                    $product['name'],
                    "<button data-product-name=\"{$product['name']}\" data-product-id=\"{$product['id']}\" class=\"btn btn-primary select-product\">{$this->wsf->trans("aws.select_products.column.actions.select")}</button>"
                );
            }
        }

        //die("S: $start , L: $length");
        //print_r($products);

        die(json_encode(array(
            'iTotalRecords'             => $productsData['totalProducts'],
            'iTotalDisplayRecords'      => $productsData['totalProducts'],
            'sEcho'                     => $sEcho,
            'aaData'                    => $rows,
            'count'                     => $productsData['count'],
        )));
    }
    
}