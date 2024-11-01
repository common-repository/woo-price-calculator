<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

namespace WSF\Config;

/*AWS_PHP_HEADER*/

class Config {
    public function initialize(){
        return array(
            'plugin_label'      => 'Woo Price Calculator',
            'plugin_code'       => 'woo-price-calculator',
            'plugin_short_code' => 'aws_price_calc',
        );
    }
}
