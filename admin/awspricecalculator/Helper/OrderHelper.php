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

class OrderHelper {
    
    var $wsf;
    
    var $fieldHelper;
    var $ecommerceHelper;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        /* HELPERS */
        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
        $this->calculatorHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        $this->ecommerceHelper      = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));
        
        /* MODELS */
        $this->fieldModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
        $this->calculatorModel      = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
    }
    
    /*
     * Calcola il prezzo del prodotto e lo aggiorna nel carrello
     */
    public function calculatorOrder($orderId){
        $simulation         = $this->calculatorModel->getSimulationByOrderId($orderId);
        $ret                = "";
                
        if(count($simulation) == 0){
            return "No simulations";
        }else{
            $simulation_data = json_decode($simulation->simulation_data, true);
            $simulators      = json_decode($simulation->simulators, true);

            foreach($simulation_data as $cart_item_key => $orderItem){
                $simulatorId        = $orderItem['simulator_id'];
                $calculator         = $this->calculatorModel->get($simulatorId);
                
                if(!empty($calculator)){
                    $product_id         = $orderItem['product_id'];
                    $product            = $this->ecommerceHelper->getProductById($product_id);
                    $calculatorType     = $this->wsf->isset_or($calculator->type, "simple");
                    $calculatorFields   = json_decode($this->wsf->isset_or($calculator->fields, "{}"), true);
                    $product_simulator  = $simulators[$simulatorId];
                    $productTitle       = $product['name'];
                    $simpleFormula      = $product_simulator['formula'];
                    $quantity           = $orderItem['quantity'];

                    $ret .= "<b>{$quantity} x {$productTitle}:</b><br/>";

                    if($calculatorType == 'simple'){
                        $ret .= "<b>Formula: {$simpleFormula}</b><br/>";
                    }else{
                        $calculatorOptions              = json_decode($this->wsf->isset_or($calculator->options, array()), true);
                        $downloadSpreadsheetUrl         = $this->wsf->adminUrl(array(
                                'controller'    => 'calculator', 
                                'action'        => 'downloadspreadsheet', 
                                'simulator_id'  => $simulatorId,
                                'raw'           => 1,
                        ));

                        $ret .= "<b>Spreadsheet: "
                                . "<a target=\"_blank\" href=\"{$downloadSpreadsheetUrl}\">"
                                    . "{$calculatorOptions['filename']}"
                                . "</a>"
                        . "</b><br/>";
                    }
                    
                    /*
                     * Campi di output
                     */
                    if(isset($orderItem['output_fields_data'])){
                        $ret .= "&emsp;&emsp;<b>Output Fields:</b><br/>";
                        foreach($orderItem['output_fields_data'] as $fieldKey => $fieldValue){

                            $field      = $this->fieldModel->get_field_by_id($fieldKey);

                            if(!empty($fieldValue)){
                                if(empty($field->label)){
                                    $label = "[FIELD DELETED]";
                                }else{
                                    $label = $field->label;
                                }

                                $htmlElement      = $this->getReviewElement($field, $fieldValue);

                                $ret .= "&emsp;&emsp;&emsp;&emsp;{$label} [{$fieldKey}]: {$htmlElement}<br/>";
                            }
                        }
                    }
                    
                    $ret .= "<br/>";
                    
                    /*
                     * Campi di input
                     */
                    $ret .= "&emsp;&emsp;<b>Input Fields:</b><br/>";
                    foreach($orderItem['simulator_fields_data'] as $field_key => $field_value){

                        $field_id = str_replace("aws_price_calc_", "", $field_key);
                        $field = $this->fieldModel->get_field_by_id($field_id);

                        if(!empty($field_value)){
                            if(empty($field->label)){
                                $label = "[FIELD DELETED]";
                            }else{
                                $label = $field->label;
                            }

                            $htmlElement      = $this->getReviewElement($field, $field_value);

                            $ret .= "&emsp;&emsp;&emsp;&emsp;{$label} [{$field_key}]: {$htmlElement}<br/>";
                        }
                    }


                    $ret .= "<br/>";
                }
                

            }
            
            return $ret;
        }
    }
    
    
    /*
     * Ritorna l'elemento per il review dell'ordine
     */
    public function getReviewElement($field, $value, $orderDetails = false){
        
        if($field->type == "numeric"){
            
            if($value == ""){
                return "0";
            }else{
                return $value;
            }
            
        }else if($field->type == "checkbox"){
            if($value === "on"){
                return $this->wsf->userTrans("Yes");
            }else{
                return $this->wsf->userTrans("No");
            }
            
        }else if($field->type == "picklist"){
            $picklistItems = $this->fieldHelper->get_field_picklist_items($field);

            foreach($picklistItems as $index => $item){
                if($value == $item['id']){
                    return $this->getItemLabel($item, $orderDetails);
                }
            }
        }else if($field->type == "radio"){
            $radioItems   = $this->fieldHelper->getFieldItems('radio', $field);

            foreach($radioItems as $index => $item){
                if($value == $item['id']){
                    return $this->getItemLabel($item, $orderDetails);
                }
            }
        }else if($field->type == "imagelist"){
            $imagelistItems   = $this->fieldHelper->getFieldItems('imagelist', $field);

            foreach($imagelistItems as $index => $item){
                if($value == $item['id']){
                    return $this->getItemLabel($item, $orderDetails);
                }
            }
        }else{
            if($value == ""){
                /* This will make the field not disappear when it's empty */
                return $this->wsf->mixTrans("element.not_set");
            }else{
                return $value;
            }
        }
    }
    
    /*
     * Get the label of an item element (Picklist, Radio buttons)
     * 
     * If $orderDetails = true, additionals order information will be added
     */
    public function getItemLabel($item, $orderDetails = false){
        $label      = $this->wsf->userTrans($item['label']);

        if($orderDetails == true && !empty($item['order_details'])){
            $label  .= " [{$item['order_details']}]";
        }
        
        return $label;
    }
}
