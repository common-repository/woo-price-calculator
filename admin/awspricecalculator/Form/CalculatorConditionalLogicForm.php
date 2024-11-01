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

class CalculatorConditionalLogicForm {
    
    private $wsf;
    
    private $form;
    
    private $calculatorModel;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        /* MODELS */
        $this->calculatorModel = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
        
        /* HELPERS */
        $this->calculatorHelper  = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        
        $this->form[] = array(
            'name' => 'enabled'
        );
        
        $this->form[] = array(
            'name' => 'hide_fields'
        );
        
        $this->form[] = array(
            'name' => 'field_filters_json'
        );
        
        $this->form[] = array(
            'name' => 'field_filters_sql'
        );

    }
    
    public function check($record, $params = array()){
        
    }
    
    public function getForm(){
        return $this->form;
    }
    
    public function setForm($form){
        $this->form = $form;
    }
}

