<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div class="awspc-field-widget" data-id="<?php echo $this->view['elementId']; ?>">
    <div class="awspc-field <?php echo $this->view['class']; ?>">
        <?php echo $this->view['element']; ?>
    </div>

    <div class="awspc-field-error">
        <?php if(!empty($this->view['errors'])): ?>
        <?php echo implode("<br/>", $this->view['errors']); ?>
        <?php endif; ?>
    </div>
    
    <?php if(!empty($this->view['field']->text_after_field)): ?>
        <div class="awspc-text-after-field">
            <?php echo $this->view['field']->text_after_field; ?>
        </div>
    <?php endif; ?>
</div>

