<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

namespace AWSPriceCalculator\Form;

/*AWS_PHP_HEADER*/

use WSF\Helper\FrameworkHelper;

class SettingsForm {
    
    private $wsf;
    
    private $form;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
               
        $this->form[] = array(
            'name' => 'custom_css'
        );
        
        $this->form[] = array(
            'name' => 'cart_edit_button_class',
        );
     
        $this->form[] = array(
            'name' => 'cart_edit_button_position',
        );
        
        $this->form[] = array(
            'name'  => 'hide_alert_errors',
        );
        
        $this->form[] = array(
            'name' => 'single_product_ajax_hook_class',
        );
        
        $this->form[] = array(
            'name' => 'disable_ajax_price_product_page',
        );
    }
    
    public function check($record, $params = array()){

        $errors = array();

        return $errors;
    }
    
    public function getForm(){
        return $this->form;
    }
    
    public function setForm($form){
        $this->form = $form;
    }
}

