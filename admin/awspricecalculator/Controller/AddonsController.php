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

class AddonsController {
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

    /**
     * Setting section.
     *
     * It is the entry point for the addons page of the plug-in.
     *
     * @return void
     */
    public function indexAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
        
        $addonsRows     = array();
        $addonsDataJson = file_get_contents("https://altoswebsolutions.com/aws_files/woopricecalculator/addons.json");
        $addonsData     = json_decode($addonsDataJson, true);

        
        
        $rowIndex       = 0;
        $columnIndex    = 0;
        foreach($addonsData as $addonRow){
            $addonsRows[$rowIndex][]    = $addonRow;
            
            $columnIndex++;
            
            if($columnIndex >= 3){
                $columnIndex    = 0;
                $rowIndex++;
            }
        }

        $this->wsf->renderView('addons/addons.php', array(
            'addonsRows'        => $addonsRows,
        ));
    }
    
        
}
