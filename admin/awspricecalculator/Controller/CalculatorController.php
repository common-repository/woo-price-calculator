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

class CalculatorController {
    private $wsf;
    private $db;
   
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
        $this->fieldModel               = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
        $this->calculatorModel          = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
       
        /* HELPERS */
        $this->themeHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'ThemeHelper', array($this->wsf));
        $this->calculatorHelper     = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        $this->wooCommerceHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'EcommerceHelper', array($this->wsf));
        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
        $this->pluginHelper         = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'PluginHelper', array($wsf));
       
        $this->MAX_EMPTY_COLUMNS    = 100;
        $this->MAX_EMPTY_ROWS       = 50;
    }
   
    public function indexAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
       
        if($this->wsf->getLicense() != 0){
            $loadCalculatorUrl      = $this->wsf->adminUrl(array('controller' => 'calculator', 'action' => 'loader'));
        }else{
            $loadCalculatorUrl      = "#load_calculator";
        }
       
        $this->wsf->renderView('calculator/list.php', array(
            'list_header'    => array(
                'name'              => $this->wsf->trans('wpc.calculator.list.name'),
                'description'       => $this->wsf->trans('wpc.calculator.list.description'),
                'type'              => $this->wsf->trans('wpc.calculator.list.type'),
                'actions'           => $this->wsf->trans('wpc.actions'),
            ),
            'list_rows'             => $this->calculatorModel->get_list(),
           
            'loadCalculatorUrl'     => $loadCalculatorUrl,
        ));
       
       
    }
   
    public function loaderAction(){
        $setToken       = $this->wsf->requestValue('set_token');
        $calculatorId   = $this->wsf->requestValue('calculator_id');
       
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');

        if(!empty($setToken)){
            $token      = $setToken;
        }else{
            $token      = $this->calculatorHelper->generateFileName();
        }
       
        $this->wsf->renderView('calculator/load.php', array(
            'calculator_id' => $calculatorId,
            'token'         => $token,
        ));

    }
   
   
   
    public function deleteAction(){
        $id = $this->wsf->requestValue('id');
       
        $this->calculatorModel->delete($id);
       
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'calculator', 'index');
    }
   
    public function addAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
       
        $id             = $this->wsf->requestValue('id', null);
        $calculatorForm = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'CalculatorForm', array($this->wsf));
        $form           = $this->wsf->requestForm($calculatorForm);
       
        $task           = $this->wsf->requestValue('task');
        $mapping        = $this->wsf->requestValue('mapping');
        $type           = $this->wsf->requestValue('type');
        $loader_fields  = array();
       
        if(empty($type)){
            $type = "simple";
        }
       
        $fields                 = $this->fieldModel->get_field_list('input');
        $outputFields           = $this->fieldModel->get_field_list('output');
       
        $productCategories      = $this->wooCommerceHelper->getProductCategories();
       
        $themes                 = $this->themeHelper->getThemes();
       
        $errors                 = array();
        $warnings               = array();
       
        if($this->wsf->isPost() && $task == 'calculator'){
            if($type == "simple" || ($type == "excel" && $mapping != 1)){

                if($type == "excel"){
                    $form['options']         = $_SESSION['woo-price-calculator']['admin']['loader_fields'];
                }
               
                /* In questo modo riesco a prendere l'ordine dei campi */
                if(!empty($form['field_orders'])){
                    $form['fields']         = explode(",", $form['field_orders']);
                }
           
                if(!empty($form['output_field_orders'])){
                    $form['output_fields']         = explode(",", $form['output_field_orders']);
                }
               
                $errors         = $calculatorForm->check($form, array('id' => $id));
                $warnings       = $calculatorForm->checkWarnings($form);
                
                if(count($errors) == 0){

                    $id     = $this->calculatorModel->save($form, $id);

                    
                    //checking if the record was created in the database, if not display an error message
                    if($id == 0){

                        $this->wsf->renderView('app/form_message.php', array(
                            'type'          => 'danger',
                            'message'       => $this->wsf->trans('database_problem'),
                        ));


                    }else {

                        $this->wsf->renderView('app/form_message.php', array(
                            'message'   => $this->wsf->trans('aws.calculator.form.success'),
                            'url'       => $this->wsf->adminUrl(array('controller' => 'calculator')),
                        ));
                    }

                }
            }
        }else{          
            if($type == "excel"){
            }
       
           
        }

        $this->wsf->renderView('calculator/calculator.php', array(
            'id'                        => $id,
            'title'                     => $this->wsf->trans('Add'),
            'action'                    => 'add',
           
            'ecommerceHelper'           => $this->wooCommerceHelper,
            'fieldHelper'               => $this->fieldHelper,
           
            'errors'                    => $errors,
            'warnings'                  => $warnings,
           
            'form'                      => $form,

            'calculableFields'          => $this->fieldHelper->getCalculableFieldList('input'),
            'fields'                    => $fields,
           
            'orderedFields'             => $this->calculatorHelper->orderFields($form['fields'], 'input'),
            'outputOrderedFields'       => $this->calculatorHelper->orderFields($form['output_fields'], 'output'),
           
            'products'                  => $this->wooCommerceHelper->getProductsByIds($form['products']),
            'productCategories'         => $productCategories,
           
            'themes'                    => $themes,
           
            'loader_fields'             => $loader_fields,
           
            'type'                      => $type,
        ));

    }
   
    public function editAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
       
        $calculatorForm = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'CalculatorForm', array($this->wsf));
       
        $id                     = $this->wsf->requestValue('id');
        
        /* No calculator to edit => Redirect to calculators list */
        if(empty($id)){
            $this->wsf->redirect($this->wsf->adminUrl(array(
                'controller' => 'calculator'
            )));
        }
        
        $task                   = $this->wsf->requestValue('task');
        $fields                 = $this->fieldModel->get_field_list('input');
       
        $productCategories      = $this->wooCommerceHelper->getProductCategories();
       
        $themes                 = $this->themeHelper->getThemes();
        $calculator             = $this->calculatorModel->get($id);
        $calculatorType         = $this->wsf->isset_or($calculator->type, "simple");
       
        $calculatorFields       = json_decode($this->wsf->isset_or($calculator->fields, "{}"), true);
        $calculatorOutputFields = json_decode($this->wsf->isset_or($calculator->output_fields, "{}"), true);
        $calculatorOptions      = json_decode($this->wsf->isset_or($calculator->options, "{}"), true);
           
        $form = $this->wsf->requestForm($calculatorForm, array(
            'name'                          => $calculator->name,
            'description'                   => $this->wsf->isset_or($calculator->description, ""),
            'fields'                        => $calculatorFields,
            'output_fields'                 => $calculatorOutputFields,
            'products'                      => json_decode($this->wsf->isset_or($calculator->products, "{}"), true),
            'product_categories'            => json_decode($this->wsf->isset_or($calculator->product_categories, "{}"), true),
            'overwrite_quantity'            => $this->wsf->isset_or($calculator->overwrite_quantity, null),
            'overwrite_weight'              => $this->wsf->isset_or($calculator->overwrite_weight, null),
            'overwrite_length'              => $this->wsf->isset_or($calculator->overwrite_length, null),
            'overwrite_width'               => $this->wsf->isset_or($calculator->overwrite_width, null),
            'overwrite_height'              => $this->wsf->isset_or($calculator->overwrite_height, null),
            'options'                       => $calculatorOptions,
            'formula'                       => $this->wsf->isset_or($calculator->formula, ""),
            'force_to_show_price_on_errors' => $this->wsf->isset_or($calculator->force_to_show_price_on_errors, 0),
            'hide_startup_fields_errors'    => $this->wsf->isset_or($calculator->hide_startup_fields_errors, 0),
            'redirect'                      => $this->wsf->isset_or($calculator->redirect, 0),
            'empty_cart'                    => $this->wsf->isset_or($calculator->empty_cart, 0),
            'type'                          => $calculatorType,
            'theme'                         => $this->wsf->isset_or($calculator->theme, ""),
            'system_created'                => $this->wsf->isset_or($calculator->system_created, 0),
        ));
           
        $errors         = array();
        $warnings       = array();
       
        if($this->wsf->isPost() && $task == 'calculator'){
            $form               = $this->wsf->requestForm($calculatorForm);
            
            /* In questo modo riesco a prendere l'ordine dei campi */
            if(!empty($form['field_orders'])){
                $form['fields']     = explode(",", $form['field_orders']);
            }
           
            if(!empty($form['output_field_orders'])){
                $form['output_fields']     = explode(",", $form['output_field_orders']);
            }
            
            /* Non modifico le informazioni delle opzioni */
            if($calculatorType == 'excel'){
                $form['options']     = $calculatorOptions;
            }
                    
            $errors             = $calculatorForm->check($form, array('id' => $id));
            $warnings           = $calculatorForm->checkWarnings($form);
            
            if(count($errors) == 0){            
                $this->calculatorModel->save($form, $id);

                $calculator                 = $this->calculatorModel->get($id);

                $form['fields']             = json_decode($calculator->fields, true);
                $form['output_fields']      = json_decode($calculator->output_fields, true);

                $this->wsf->renderView('app/form_message.php', array(
                    'message'       => $this->wsf->trans('aws.calculator.form.success'),
                    'url'           => $this->wsf->adminUrl(array('controller' => 'calculator'))
                ));
            }

        }

        $this->wsf->renderView('calculator/calculator.php', array(
            'id'                        => $id,
            'title'                     => $this->wsf->trans('Edit'),
            'action'                    => 'edit',
            'form'                      => $form,
           
            'ecommerceHelper'           => $this->wooCommerceHelper,
            'fieldHelper'               => $this->fieldHelper,
            
            'errors'                    => $errors,
            'warnings'                  => $warnings,

            'calculableFields'          => $this->fieldHelper->getCalculableFieldList('input'),
            'fields'                    => $fields,
           
            'orderedFields'             => $this->calculatorHelper->orderFields($form['fields'], 'input'),
            'outputOrderedFields'       => $this->calculatorHelper->orderFields($form['output_fields'], 'output'),
           
            'products'                  => $this->wooCommerceHelper->getProductsByIds($form['products']),
            'productCategories'         => $productCategories,
           
            'themes'                    => $themes,

            'loader_fields'             => $fields,
           
            'type'                      => $calculator->type,

        ));

    }
   
    function uploadSpreadsheetAction(){
        $targetPath     = $this->wsf->getUploadPath('docs');
        $token          = $this->wsf->requestValue('token');
        $calculatorId   = $this->wsf->requestValue('calculator_id');
       
        if (!empty($_FILES)) {
            $tempFile   = $_FILES['file_upload']['tmp_name'];
            $filename   = $_FILES['file_upload']['name'];

            // Validate the file type
            $fileTypes = array('xls','xlsx', 'ods'); // File extensions
            $fileParts = pathinfo($filename);

            $targetFile = rtrim($targetPath,'/') . '/' . $token;

            if (in_array($fileParts['extension'],$fileTypes)) {
                    move_uploaded_file($tempFile, $targetFile);
                    echo $token;
            } else {
                    echo 'Invalid file type.';
            }
        }

        $redirectUrl    = $this->wsf->adminUrl(array(
            'controller'    => 'calculator',
            'action'        => 'loadersheet',
            'file'          => $token,
            'filename'      => urlencode($filename),
            'calculator_id' => $calculatorId,
        ));
       
        header("location: {$redirectUrl}");
        exit(-1);
    }
   
    public function downloadSpreadsheetAction(){
        $simulatorId          = $this->wsf->requestValue('simulator_id');

        $this->calculatorHelper->downloadSpreadsheet($simulatorId);
    }
   
    public function conditionalLogicAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
       
        $id                         = $this->wsf->requestValue('id');
        $task                       = $this->wsf->requestValue('task');
        $calculator                 = $this->calculatorModel->get($id);
        $calculatorConditionalLogic = $this->calculatorModel->getConditionalLogic($id);
        $calculatorFieldsIds        = $this->calculatorHelper->get_simulator_fields($id);
        $fields                     = $this->fieldHelper->get_fields_by_ids($calculatorFieldsIds);
       
        $calculatorConditionalLogicForm         = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'CalculatorConditionalLogicForm', array($this->wsf));
               
        $form = $this->wsf->requestForm($calculatorConditionalLogicForm, array(
            'enabled'                  => $this->wsf->isset_or($calculatorConditionalLogic['enabled'], 0),
            'hide_fields'              => $this->wsf->isset_or($calculatorConditionalLogic['hide_fields'], array()),
            'field_filters_json'       => $this->wsf->isset_or($calculatorConditionalLogic['field_filters_json'], ""),
            'field_filters_sql'        => $this->wsf->isset_or($calculatorConditionalLogic['field_filters_sql'], ""),

        ));
       
        if($this->wsf->isPost() && $task == 'save'){
            $form                       = $this->wsf->requestForm($calculatorConditionalLogicForm);
           
            /* Converto i filtri JSON per il salvataggio, pulendo */
            foreach($form['field_filters_json'] as $fieldKey => $filterValue){
                $form['field_filters_json'][$fieldKey]  = json_decode(stripslashes($filterValue), true);
            }
           
            /* Converto i filtri SQL per il salvataggio, pulendo */
            foreach($form['field_filters_sql'] as $fieldKey => $filterValue){
                $form['field_filters_sql'][$fieldKey]  = stripslashes($filterValue);
            }

            $this->calculatorModel->saveConditionalLogic($form, $id);
           
            $this->wsf->renderView('app/form_message.php', array(
                'message'    => $this->wsf->trans('aws.conditional_logic.form.success'),
                'url'        => $this->wsf->adminUrl(array(
                    'controller' => 'calculator',
                ))
            ));
        }
       
        $filters                = array();
        foreach($fields as $field){
            $filters[$field->id]                = $this->fieldHelper->convertFieldsToFilters($fields, $field->id);
        }
       
        $this->wsf->renderView('calculator/conditional_logic.php', array(
            'id'                        => $id,
            'calculator'                => $calculator,
           
            'form'                      => $form,
           
            'warnings'                  => array(),
            'errors'                    => array(),
           
            'fields'                    => $fields,
           
            'filters'                   => $filters,
           
            'fieldHelper'               => $this->fieldHelper,
        ));
    }
        
    /* Importa un calcolatore */
    
    /* Esporta un calcolatore */
    public function exportAction(){
        $id                         = $this->wsf->requestValue('id');
        $calculator                 = $this->calculatorModel->get($id);
        
        $filename                   = "calculator_{$id}.zip";
        $filePath                   = $this->calculatorHelper->export($calculator, $filename);
        
        
        header("Content-type: application/zip"); 
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-length: " . filesize($filePath));
        header("Pragma: no-cache"); 
        header("Expires: 0"); 
        readfile($filePath);
        
        
        die();
    }

    /**
     * Generate custom tab layout in the product admin page
     * in the custom tab section
     *
     * @return void
     */
    public function customTabProductPageAction(){

        $selectedSimulator = $this->calculatorHelper->get_simulator_for_product(get_the_ID());

        $allCalculators = $this->calculatorModel->get_list();

        //Generate options for the select input field
        $selectCalculatorArray[''] = 'Please select';
        foreach ($allCalculators as $calculator){
            $allArrayCalculators[$calculator->id] = (array)$calculator;
            $selectCalculatorArray[$calculator->id] = $calculator->name;
        }

        $ajaxUrl = $this->wsf->getAjaxUrl();
        $productId = get_the_ID();
        $availableCalculators = json_encode($allArrayCalculators);
        $resourceUrl = $this->wsf->getResourcesUrl('/assets/js/postPage.js');


        $this->wsf->renderView('product/edit_page.php', array(
            'selectedSimulator'     => $selectedSimulator,
            'selectCalculatorArray' => $selectCalculatorArray,
            'ajaxUrl'               => $ajaxUrl,
            'productId'             => $productId,
            'availableCalculators'  => $availableCalculators,
            'resourceUrl'           => $resourceUrl,

        ));

    }

}