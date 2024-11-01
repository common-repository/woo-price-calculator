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

class FieldController {
    var $wsf;

    var $db;

    public function __construct(FrameworkHelper $wsf){
        $this->wsf = $wsf;
        $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));

        $this->calculatorModel      = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
        $this->fieldModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
        $this->regexModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'RegexModel', array($this->wsf));

        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
        $this->tableHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'TableHelper', array($this->wsf));

        $currentAction              = $this->wsf->getCurrentActionName();

    }

    public function indexAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');

        $this->wsf->renderView('fields/list.php', array(
            'list_header'    => array(
                'label'         => $this->wsf->trans('wpc.field.list.label'),
                'name'          => $this->wsf->trans('wpc.field.list.name'),
                'mode'          => $this->wsf->trans('wpc.field.list.mode'),
                'type'          => $this->wsf->trans('wpc.field.list.type'),
                'description'   => $this->wsf->trans('wpc.field.list.description'),
                'actions'       => $this->wsf->trans('wpc.actions'),
            ),
            'list_rows'      => $this->fieldModel->get_field_list(),
        ));
    }

    public function deleteAction(){
        $id = $this->wsf->requestValue('id');

        $error  = $this->fieldHelper->checkFieldUsage($id);

        if(!empty($error)){
            $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
            $this->wsf->renderView('fields/field_error.php', array(
                'error'     => $error,
            ));
        }else{
            $this->fieldModel->delete($id);
        }

        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'field', 'index');
    }

    public function formAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');

        $fieldForm              = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'FieldForm', array($this->wsf));
        $errors                 = array();
        $picklistItemsData      = array();
        $radioItemsData         = array();
        $imageListItemsData     = array();

        $id                     = $this->wsf->requestValue('id');
        $cloneId                = $this->wsf->requestValue('clone_id');
        $task                   = $this->wsf->requestValue('task');
        $form                   = null;

        $fieldTypes             = $this->fieldHelper->getFieldTypes();
        
        if(!empty($id)){
            $record                     = $this->fieldModel->get_field_by_id($id);
            $options                    = json_decode($record->options, true);
        }

        if(!empty($cloneId)){
            $record                     = $this->fieldModel->get_field_by_id($cloneId);
            $options                    = json_decode($record->options, true);
        }


        $form = $this->wsf->requestForm($fieldForm, array(
            'label'                             => $this->wsf->isset_or($record->label, ''),
            'short_label'                       => $this->wsf->isset_or($record->short_label, ''),
            'description'                       => $this->wsf->isset_or($record->description, ''),
            'mode'                              => $this->wsf->isset_or($record->mode, 'input'),
            'type'                              => $this->wsf->isset_or($record->type, ''),
            'check_errors'                      => $this->wsf->isset_or($record->check_errors, 'always'),
            'required'                          => $this->wsf->isset_or($record->required, false),
            'required_error_message'            => $this->wsf->isset_or($record->required_error_message, ''),
            'text_after_field'                  => $this->wsf->isset_or($record->text_after_field, ''),
            
            'hide_field_product_page'           => $this->wsf->isset_or($record->hide_field_product_page, 0),
            'hide_field_cart_if_empty'          => $this->wsf->isset_or($record->hide_field_cart_if_empty, 0),
            'hide_field_checkout_if_empty'      => $this->wsf->isset_or($record->hide_field_checkout_if_empty, 0),
            'hide_field_cart'                   => $this->wsf->isset_or($record->hide_field_cart, 0),
            'hide_field_checkout'               => $this->wsf->isset_or($record->hide_field_checkout, 0),
            'hide_field_order'                  => $this->wsf->isset_or($record->hide_field_order, 0),
            
            'checkbox_check_value'              => $this->wsf->isset_or($options['checkbox']['check_value'], ''),
            'checkbox_uncheck_value'            => $this->wsf->isset_or($options['checkbox']['uncheck_value'], ''),

            'items_list_id'                     => $this->wsf->isset_or($options['items_list_id'], 1),
            'picklist_items'                    => $this->wsf->isset_or($options['picklist_items'], ""),

            /* Radio */
            'radio_image_width'                 => $this->wsf->isset_or($options['radio']['radio_image_width'], ""),
            'radio_image_height'                => $this->wsf->isset_or($options['radio']['radio_image_height'], ""),
            'radio_items'                       => $this->wsf->isset_or($options['radio']['radio_items'], ""),

            /* Date / Time / DateTime */
            'date_format'                       => $this->wsf->isset_or($options['date']['date_format'], ""),
            'time_format'                       => $this->wsf->isset_or($options['date']['time_format'], ""),
            'datetime_format'                   => $this->wsf->isset_or($options['date']['datetime_format'], ""),
            'datetime_default_value'            => $this->wsf->isset_or($options['date']['default_value'], ""),
            
            /* Image List */
            'imagelist_field_image_width'       => $this->wsf->isset_or($options['imagelist']['imagelist_field_image_width'], ""),
            'imagelist_field_image_height'      => $this->wsf->isset_or($options['imagelist']['imagelist_field_image_height'], ""),
            'imagelist_popup_image_width'       => $this->wsf->isset_or($options['imagelist']['imagelist_popup_image_width'], ""),
            'imagelist_popup_image_height'      => $this->wsf->isset_or($options['imagelist']['imagelist_popup_image_height'], ""),
            'imagelist_items'                   => $this->wsf->isset_or($options['imagelist']['imagelist_items'], ""),

            'checkbox_default_status'           => $this->wsf->isset_or($options['checkbox']['default_status'], 0),

            'numeric_default_value'             => $this->wsf->isset_or($options['numeric']['default_value'], ""),
            'numeric_max_value'                 => $this->wsf->isset_or($options['numeric']['max_value'], ""),
            'numeric_max_value_error'           => $this->wsf->isset_or($options['numeric']['max_value_error'], ""),
            'numeric_min_value'                 => $this->wsf->isset_or($options['numeric']['min_value'], ""),
            'numeric_min_value_error'           => $this->wsf->isset_or($options['numeric']['min_value_error'], ""),
            'numeric_decimals'                  => $this->wsf->isset_or($options['numeric']['decimals'], ""),
            'numeric_decimal_separator'         => $this->wsf->isset_or($options['numeric']['decimal_separator'], ""),
            'numeric_thousand_separator'        => $this->wsf->isset_or($options['numeric']['thousand_separator'], ""),

            'output_numeric_decimals'           => $this->wsf->isset_or($options['numeric']['decimals'], ""),
            'output_numeric_decimal_separator'  => $this->wsf->isset_or($options['numeric']['decimal_separator'], ""),
            'output_numeric_thousand_separator' => $this->wsf->isset_or($options['numeric']['thousand_separator'], ""),

            'text_default_value'                => $this->wsf->isset_or($options['text']['default_value'], ""),
            'text_regex'                        => $this->wsf->isset_or($options['text']['regex'], ""),
            'text_regex_error'                  => $this->wsf->isset_or($options['text']['regex_error'], ""),

            'system_created'                    => $this->wsf->isset_or($record->system_created, 0),
        ));


        if($this->wsf->isPost() && $task == 'field_form'){
            $form                       = $this->wsf->requestForm($fieldForm);

            $errors                     = array_merge($fieldForm->check($form, array('id' => $id)), $errors);

            $picklistItemsData          = json_decode($this->wsf->requestValue('picklist_items'), true);
            $radioItemsData             = json_decode($this->wsf->requestValue('radio_items'), true);
            $imageListItemsData         = json_decode($this->wsf->requestValue('imagelist_items'), true);

            if(count($errors) == 0){
                $insertId     = $this->fieldModel->save($form, $id);

                $id           = (empty($insertId))?$id:$insertId;

                //checking if the record was created in the database, if not display an error message
                if($id == 0){

                    $this->wsf->renderView('app/form_message.php', array(
                        'type'              => 'danger',
                        'message'           => $this->wsf->trans('database_problem'),
                    ));


                }else {

                    $this->wsf->renderView('app/form_message.php', array(
                        'message'       => $this->wsf->trans('aws.field.form.success'),
                        'url'           => $this->wsf->adminUrl(array('controller' => 'field'))
                    ));
                }
            }
        }else{
            $picklistItemsData          = $this->fieldHelper->get_field_picklist_items($record);
            $radioItemsData             = $this->fieldHelper->getFieldItems('radio', $record);
            $imageListItemsData         = $this->fieldHelper->getFieldItems('imagelist', $record);
        }

        $viewParams     = array(
            'title'                             => $this->wsf->trans('Add'),
            'errors'                            => $errors,
            'form'                              => $form,
            
            'fieldHelper'                       => $this->fieldHelper,


            'id'                                => $id,

            'picklist_items_data'               => $picklistItemsData,
            'radio_items_data'                  => $radioItemsData,
            'imagelist_items_data'              => $imageListItemsData,
            
            'fieldTypes'                        => $fieldTypes,
        );
        
        $this->wsf->renderView('fields/field.php', array_merge($viewParams, array(
            'fieldOptions'                      => $this->fieldHelper->getFieldOptions($viewParams),
        )));

    }

    function importFileAction(){
        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
        $fieldType = $_GET['type'];
        $fileExtension =  array("xls", "xlsx", "ods" );
        $errors = array();

        if(!isset($_GET['id'])){
            $_GET['id']="";
        }




        $form = array(
            'label'                             => (isset($_POST['label'])?$_POST['label']:(isset($_GET['label'])?$_GET['label']:"")),
            'short_label'                       => (isset($_POST['short_label'])?$_POST['short_label']:(isset($_GET['short_label'])?$_GET['short_label']:"")),
            'description'                       => (isset($_POST['description'])?$_POST['description']:(isset($_GET['description'])?$_GET['description']:"")),
            'mode'                              => '',
            'type'                              => '',
            'check_errors'                      => (isset($_POST['check_errors'])?$_POST['check_errors']:(isset($_GET['check_errors'])?$_GET['check_errors']:"")),
            'required'                          => (isset($_POST['required'])?$_POST['required']:(isset($_GET['required'])?$_GET['required']:"")),
            'required_error_message'            => (isset($_POST['required_error_message'])?$_POST['required_error_message']:(isset($_GET['required_error_message'])?$_GET['required_error_message']:"")),
            'text_after_field'                  => (isset($_POST['text_after_field'])?$_POST['text_after_field']:(isset($_GET['text_after_field'])?$_GET['text_after_field']:"")),
            
            'hide_field_product_page'           => (isset($_POST['hide_field_product_page'])?$_POST['hide_field_product_page']:(isset($_GET['hide_field_product_page'])?$_GET['hide_field_product_page']:"")),
            'hide_field_cart_if_empty'          => (isset($_POST['hide_field_cart_if_empty'])?$_POST['hide_field_cart_if_empty']:(isset($_GET['hide_field_cart_if_empty'])?$_GET['hide_field_cart_if_empty']:"")),
            'hide_field_checkout_if_empty'      => (isset($_POST['hide_field_checkout_if_empty'])?$_POST['hide_field_checkout_if_empty']:(isset($_GET['hide_field_checkout_if_empty'])?$_GET['hide_field_checkout_if_empty']:"")),
            'hide_field_cart'                   => (isset($_POST['hide_field_cart'])?$_POST['hide_field_cart']:(isset($_GET['hide_field_cart'])?$_GET['hide_field_cart']:"")),
            'hide_field_checkout'               => (isset($_POST['hide_field_checkout'])?$_POST['hide_field_checkout']:(isset($_GET['hide_field_checkout'])?$_GET['hide_field_checkout']:"")),
            'hide_field_order'                  => (isset($_POST['hide_field_order'])?$_POST['hide_field_order']:(isset($_GET['hide_field_order'])?$_GET['hide_field_order']:"")),
            
            'checkbox_check_value'              => '',
            'checkbox_uncheck_value'            => '',

            'items_list_id'                     =>  1,
            'picklist_items'                    =>  "",

            /* Radio */
            'radio_image_width'                 =>  (isset($_POST['radio_image_width'])?$_POST['radio_image_width']:(isset($_GET['radio_image_width'])?$_GET['radio_image_width']:"")),
            'radio_image_height'                =>  (isset($_POST['radio_image_height'])?$_POST['radio_image_height']:(isset($_GET['radio_image_height'])?$_GET['radio_image_height']:"")),
            'radio_items'                       =>  "",

            /* Image List */
            'imagelist_field_image_width'       => (isset($_POST['imagelist_field_image_width'])?$_POST['imagelist_field_image_width']:(isset($_GET['imagelist_field_image_width'])?$_GET['imagelist_field_image_width']:"")),
            'imagelist_field_image_height'      => (isset($_POST['imagelist_field_image_height'])?$_POST['imagelist_field_image_height']:(isset($_GET['imagelist_field_image_height'])?$_GET['imagelist_field_image_height']:"")),
            'imagelist_popup_image_width'       => (isset($_POST['imagelist_popup_image_width'])?$_POST['imagelist_popup_image_width']:(isset($_GET['imagelist_popup_image_width'])?$_GET['imagelist_popup_image_width']:"")),
            'imagelist_popup_image_height'      => (isset($_POST['imagelist_popup_image_height'])?$_POST['imagelist_popup_image_height']:(isset($_GET['imagelist_popup_image_height'])?$_GET['imagelist_popup_image_height']:"")),
            'imagelist_items'                   => "",

            'checkbox_default_status'           => 0,

            'datetime_default_value'            => "",
            
            'numeric_default_value'             => "",
            'numeric_max_value'                 => "",
            'numeric_max_value_error'           => "",
            'numeric_min_value'                 => "",
            'numeric_min_value_error'           => "",
            'numeric_decimals'                  => "",
            'numeric_decimal_separator'         => "",
            'numeric_thousand_separator'        => "",

            'output_numeric_decimals'           => "",
            'output_numeric_decimal_separator'  => "",
            'output_numeric_thousand_separator' => "",

            'text_default_value'                => "",
            'text_regex'                        => "",
            'text_regex_error'                  => "",

            'system_created'                    =>  0,
        );




        if (!isset($_FILES['file_upload']['name']) || $_FILES['file_upload']['name'] == ""){
            $this->wsf->renderView('fields/import.php', array('type'=>$fieldType, 'form'=>$form));
        }else{

            $ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);

            if (in_array($ext,$fileExtension)) {

                try {
                    $spreadsheetFields = \PHPExcel_IOFactory::load($_FILES['file_upload']['tmp_name']);
                } catch (\PHPExcel_Reader_Exception $e) {
                    $this->wsf->renderView('fields/import.php', array('errors' => $this->wsf->trans('items.import.openfile.failed'), 'form'=>$form));
                }

                $importedFields = $this->fieldHelper->getItemsList($spreadsheetFields, $fieldType);
                $hasItem = $importedFields;
                $form['type'] = $fieldType;

                if (!$hasItem){
                    $this->wsf->renderView('fields/import.php', array('errors' => $this->wsf->trans('items.import.empty.failed'), 'type'=>$fieldType, 'form'=>$form));
                }elseif($_GET['id'] == "") {
                    $this->wsf->renderView('fields/field.php', array(
                        'title'                             => $this->wsf->trans('Add'),
                        'errors'                            => $errors,
                        'form'                              => $form,
                        
                        'fieldHelper'                       => $this->fieldHelper,


                        'id'                                => $_GET['id'],

                        'picklist_items_data'               => $importedFields['picklist_items_data'],
                        'radio_items_data'                  => $importedFields['radio_items_data'],
                        'imagelist_items_data'              => $importedFields['imagelist_items_data'],


                    ));
                }else{
                    $importedFields['controller']= 'field';
                    $importedFields['action'] = 'form';

                    $this->modifyItemsList($_GET['id'], $importedFields);


                }

            }else{
                $this->wsf->renderView('fields/import.php', array('errors' => $this->wsf->trans('items.import.extension.failed'), 'type'=>$fieldType, 'form'=>$form));

            }

        }



    }


    function modifyItemsList($field_idi, $data){

        $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');

        $fieldForm              = $this->wsf->get('\\AWSPriceCalculator\\Form', true, 'awspricecalculator/Form', 'FieldForm', array($this->wsf));
        $errors                 = array();
        $picklistItemsData      = array();
        $radioItemsData         = array();
        $imageListItemsData     = array();

        $id                     = $field_idi;
        $cloneId                = $this->wsf->requestValue('clone_id');
        $task                   = $this->wsf->requestValue('task');
        $form                   = null;

        if(!empty($id)){
            $record                     = $this->fieldModel->get_field_by_id($id);
            $options                    = json_decode($record->options, true);
        }

        if(!empty($cloneId)){
            $record                     = $this->fieldModel->get_field_by_id($cloneId);
            $options                    = json_decode($record->options, true);
        }


        $form = $this->wsf->requestForm($fieldForm, array(
            'label'                             => $this->wsf->isset_or($record->label, ''),
            'short_label'                       => $this->wsf->isset_or($record->short_label, ''),
            'description'                       => $this->wsf->isset_or($record->description, ''),
            'mode'                              => $this->wsf->isset_or($record->mode, 'input'),
            'type'                              => $this->wsf->isset_or($record->type, ''),
            'check_errors'                      => $this->wsf->isset_or($record->check_errors, 'always'),
            'required'                          => $this->wsf->isset_or($record->required, false),
            'required_error_message'            => $this->wsf->isset_or($record->required_error_message, ''),
            'text_after_field'                  => $this->wsf->isset_or($record->text_after_field, ''),
            
            'hide_field_product_page'           => $this->wsf->isset_or($record->hide_field_product_page, 0),
            'hide_field_cart_if_empty'          => $this->wsf->isset_or($record->hide_field_cart_if_empty, 0),
            'hide_field_checkout_if_empty'      => $this->wsf->isset_or($record->hide_field_checkout_if_empty, 0),
            'hide_field_cart'                   => $this->wsf->isset_or($record->hide_field_cart, 0),
            'hide_field_checkout'               => $this->wsf->isset_or($record->hide_field_checkout, 0),
            'hide_field_order'                  => $this->wsf->isset_or($record->hide_field_order, 0),
            
            'checkbox_check_value'              => $this->wsf->isset_or($options['checkbox']['check_value'], ''),
            'checkbox_uncheck_value'            => $this->wsf->isset_or($options['checkbox']['uncheck_value'], ''),

            'items_list_id'                     => $this->wsf->isset_or($options['items_list_id'], 1),
            'picklist_items'                    => $this->wsf->isset_or($data['picklist_items_data'], ""),

            /* Radio */
            'radio_image_width'                 => $this->wsf->isset_or($options['radio']['radio_image_width'], ""),
            'radio_image_height'                => $this->wsf->isset_or($options['radio']['radio_image_height'], ""),
            'radio_items'                       => $this->wsf->isset_or($data['radio_items_data'], ""),

            /* Image List */
            'imagelist_field_image_width'       => $this->wsf->isset_or($options['imagelist']['imagelist_field_image_width'], ""),
            'imagelist_field_image_height'      => $this->wsf->isset_or($options['imagelist']['imagelist_field_image_height'], ""),
            'imagelist_popup_image_width'       => $this->wsf->isset_or($options['imagelist']['imagelist_popup_image_width'], ""),
            'imagelist_popup_image_height'      => $this->wsf->isset_or($options['imagelist']['imagelist_popup_image_height'], ""),
            'imagelist_items'                   => $this->wsf->isset_or($data['imagelist_items_data'], ""),

            'checkbox_default_status'           => $this->wsf->isset_or($options['checkbox']['default_status'], 0),

            'datetime_default_value'            => $this->wsf->isset_or($options['date']['default_value'], ""),
            
            'numeric_default_value'             => $this->wsf->isset_or($options['numeric']['default_value'], ""),
            'numeric_max_value'                 => $this->wsf->isset_or($options['numeric']['max_value'], ""),
            'numeric_max_value_error'           => $this->wsf->isset_or($options['numeric']['max_value_error'], ""),
            'numeric_min_value'                 => $this->wsf->isset_or($options['numeric']['min_value'], ""),
            'numeric_min_value_error'           => $this->wsf->isset_or($options['numeric']['min_value_error'], ""),
            'numeric_decimals'                  => $this->wsf->isset_or($options['numeric']['decimals'], ""),
            'numeric_decimal_separator'         => $this->wsf->isset_or($options['numeric']['decimal_separator'], ""),
            'numeric_thousand_separator'        => $this->wsf->isset_or($options['numeric']['thousand_separator'], ""),

            'output_numeric_decimals'           => $this->wsf->isset_or($options['numeric']['decimals'], ""),
            'output_numeric_decimal_separator'  => $this->wsf->isset_or($options['numeric']['decimal_separator'], ""),
            'output_numeric_thousand_separator' => $this->wsf->isset_or($options['numeric']['thousand_separator'], ""),

            'text_default_value'                => $this->wsf->isset_or($options['text']['default_value'], ""),
            'text_regex'                        => $this->wsf->isset_or($options['text']['regex'], ""),
            'text_regex_error'                  => $this->wsf->isset_or($options['text']['regex_error'], ""),

            'system_created'                    => $this->wsf->isset_or($record->system_created, 0),
        ));


        if($this->wsf->isPost() && $task == 'field_form'){
            $form                       = $this->wsf->requestForm($fieldForm);

            $errors                     = array_merge($fieldForm->check($form, array('id' => $id)), $errors);

            $picklistItemsData          = $data['picklist_items_data'];
            $radioItemsData             = $data['radio_items_data'];
            $imageListItemsData         = $data['imagelist_items_data'];

            if(count($errors) == 0){
                $insertId     = $this->fieldModel->save($form, $id);

                $id           = (empty($insertId))?$id:$insertId;

                //checking if the record was created in the database, if not display an error message
                if($id == 0){

                    $this->wsf->renderView('app/form_message.php', array(
                        'type'              => 'danger',
                        'message'           => $this->wsf->trans('database_problem'),
                    ));


                }else {

                    $this->wsf->renderView('app/form_message.php', array(
                        'message'       => $this->wsf->trans('aws.field.form.success'),
                        'url'           => $this->wsf->adminUrl(array('controller' => 'field'))
                    ));
                }
            }
        }else{
            $picklistItemsData          = $data['picklist_items_data'];
            $radioItemsData             = $data['radio_items_data'];
            $imageListItemsData         = $data['imagelist_items_data'];
        }

        $this->wsf->renderView('fields/field.php', array(
            'title'                             => $this->wsf->trans('Add'),
            'errors'                            => $errors,
            'form'                              => $form,
            
            'fieldHelper'                       => $this->fieldHelper,


            'id'                                => $id,

            'picklist_items_data'               => $picklistItemsData,
            'radio_items_data'                  => $radioItemsData,
            'imagelist_items_data'              => $imageListItemsData,
        ));



    }






}
