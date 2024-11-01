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

class ThemeHelper {
    
    var $wsf;
    
    var $fieldHelper;
    
    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        /* HELPERS */
        $this->fieldHelper          = $this->wsf->get('\\AWSPriceCalculator\\Helper', true, 'awspricecalculator/Helper', 'FieldHelper', array($this->wsf));
    }
    
    /*
     * Ritorna la lista dei temi nel formato:
     * array(
     *      'path'      => PATH
     *      'filename'  => NOME FILE TEMA
     *      'name'      => NOME TEMA
     * )
     */
    public function getThemes($wsf = null){
        $themes_list = array();
        
        if($wsf === null){
            $wsf    = $this->wsf;
        }
        
        $themes = glob($wsf->getUploadPath('themes/*.php'));
        
        foreach($themes as $theme){
            $theme_file = file_get_contents($theme);
            preg_match("/THEME_NAME:(.*)/", $theme_file, $match_name);
            
            $name = trim($match_name[1]);
            $name = str_replace("/*", "", $name);
            $name = str_replace("*/", "", $name);
            
            $filename = str_replace($wsf->getUploadPath('themes/'), "", $theme);
            
            $themes_list[] = array(
                'path'      => $theme,
                'filename'  => $filename,
                'name'      => $name,
            );
        }
        
        return $themes_list;
    }
    
    /*
     * Ricerca un tema passando in $findTheme il contenuto del file. Se non
     * trovato ritorna false
     */
    public function findTheme($themeContents){
        $themes         = $this->getThemes();
        
        foreach($themes as $theme){
            if(sha1($themeContents) == sha1_file($theme['path'])){
                return $theme;
            }
        }
        
        return false;
    }
    
    /*
     * Ritorna il valore di default di un campo da visualizzare
     */
    public function getThemeFieldDefaultValue($field, $calculator){
        $options                = json_decode($field->options, true);
        $defaultValue           = $this->fieldHelper->getFieldDefaultValue($field, $calculator, true);
        
        if($field->type == "numeric"){
            if(empty($options['numeric']['decimal_separator'])){
                $separator    = ".";
            }else{
                $separator    = $options['numeric']['decimal_separator'];
            }
            
            return str_replace(".", $separator, $defaultValue);
        }else if(
                 $field->type == "date" || 
                 $field->type == "time" || 
                 $field->type == "datetime"
                ){

            return $defaultValue;
        }
        
        return $defaultValue;
    }
    
    public function getDefaultThemeData($calculator, $simulatorFields, $values = array(), $errors = array()){
        $data       = array();

        foreach($simulatorFields as $key => $v){
            $options    = json_decode($v->options, true);
            $elementId  = $this->fieldHelper->getFieldName($v->id);
            $optionId   = $this->fieldHelper->getFieldName("{$v->id}_options");
            $class      = $this->fieldHelper->getFieldName($v->type);

            if(count($values) == 0){ //I valori sono nella richiesta
                $value      = $this->wsf->requestValue($elementId);
            }else{
                if(isset($values[$elementId])){
                    $value      = $values[$elementId];
                }else{
                    $value      = 0;
                }

            }

            if($v->type == "checkbox"){
                    if($value === "on"){
                        $checked = "checked";
                    }else{
                        $checked = "";
                        if($options['checkbox']['default_status'] == 1){
                            $checked = "checked";
                        }
                    }

                    $element = "<input name=\"{$elementId}\" type=\"checkbox\" {$checked}/>";

            }else if($v->type == "numeric"){
                    if(empty($value)){
                        $value = $this->getThemeFieldDefaultValue($v, $calculator);
                    }

                    $element = "<input name=\"{$elementId}\" type=\"text\" value=\"{$value}\" />";

            }else if($v->type == "picklist"){
                    $element = "<select name=\"{$elementId}\">";
                        $picklist_items = $this->fieldHelper->get_field_picklist_items($v);

                        foreach($picklist_items as $index => $item){
                            $selected           = '';
                            $label              = $this->wsf->userTrans($item['label']);
                            $defaultOption      = (isset($item['default_option'])?$item['default_option']:false);
                            
                            if($value == $item['id']){
                                $selected = 'selected="selected"';
                            }

                            if(empty($selected) && $defaultOption == true){
                                $selected = 'selected="selected"';
                            }
                    
                            $element .= "<option value=\"{$item['id']}\" {$selected}>{$label}</option>";
                        }
                    $element .= '</select>';
            }else if($v->type == "text"){
                if(empty($value)){
                    $value = htmlspecialchars($this->wsf->decode($options['text']['default_value']));
                }

                $element = "<input name=\"{$elementId}\" type=\"text\" value=\"{$value}\" />";

            }else if($v->type == "date" || $v->type == "time" || $v->type == "datetime"){
                if(empty($value)){
                    $value = $this->getThemeFieldDefaultValue($v, $calculator);
                }
                    
                $element = "<input name=\"{$elementId}\" value=\"{$value}\" type=\"text\" />";
            }else if($v->type == "radio"){
                
                $radio_items    = $this->fieldHelper->getFieldItems('radio', $v);
                $radioOptions   = $options['radio'];

                $imageWidth     = (!empty($radioOptions['radio_image_width']))?"width: {$radioOptions['radio_image_width']};":"";
                $imageHeight    = (!empty($radioOptions['radio_image_height']))?"height: {$radioOptions['radio_image_width']};":"";
                    
                $radioIndex    = 0;
                $element       = "";
                foreach($radio_items as $index => $item){
                    $selected   = '';
                    $label              = $this->wsf->userTrans($item['label']);
                    $tooltipMessage     = (isset($item['tooltip_message'])?$item['tooltip_message']:"");
                    $tooltipPosition    = (isset($item['tooltip_position'])?$item['tooltip_position']:"none");
                    $defaultOption      = (isset($item['default_option'])?$item['default_option']:false);
                    $image              = (isset($item['image']))?$item['image']:null;
  
                    if($value == $item['id']){
                        $selected = 'checked="checked"';
                    }
                    
                    if(empty($selected) && $defaultOption == true){
                        $selected = 'checked="checked"';
                    }
                    
                    if(empty($selected) && $radioIndex == 0){
                        $selected = 'checked="checked"';
                    }
                    
                    $tooltip        = "";
                    $leftTooltip    = "";
                    $rightTooltip   = "";
                    if($tooltipPosition != 'none'){
                        $tooltip  = $this->wsf->getView('awspricecalculator', 'partial/help.php', true, array(
                            'text' => $this->wsf->userTrans($tooltipMessage),
                        ));
                        
                        if($tooltipPosition == "left"){
                            $leftTooltip    = $tooltip;
                        }else{
                            $rightTooltip   = $tooltip;
                        }
                    }
                    
                    if(!empty($image)){
                        $imageHtml  = "<img style=\"{$imageHeight}{$imageWidth}\" src=\"{$image}\" />";
                    }else{
                        $imageHtml  = "";
                    }
                    
                    $element .= "{$imageHtml} <input value=\"{$item['id']}\" {$selected} name=\"{$elementId}\" type=\"radio\" />{$leftTooltip} {$label} {$rightTooltip}<br/>";

                    $radioIndex++;
                }

            }else if($v->type == "imagelist"){
                $imagelistItems    = $this->fieldHelper->getFieldItems('imagelist', $v);
                $height            = (empty($options['imagelist']['imagelist_field_image_height']))?"":"height: {$options['imagelist']['imagelist_field_image_height']};";
                $width             = (empty($options['imagelist']['imagelist_field_image_width']))?"":"width: {$options['imagelist']['imagelist_field_image_width']};";

                /* If a value was choosen, select the choosen value */
                $selectedItem      = null;
                foreach($imagelistItems as $index => $item){
                    if($value == $item['id']){
                        $selectedItem   = $item;
                        break;
                    }
                }
                
                /*
                 * If no value was choosen, I try to display the default option
                 */
                if(empty($selectedItem)){
                    foreach($imagelistItems as $index => $item){
                        $defaultOption      = (isset($item['default_option'])?$item['default_option']:false);
                        if(empty($selectedItem) && $defaultOption == true){
                            $selectedItem   = $item;
                            break;
                        }
                    }
                }

                /*
                 * If no default option was choosen, I just take the first one
                 */
                if(empty($selectedItem)){
                    foreach($imagelistItems as $index => $item){
                        $selectedItem   = $item;
                        break;
                    }
                }
                
                $value             = $selectedItem['id'];
                
                $element           = "<button data-remodal-target=\"awspc_modal_imagelist_{$v->id}\" class=\"aws_price_calc_imagelist_button\" type=\"button\">"
                                        . "<img style=\"{$height}{$width}\" class=\"awspc_modal_imagelist_image\" src=\"{$selectedItem['image']}\" />"
                                        . "<span class=\"awspc_modal_imagelist_text\">{$selectedItem['label']}</span>"
                                    . '</button>';
                
                /* Hidden element */
                $element           .= "<input type=\"hidden\" name=\"{$elementId}\" value=\"{$selectedItem['id']}\" />";
            }

            $viewParams             = array(
                        'elementId'     => $elementId,
                        'field'         => $v,
                        'labelId'       => $this->fieldHelper->getFieldName("label_{$v->id}"),
                        'inputId'       => $this->fieldHelper->getFieldName("input_{$v->id}"),
                        'optionId'      => $optionId,
                        'value'         => $value,
                        'element'       => $element,
                        'options'       => json_encode($options),
                        'class'         => $class,
                        'errors'        => isset($errors[$elementId])?$errors[$elementId]:null,
            );

            $viewParams['widget']   = $this->wsf->getView('awspricecalculator', 'product/widget_input.php', true, $viewParams);
            $data[$elementId]       = $viewParams;

        }

        return $data;
    }
        
}
