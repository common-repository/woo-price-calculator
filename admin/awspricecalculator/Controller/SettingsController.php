<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

namespace AWSPriceCalculator\Controller;

/*AWS_PHP_HEADER*/

use WSF\Helper\FrameworkHelper;

class SettingsController {
    private $wsf;
    
    private $tableHelper;
    private $calculatorHelper;

    private $fieldModel;
    private $calculatorModel;
    
    private $wooCommerceHelper;
    
    public function __construct(FrameworkHelper $wsf){
        if(!session_id()){
            session_start();
        }
        
        $this->wsf  = $wsf;
        
        $this->tableHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'TableHelper', array($this->wsf));
        
        /* MODELS */
        $this->fieldModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
        $this->calculatorModel      = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
        $this->settingsModel        = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'SettingsModel', array($this->wsf));
        
        /* HELPERS */
        $this->calculatorHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        $this->wooCommerceHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));

    }
    
    public function indexAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
        
        $settingsForm           = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'SettingsForm', array($this->wsf));
        $errors                 = array();
        $picklistItemsData      = array();
        $radioItemsData         = array();
        
        $task                   = $this->wsf->requestValue('task');
        $form                   = null;

        $values                     = $this->settingsModel->getValues();

        $form = $this->wsf->requestForm($settingsForm, array(
            'disable_ajax_price_product_page'   => $this->wsf->isset_or($values['disable_ajax_price_product_page'], ''),
            'single_product_ajax_hook_class'    => $this->wsf->isset_or($values['single_product_ajax_hook_class'], ''),
            'cart_edit_button_class'            => $this->wsf->isset_or($values['cart_edit_button_class'], ''),
            'cart_edit_button_position'         => $this->wsf->isset_or($values['cart_edit_button_position'], ''),
            'hide_alert_errors'                 => $this->wsf->isset_or($values['hide_alert_errors'], 0),
            'custom_css'                        => file_get_contents($this->wsf->getUploadPath("style/custom.css")),
        ));
        
        if($this->wsf->isPost() && $task == 'save'){
            $form                       = $this->wsf->requestForm($settingsForm);
            $errors                     = array_merge($settingsForm->check($form, array()), $errors);
                            
            if(count($errors) == 0){

                foreach($form as $key => $value){
                    if($key == "custom_css"){
                        file_put_contents($this->wsf->getUploadPath("style/custom.css"), $form['custom_css']);
                    }else{
                        $this->settingsModel->setValue($key, $value);
                    }

                }
                
                
                $this->wsf->renderView('app/form_message.php', array(
                    'message'    => $this->wsf->trans('aws.settings.form.success'),
                ));
            }
        }

        $this->wsf->renderView('settings/settings.php', array(
            'errors'                            => $errors,
            'form'                              => $form,
        ));
    }
    
        
}
