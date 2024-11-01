<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<span class="wpc-cart-container">
    <?php if($this->getLicense() == 1): ?>
    <?php else: ?>
        <?php echo $this->view['price']; ?>
    <?php endif; ?>
</span>

