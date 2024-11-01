<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 **/

namespace AWSPriceCalculator\Model;

/*AWS_PHP_HEADER*/

use WSF\Helper\FrameworkHelper;

class FieldModel {
    var $wsf;
    var $db;

    public function __construct(FrameworkHelper $wsf){
        $this->wsf = $wsf;

        $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));

    }
    /*
     * Ritorna tutta la lista dei campi
     */
    public function get_field_list($mode = ''){
        if(empty($mode)){
            return $this->databaseHelper->getResults("SELECT * FROM [prefix]woopricesim_fields");
        }else{
            return $this->databaseHelper->getResults("SELECT * FROM [prefix]woopricesim_fields WHERE mode = :mode", array(
                'mode'  => $mode
            ));
        }

    }
    
    /*
     * Ritorna un campo utilizzando l'ID
     */
    public function get_field_by_id($id){
        return $this->databaseHelper->getRow("
                SELECT * FROM [prefix]woopricesim_fields 
                WHERE id = :id", array(
            'id'    => $id,
        ));
    }

    /*
     * Ritorna il numero di campi creati
     */
    public function getFieldCount(){
        return count($this->get_field_list());
    }

    public function save($data, $id = null){

        if(isset($data['options'])){
            $options    = $data['options'];
        }else{
            $options    = array(
                'items_list_id'         => $data['items_list_id'],

                'picklist_items'        => $data['picklist_items'],

                'imagelist'             => array(
                    'imagelist_field_image_width'       => $data['imagelist_field_image_width'],
                    'imagelist_field_image_height'      => $data['imagelist_field_image_height'],
                    'imagelist_popup_image_width'       => $data['imagelist_popup_image_width'],
                    'imagelist_popup_image_height'      => $data['imagelist_popup_image_height'],
                    'imagelist_items'                   => $data['imagelist_items'],
                ),

                'checkbox' => array(
                    'check_value'       => $data['checkbox_check_value'],
                    'uncheck_value'     => $data['checkbox_uncheck_value'],
                    'default_status'    => $data['checkbox_default_status'],
                ),

                'numeric' => array(
                    'default_value'      => $data['numeric_default_value'],
                    'max_value'          => $data['numeric_max_value'],
                    'max_value_error'    => $data['numeric_max_value_error'],
                    'min_value'          => $data['numeric_min_value'],
                    'min_value_error'    => $data['numeric_min_value_error'],
                    'decimals'           => ($data['mode'] == 'output')?$data['output_numeric_decimals']:$data['numeric_decimals'],
                    'decimal_separator'  => ($data['mode'] == 'output')?$data['output_numeric_decimal_separator']:$data['numeric_decimal_separator'],
                    'thousand_separator' => ($data['mode'] == 'output')?$data['output_numeric_thousand_separator']:$data['numeric_thousand_separator'],
                ),

                'date'=> array(
                    'date_format'        => $data['date_format'],
                    'time_format'        => $data['time_format'],
                    'datetime_format'    => $data['datetime_format'],
                    'default_value'      => $data['datetime_default_value'],

                ),

                'text' => array(
                    'default_value'      => $data['text_default_value'],
                    'regex'              => $data['text_regex'],
                    'regex_error'        => $data['text_regex_error'],
                ),

                'radio' => array(
                    'radio_image_width'       => $data['radio_image_width'],
                    'radio_image_height'      => $data['radio_image_height'],
                    'radio_items'             => $data['radio_items'],
                ),
            );
        }


        $record = array(
            "label"                         => $data['label'],
            "short_label"                   => $data['short_label'],
            "description"                   => $data['description'],
            "mode"                          => $data['mode'],
            "type"                          => $data['type'],
            "check_errors"                  => $data['check_errors'],
            "required"                      => $data['required'],
            "required_error_message"        => $data['required_error_message'],
            "text_after_field"              => $data['text_after_field'],
            
            "hide_field_product_page"       => $data['hide_field_product_page'],
            "hide_field_cart_if_empty"      => $data['hide_field_cart_if_empty'],
            "hide_field_checkout_if_empty"  => $data['hide_field_checkout_if_empty'],
            "hide_field_cart"               => $data['hide_field_cart'],
            "hide_field_checkout"           => $data['hide_field_checkout'],
            "hide_field_order"              => $data['hide_field_order'],
            
            "options"                       => json_encode($options),
            "system_created"                => 0,
        );

        if(empty($id)){
            return $this->databaseHelper->insert("[prefix]woopricesim_fields", $record);
        }else{
            $this->databaseHelper->update("[prefix]woopricesim_fields", $record, array(
                'id'    => $id,
            ));
        }
    }

    public function delete($id){
        $this->databaseHelper->delete("[prefix]woopricesim_fields", array(
            'id'    => $id,
        ));
    }
}