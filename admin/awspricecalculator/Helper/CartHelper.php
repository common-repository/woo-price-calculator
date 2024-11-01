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

class CartHelper {
    
    var $wsf;
    
    var $fieldHelper;
    var $wooCommerceHelper;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        /* HELPERS */
        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
        $this->calculatorHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        $this->wooCommerceHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));
        
        /* MODELS */
        $this->fieldModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
        $this->calculatorModel      = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
    }
    
    /*
     * Calcola il prezzo del prodotto e lo aggiorna nel carrello
     */
    public function updateCartByCartObject($cart_object){
        global $woocommerce;

        foreach ($cart_object->cart_contents as $cartItemKey => $cartItem){
            if(isset($cartItem['simulator_id'])){
                $productId      = $cartItem['product_id'];
                $product        = new \WC_Product($productId);
                $simulatorId    = $cartItem['simulator_id'];
                $product_price  = 0;
                $fieldsData     = $cartItem['simulator_fields_data'];
                $calculator     = $this->calculatorModel->get($simulatorId);

                if(empty($calculator)){
                    /* Probabilmente il calcolatore è stato cancellato lato admin */
                    $woocommerce->cart->remove_cart_item($cartItemKey);
                }else{
                    //echo $this->calculatorHelper->calculate_price($value['product_id'], $variant, false) . "|";
                    $product_price += $this->calculatorHelper->calculate_price($cartItem['product_id'], $fieldsData, false);
                    
                    /* A quantity field has been mapped, so I good solution is: final price = final price/quantity */
                    if(!empty($calculator->overwrite_quantity)){
                        $quantity      = $this->calculatorHelper->getCalculatorQuantity($calculator, $product, $fieldsData);
                        
                        $product_price = $product_price/$quantity;
                    }
                    
                    $cartItem['data']->set_price($product_price);
                }

                $woocommerce->cart->persistent_cart_update();
            }
        }
    }
    
}
