<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<input type="hidden" class="wpc_product_id" value="<?php echo $this->view['product']['id']; ?>" />
<input type="hidden" class="wpc_simulator_id" value="<?php echo $this->view['simulator']->id; ?>" />
<input type="hidden" class="awspc-price-format" value="<?php echo $this->view['priceFormat']; ?>" />

<?php foreach($this->view['data'] as $key => $data): ?>
<input type="hidden" id="<?php echo $data['optionId']; ?>" value="<?php echo htmlspecialchars($data['options']); ?>" />
<?php endforeach; ?>

<?php echo $this->view['imagelist_modals']; ?>