<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<span class="wpc-edit-icon <?php echo $this->view['cartEditButtonClass']; ?>" data-remodal-target="wpc_cart_item_<?php echo $this->view['cartItemKey']; ?>">
    <span class="cart-text">
        <?php echo $this->mixTrans('wpc.cart.edit'); ?>
    </span>
</span>