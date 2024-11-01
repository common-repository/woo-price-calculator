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

class SettingsModel {
    var $wsf;
    var $db;
    
    public function __construct(FrameworkHelper $wsf){
        $this->wsf = $wsf;
        
        $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));
    }

    /*
     * Ritorna un campo utilizzando la chiave
     */
    public function getValues(){
        $ret    = array();
        
        $rows   = $this->databaseHelper->getResults("SELECT * FROM [prefix]woopricesim_settings");

        if(empty($rows) || count($rows) == 0){
            $rows       = array();
        }
        
        foreach($rows as $row){
            $ret[$row->s_key]     = $row->s_value;
        }
        
        return $ret;
    }
    
    /*
     * Ritorna un campo utilizzando la chiave
     */
    public function getValue($key){

        $ret    = $this->databaseHelper->getRow("SELECT * FROM [prefix]woopricesim_settings WHERE s_key = :key", array(
            'key'   => $key,
        ));
        
        if(empty($ret)){
            return null;
        }
        
        return $ret->s_value;
    }
    
    /*
     * Salva un valore nella tabella delle impostazioni
     */
    public function setValue($key, $value){
        $record = array(
            's_key'         => $key,
            's_value'       => $value,
        );
        
        if($this->getValue($key) == null && $this->getValue($key) !== ''){
            return $this->databaseHelper->insert("[prefix]woopricesim_settings", $record);
        }else{
            $this->databaseHelper->update("[prefix]woopricesim_settings", $record, array(
                    's_key' => $key
            ));
        }
    }
    
    /* Controlla se esiste una chiave */
    public function isValue($key){
        $rows           = $this->databaseHelper->getResults(
                "SELECT * FROM [prefix]woopricesim_settings WHERE s_key = :s_key", array(
                            's_key'     => $key,
                ));
        
        if(count($rows) == 0 || empty($rows)){
            return false;
        }
        
        return true;
    }
   
    
}