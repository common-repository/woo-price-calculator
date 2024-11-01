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

class FieldForm {

    private $wsf;

    private $form;

    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;

        $this->form[] = array(
            'name' => 'label'
        );

        $this->form[] = array(
            'name' => 'short_label'
        );

        $this->form[] = array(
            'name' => 'description'
        );

        $this->form[] = array(
            'name'  => 'mode',
        );

        $this->form[] = array(
            'name' => 'type'
        );

        $this->form[] = array(
            'name' => 'check_errors'
        );
        
        $this->form[] = array(
            'name' => 'required'
        );

        $this->form[] = array(
            'name' => 'required_error_message'
        );
        
        $this->form[] = array(
            'name' => 'text_after_field',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_product_page',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_cart_if_empty',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_checkout_if_empty',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_cart',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_checkout',
        );
        
        $this->form[] = array(
            'name' => 'hide_field_order',
        );
        
        $this->form[] = array(
            'name' => 'checkbox_check_value'
        );

        $this->form[] = array(
            'name' => 'checkbox_uncheck_value'
        );

        $this->form[] = array(
            'name' => 'picklist_items'
        );

        $this->form[] = array(
            'name' => 'imagelist_field_image_width'
        );

        $this->form[] = array(
            'name' => 'imagelist_field_image_height'
        );

        $this->form[] = array(
            'name' => 'imagelist_popup_image_width'
        );

        $this->form[] = array(
            'name' => 'imagelist_popup_image_height'
        );

        $this->form[] = array(
            'name' => 'imagelist_items'
        );

        /* Radio */

        $this->form[] = array(
            'name' => 'radio_image_width'
        );

        $this->form[] = array(
            'name' => 'radio_image_height'
        );

        $this->form[] = array(
            'name' => 'radio_items'
        );

        $this->form[] = array(
            'name' => 'items_list_id'
        );

        $this->form[] = array(
            'name' => 'checkbox_default_status'
        );

        $this->form[] = array(
            'name' => 'numeric_default_value'
        );

        $this->form[] = array(
            'name' => 'numeric_max_value'
        );

        $this->form[] = array(
            'name' => 'numeric_max_value_error'
        );

        $this->form[] = array(
            'name' => 'numeric_min_value'
        );

        $this->form[] = array(
            'name' => 'numeric_min_value_error'
        );

        $this->form[] = array(
            'name' => 'numeric_decimals'
        );

        $this->form[] = array(
            'name' => 'numeric_decimal_separator'
        );

        $this->form[] = array(
            'name' => 'numeric_thousand_separator'
        );

        $this->form[] = array(
            'name' => 'output_numeric_decimals'
        );

        $this->form[] = array(
            'name' => 'output_numeric_decimal_separator'
        );

        $this->form[] = array(
            'name' => 'output_numeric_thousand_separator'
        );

        $this->form[] = array(
            'name' => 'text_default_value'
        );

        $this->form[] = array(
            'name' => 'text_regex'
        );

        $this->form[] = array(
            'name' => 'text_regex_error'
        );

        $this->form[] = array(
            'name' => 'system_created'
        );

        $this->form[] = array(
            'name' => 'date_format'
        );

        $this->form[] = array(
            'name' => 'time_format'
        );

        $this->form[] = array(
            'name' => 'datetime_format'
        );

        $this->form[] = array(
            'name' => 'datetime_default_value'
        );
    }

    public function check($record, $params = array()){

        $errors = array();
        if(empty($record['label'])){
            $errors[] = "- " . $this->wsf->trans('Field Label must not be empty');
        }

        if(empty($record['mode'])){
            $errors[] = "- " . $this->wsf->trans('aws.field.mode.error.empty');
        }else if($record['mode'] == 'input'){
            if(empty($record['type'])){
                $errors[] = "- " . $this->wsf->trans('Field Type must not be empty');
            }

            /* Checking if regex is correct */
            if($record['type'] == 'text' && !empty($record['text_regex'])){
                if(@preg_match($record['text_regex'], "") === false){
                    $regexError     = error_get_last();
                    $errors[]       = $this->wsf->trans('field.form.error.regex_error', array('error_message' => $regexError['message']));
                }
            }
        }

        return $errors;
    }

    public function getForm(){
        return $this->form;
    }

    public function setForm($form){
        $this->form = $form;
    }
}

