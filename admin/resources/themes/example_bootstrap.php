<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/

/*
 * THEME_NAME: Example (Bootstrap)
 */
?>

<div class="wsf-bs">
    <div class="row">
        <div class="col-xs-12">
            <h1>This is an example</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 wpc-product-form">
            <?php foreach($this->view['data'] as $field): ?>
                <div id="<?php echo $field['elementId']; ?>" class="form-group awspc-field-widget">
                    <label for=""><?php echo $field['field']->label; ?>:</label>

                    <div class="awspc-field <?php echo $field['class']; ?>">
                        <input class="form-control" name="<?php echo $field['elementId']; ?>" type="text" value="<?php echo $field['value']; ?>" />
                    </div>

                    <div class="awspc-field-error"></div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>