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

class RegexModel {
    
    var $wsf;
    var $db;
    
    public function __construct(FrameworkHelper $wsf){
        $this->wsf  = $wsf;
        
        $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));
    }
    
    /*
     * Ritorna un simulatore utilizzando l'ID
     */
    public function get($id){
        return $this->databaseHelper->getRow("SELECT * FROM [prefix]woopricesim_regex WHERE id = :id", array(
            'id'    => $id,
        ));
    }
    
    /*
     * Ritorna la lista di tutti i simulatori
     */
    public function get_list(){
        return $this->databaseHelper->getResults("SELECT * FROM [prefix]woopricesim_regex");
    }
    
    public function exchangeArray($object){
        return array(
            "name"              => $object->name,
            "regex"             => $object->regex,
            "user_created"      => $object->user_created,
        );
    }
    
    public function save($data, $id = null){
            $record = array(
               "name"           => $data['name'],
               "regex"          => $data['regex'],
               "user_created"   => 1,
            );
                        
            if(empty($id)){
                return $this->databaseHelper->insert("[prefix]woopricesim_regex", $record);
            }else{
                $this->databaseHelper->update("[prefix]woopricesim_regex", $record, array(
                    'id' => $id
                ));
            }
    }
    
    public function delete($id){
        $this->databaseHelper->delete("[prefix]woopricesim_regex", array("id" => $id));
    }
    
}
