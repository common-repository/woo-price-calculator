<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div class="wsf-bs wsf-wrap">
    <div class="mt-md">
        <?php if(!empty($this->view['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $this->view['error']; ?> 
            </div>
        <?php endif; ?>
    </div>
</div>
