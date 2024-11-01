<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/

?>

<div class="wsf-bs">
    <div class="alert alert-<?php echo (isset($this->view['type'])?$this->view['type']:"success"); ?> success mt-md">
        <h4><?php echo $this->view['message']; ?></h4>

        <?php if(!empty($this->view['url'])): ?>
            <?php echo $this->trans('Click'); ?> <a href="<?php echo $this->view['url']; ?>"><?php echo $this->trans('here'); ?></a> <?php echo $this->trans('to go back'); ?>.
        <?php endif; ?>
    </div>
</div>

