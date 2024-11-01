<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<span class="woo-price-calculator-tooltip" title="<?php echo $this->view['text']; ?>">
    <img width="<?php if(empty($this->view['size'])){echo "13";}else{echo $this->view['size'];} ?>" src="<?php echo $this->getResourcesUrl('assets/images/help.png'); ?>" />
</span>