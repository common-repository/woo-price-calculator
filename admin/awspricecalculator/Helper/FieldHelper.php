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

class FieldHelper {
    var $wsf;
    
    var $fieldModel;
    
    public function __construct(FrameworkHelper $wsf){
        $this->wsf = $wsf;
        
        /* MODELS */
        $this->calculatorModel      = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'CalculatorModel', array($this->wsf));
        $this->fieldModel           = $this->wsf->get('\\AWSPriceCalculator\\Model', true, 'awspricecalculator/Model', 'FieldModel', array($this->wsf));
    }
    
    /*
     * Ritorna una serie di campi utilizzando un'array di id
     */
    public function get_fields_by_ids($ids){
        $fields = array();

        if(!empty($ids)){
            foreach($ids as $id){
                $fields[] = $this->fieldModel->get_field_by_id($id);
            }
        }
        return $fields;
    }
        
    /*
     * Ritorna gli elementi da utilizzare in un menù a tendina
     */
    public function get_field_picklist_items($field){
        if(empty($field->options)){
            return array();
        }
        
        $options    = json_decode($field->options, true);
        $items      = json_decode($options['picklist_items'], true);
        
        if(empty($items)){
            return array();
        }
        
        return $items;
    }
        
    public function getFieldItems($fieldType, $field){
        if(empty($field->options)){
            return array();
        }

        $options        = json_decode($field->options, true);
        $retItems       = array();
        
        if(isset($options[$fieldType])){
            $retItems  = json_decode($options[$fieldType]["{$fieldType}_items"], true);
            
            if(empty($retItems)){
                return array();
            }
        }
                
        return $retItems;
    }
    
    
    /* Controllo se la cancellazione del campo può generare problemi,
     * è utilizzato da un simulatore?
     */
    public function checkFieldUsage($id){

        $calculators        = $this->calculatorModel->get_list();
        $calculatorsUsage   = array();
        $fieldIds           = array();
        
        foreach($calculators as $calculator){
            $calculatorFieldIds       = json_decode($calculator->fields, true);
            
            if($calculator->type == "simple"){
                /* Nothing to do */
            }else if($calculator->type == "excel"){
                $fields         = json_decode($calculator->options, true);

                /* Controllo i campi di input */
                foreach($fields['input'] as $coord => $fieldId){
                    if(is_numeric($fieldId)){
                        $calculatorFieldIds[]     = $fieldId;
                    }
                }
                
                /* Controllo i campi di output */
                foreach($fields['output'] as $coord => $fieldId){
                    if(is_numeric($fieldId)){
                        $calculatorFieldIds[]     = $fieldId;
                    }
                }
            }
            
            $fieldIds   = array_merge($fieldIds, $calculatorFieldIds);
            
            if(in_array($id, $calculatorFieldIds)){
                $calculatorsUsage[]       = $calculator;
            }
        }

        $fieldIds       = array_unique($fieldIds);

        if(in_array($id, $fieldIds)){
            $this->wsf->execute('awspricecalculator', true, '\\AWSPriceCalculator\\Controller', 'index', 'index');
            
            $error          = $this->wsf->trans('wpc.field.delete.error') . 
                                "<br/><br/>";
            
            foreach($calculatorsUsage as $calculatorUsage){
                $error      .= "- {$calculatorUsage->id}: {$calculatorUsage->name}<br/>";
            }
            
            return $error;
        }
        
        return null;
    }
    
    public function getShortLabel($simulatorField){
        if(empty($simulatorField->short_label)){
            return $simulatorField->label;
        }
        
        return $simulatorField->short_label;
    }
    
    public function getDateTimeFieldFormat($dateField){
        $fieldType  = $dateField->type;
        $options    = json_decode($dateField->options, true);
        $format     = $options['date']["{$fieldType}_format"];
        
        if(empty($format)){
            if($fieldType == "date"){
                return "Y-m-d";
            }else if($fieldType == "datetime"){
                return "Y-m-d H:i:s";
            }else if($fieldType == "time"){
                return "H:i:s";
            }
        }
        
        return $format;
    }
    
    /* Ritorna il valore di default inserito nel campo */
    public function getFieldDefaultValue($simulatorField, $calculator, $returnKey = false){
        $options                = json_decode($simulatorField->options, true);
        $calculatorHelper       = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'CalculatorHelper', array($this->wsf));
        
        if($simulatorField->type == 'checkbox'){
            if($options['checkbox']['default_status'] == true){
                return 1;
            }
            
        }else if($simulatorField->type == 'numeric'){

            return $options['numeric']['default_value'];

        }else if($simulatorField->type == 'picklist'){
            
            $picklistItems = $this->get_field_picklist_items($simulatorField);
            
            /* Checking default option if Yes */
            foreach($picklistItems as $key => $item){
                if($item['default_option'] == true){
                    return ($returnKey == true)?$item['id']:$item['value'];
                }
            }
            
            /* Getting first available item */
            foreach($picklistItems as $key => $item){
                    return ($returnKey == true)?$item['id']:$item['value'];
            }
            
        }else if($simulatorField->type == 'text'){
            return $options['text']['default_value'];
        }else if($simulatorField->type == 'radio'){
            
            $radioItems    = $this->getFieldItems('radio', $simulatorField);
            
            /* Checking default option if Yes */
            foreach($radioItems as $key => $item){
                if($item['default_option'] == true){
                    return ($returnKey == true)?$item['id']:$item['value'];
                }
            }
            
            /* Getting first available item */
            foreach($radioItems as $key => $item){
                    return ($returnKey == true)?$item['id']:$item['value'];
            }
            
        }else if($simulatorField->type == 'imagelist'){
            
            $items    = $this->getFieldItems('imagelist', $simulatorField);
            
            /* Checking default option if Yes */
            foreach($items as $key => $item){
                if($item['default_option'] == true){
                    return ($returnKey == true)?$item['id']:$item['value'];
                }
            }
            
            /* Getting first available item */
            foreach($items as $key => $item){
                    return ($returnKey == true)?$item['id']:$item['value'];
            }
            
        }else if($simulatorField->type == 'date' || 
                 $simulatorField->type == 'time' ||
                 $simulatorField->type == 'datetime'){
            
            if($returnKey == true){
                if($calculator->type == "excel" && $options['date']['default_value'] == "[spreadsheet]"){
                    $phpExcel           = $calculatorHelper->getPhpExcelCalculator($calculator);
                    $calculatorCells    = $calculatorHelper->getLoaderCalculatorCells($calculator);

                    foreach($calculatorCells as $coordinates => $fieldId){
                        if($fieldId == $simulatorField->id){
                            $format = $this->getDateTimeFieldFormat($simulatorField);
                            $test   = $calculatorHelper->getCellFormattedDate($phpExcel, $coordinates, $format);
                            return $calculatorHelper->getCellFormattedDate($phpExcel, $coordinates, $format);
                        }
                    }

                }else{
                   return "";
                }
            }else{
                return 0;
            }

        }else{
            throw new \Exception("FieldHelper::getFieldDefaultValue(): Unknow field type {$simulatorField->type}");
        }
    }
    
    public function getOutputFieldName($id){
        return "awspc_output_result_{$id}";
    }
    
    public function getFieldName($id){
        return "aws_price_calc_{$id}";
    }
    
    public function convertFieldsToFilters($fields, $excludeFieldId = null){
        $filters    = array();
        
        foreach($fields as $field){
            if($excludeFieldId != $field->id){
                $fieldName      = $this->getFieldName($field->id);
                
                /*
                 }else if($field->type == "picklist"){
                    
                    $keyValuePair   = array();
                    foreach($this->get_field_picklist_items($field) as $item){
                        $keyValuePair[$item['id']]   = $item['label'];
                    }
                    
                    $filters[] = array(
                        'id'        => $field->id,
                        'field'     => $fieldName,
                        'label'     => $field->label,
                        'type'          => 'string',
                        'input'         => 'select',
                        'multiple'      => true,
                        'plugin'        => 'chosen',
                        'plugin_config' => array(
                            'width'     => '100%',
                        ),
                        'values' => $keyValuePair,
                        'operators' => array(
                            'in',
                            'not_in',
                            'is_empty_null',
                            'is_not_empty_null',
                        )
                    );
                }else if($field->type == "radio"){
                    
                    $keyValuePair   = array();
                    foreach($this->getFieldItems('radio', $field) as $item){
                        $keyValuePair[$item['id']]   = $item['label'];
                    }
                    
                    $filters[] = array(
                        'id'        => $field->id,
                        'field'     => $fieldName,
                        'label'     => $field->label,
                        'type'          => 'string',
                        'input'         => 'select',
                        'multiple'      => true,
                        'plugin'        => 'chosen',
                        'plugin_config' => array(
                            'width'     => '100%',
                        ),
                        'values' => $keyValuePair,
                        'operators' => array(
                            'in',
                            'not_in',
                            'is_empty_null',
                            'is_not_empty_null',
                        )
                    );
                 */
                
                if($field->type == "numeric"){
                    $filters[]  = array(
                        'id'        => $field->id,
                        'field'     => $fieldName,
                        'label'     => $field->label,
                        'type'      => 'double',
                        'operators' => array(
                            'equal',
                            'not_equal',
                            'less',
                            'less_or_equal',
                            'greater',
                            'greater_or_equal',
                            'is_empty',
                            'is_not_empty',
                        )
                    );
                }else if($field->type == "text" ||
                        $field->type == "picklist" ||
                        $field->type == "radio" || 
                        $field->type == "checkbox" ||
                        $field->type == "imagelist"){
                    $filters[] = array(
                        'id'        => $field->id,
                        'field'     => $fieldName,
                        'label'     => $field->label,
                        'type'      => 'string',
                        'operators' => array(
                            'equal',
                            'not_equal',
                            'less',
                            'less_or_equal',
                            'greater',
                            'greater_or_equal',
                            'begins_with',
                            'not_begins_with',
                            'contains',
                            'not_contains',
                            'ends_with',
                            'not_ends_with',
                            'is_empty',
                            'is_not_empty',
                        )
                    );
                }else if($field->type == "date"){
                    $filters[] = array(
                        'id'            => $field->id,
                        'field'         => $fieldName,
                        'label'         => $field->label,
                        'type'          => 'date',
                        'plugin'        => 'xdsoft_datetimepicker',
                        'plugin_config' => array(
                            'timepicker'        => false,
                            'format'            => 'Y-m-d',
                            'lazyInit'          => true,
                            'validateOnBlur'    => false,
                            'allowBlank'        =>  true,
                            'scrollInput'       =>  false,
                            'closeOnDateSelect' =>  true,
                        ),
                        'operators' => array(
                            'equal',
                            'not_equal',
                            'in',
                            'not_in',
                            'less',
                            'less_or_equal',
                            'greater',
                            'greater_or_equal',
                            'between',
                            'not_between',
                            'is_empty',
                            'is_not_empty',
                        )
                    );
                }else if($field->type == "time"){
                    $filters[] = array(
                        'id'            => $field->id,
                        'field'         => $fieldName,
                        'label'         => $field->label,
                        'type'          => 'time',
                        'plugin'        => 'xdsoft_datetimepicker',
                        'plugin_config' => array(
                            'datepicker'        => false,
                            'format'            => 'H:i:s',
                            'lazyInit'          => true,
                            'validateOnBlur'    => false,
                            'allowBlank'        => true,
                            'scrollInput'       => false
                        ),
                        'operators' => array(
                            'equal',
                            'not_equal',
                            'in',
                            'not_in',
                            'less',
                            'less_or_equal',
                            'greater',
                            'greater_or_equal',
                            'between',
                            'not_between',
                            'is_empty',
                            'is_not_empty',
                        )
                    );
                }else if($field->type == "datetime"){
                    $filters[] = array(
                        'id'            => $field->id,
                        'field'         => $fieldName,
                        'label'         => $field->label,
                        'type'          => 'datetime',
                        'plugin'        => 'xdsoft_datetimepicker',
                        'plugin_config' => array(
                            'format'            => 'Y-m-d H:i:s',
                            'lazyInit'          => true,
                            'validateOnBlur'    => false,
                            'allowBlank'        => true,
                            'scrollInput'       => false
                        ),
                        'operators' => array(
                            'equal',
                            'not_equal',
                            'in',
                            'not_in',
                            'less',
                            'less_or_equal',
                            'greater',
                            'greater_or_equal',
                            'between',
                            'not_between',
                            'is_empty',
                            'is_not_empty',
                        )
                    );
                }else{
                    throw new Exception("FieldHelper::convertFieldsToFilters(): Unknown field type {$field->type}");
                }

            }
        }
        
        return $filters;
    }
    
    /* 
     * Cerca se un campo esiste già, avendo le stesse proprietà, altrimenti
     * ritorna false
     */
    public function findField($field){
        $field              = (array) $field;
        $field['id']        = null; //Rimuovo l'ID per il confronto
        $field['options']   = (!is_array($field['options']))?json_decode($field['options'], true):$field['options'];

        
        
        $fields             = $this->fieldModel->get_field_list();
        
        foreach($fields as $currentField){
            $compareField                = (array) $currentField;
            $compareField['id']          = null; //Rimuovo l'ID per il confronto
            $compareField['options']     = (!is_array($compareField['options']))?json_decode($compareField['options'], true):$compareField['options'];
            
            /* Confronto i due array multidimensionalmente */
            if($compareField == $field){
                return $currentField;
            }
        }
        
        return false;
    }

    public function getItemsList($spreadsheet, $type){

        $items['picklist_items_data'] = array();
        $items['radio_items_data'] = array();
        $items['imagelist_items_data']= array();
        $i = 1;
        $cell = $spreadsheet->getActiveSheet();


        switch ($type){
            case 'picklist':
                while ($cell->getCell('A'.$i)->getValue() != ""){
                    array_push($items['picklist_items_data'], array(
                        'id'                => $i,
                        'label'             => $cell->getCell('A'.$i)->getValue(),
                        'value'             => $cell->getCell('B'.$i)->getValue(),
                        'default_option'    => $cell->getCell('C'.$i)->getValue(),
                        'order_details'     => $cell->getCell('D'.$i)->getValue()
                    ));
                    $i++;
                }
                break;

            case 'radio':
                while ($cell->getCell('A'.$i)->getValue() != ""){
                    array_push($items['radio_items_data'], array(
                        'id'                => $i,
                        'label'             => $cell->getCell('A'.$i)->getValue(),
                        'value'             => $cell->getCell('B'.$i)->getValue(),
                        'default_option'    => $cell->getCell('C'.$i)->getValue(),
                        'order_details'     => $cell->getCell('D'.$i)->getValue(),
                        'tooltip_position'  => $cell->getCell('E'.$i)->getValue(),
                        'tooltip_message'   => $cell->getCell('F'.$i)->getValue(),
                        'image'             => $cell->getCell('G'.$i)->getValue()
                    ));
                    $i++;
                }
                break;

            case 'imagelist':
                while ($cell->getCell('A'.$i)->getValue() != ""){
                    array_push($items['imagelist_items_data'], array(
                        'id'                => $i,
                        'label'             => $cell->getCell('A'.$i)->getValue(),
                        'value'             => $cell->getCell('B'.$i)->getValue(),
                        'default_option'    => $cell->getCell('C'.$i)->getValue(),
                        'order_details'     => $cell->getCell('D'.$i)->getValue(),
                        'image'             => $cell->getCell('E'.$i)->getValue()
                    ));
                    $i++;
                }
                break;

        }




        if (empty($items['imagelist_items_data']) && empty($items['radio_items_data']) && empty($items['picklist_items_data'])) return false;
        else return $items;

    }
    
    /*
     * Get the list of calculable fields types
     */
    public function getCalculableFieldTypes(){
        return array(
            'checkbox',
            'numeric',
            'picklist',
            'imagelist',
            'radio',
        );
    }
    
    /*
     * Return only a list of calculable fields
     */
    public function getCalculableFieldList($mode = null){
        $fields                 = $this->fieldModel->get_field_list($mode);
        $calculableFieldTypes   = $this->getCalculableFieldTypes();
        $calculableFields       = array();
        
        
        foreach($fields as $field){
            if(in_array($field->type, $calculableFieldTypes)){
                $calculableFields[]     = $field;
            }
        }
        
        return $calculableFields;
    }
    
    public function getFieldTypes(){                                                    
        $fieldTypes     = array(
            'checkbox'      => $this->wsf->trans("Checkbox"),
            'numeric'       => $this->wsf->trans("Numeric"),
            'picklist'      => $this->wsf->trans("Picklist"),
            'imagelist'     => $this->wsf->trans('field.form.field_type.imagelist'),
            'text'          => $this->wsf->trans('Text'),
            'date'          => $this->wsf->trans('wpc.date'),
            'time'          => $this->wsf->trans('wpc.time'),
            'datetime'      => $this->wsf->trans('wpc.datetime'),
            'radio'         => $this->wsf->trans('wpc.radio'),
        );
        
        $fieldTypes     = apply_filters("awspc_filter_field_types", $fieldTypes);
        
        return $fieldTypes;
    }
    
    public function getFieldOptions($viewParams = array()){
        $fieldOptions           = array();
        $fieldTypes             = $this->getFieldTypes();
        $availableFieldOptions  = array(
            'datetime',
            'checkbox',
            'picklist',
            'imagelist',
            'numeric',
            'text',
            'radio',
        );
        
        foreach($fieldTypes as $fieldTypeKey => $fieldTypeLabel){
            if(in_array($fieldTypeKey, $availableFieldOptions)){
                $fieldOptions[$fieldTypeKey]    = $this->wsf->getView(
                        'awspricecalculator',
                        "fields/{$fieldTypeKey}_options.php",
                        true,
                        $viewParams
                );
            }
        }
        
        $fieldOptions     = apply_filters("awspc_filter_field_options", $fieldOptions);
        
        return $fieldOptions;
    }
    
    public function getOutputResult($field, $value){
        $fieldOptions                   = json_decode($field->options, true);
        
        /* Decimals could be also "0", so don't put "empty" check on this if */
        if($fieldOptions['numeric']['decimals'] === ''){
            $decimals                   = 2; //Default decimals
        }else{
            $decimals                   = (int)$fieldOptions['numeric']['decimals'];
        }

        if(empty($fieldOptions['numeric']['decimal_separator'])){
            $decimalSeparator           = "."; //Default decimal separator
        }else{
            $decimalSeparator           = $fieldOptions['numeric']['decimal_separator'];
        }

        $thousandSeparator              = $fieldOptions['numeric']['thousand_separator'];

        if(is_numeric($value)){
            return number_format($value, $decimals, $decimalSeparator, $thousandSeparator);
        }
        
        return $value;
    }
}
