<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/

/*
 * THEME_NAME: Example (UIkit)
 */
?>

<div class="wsf-uikit">
    <div class="uk-grid">
        <div class="uk-width-1-1">
            <h1>This is an example</h1>
        </div>
        <div class="uk-width-1-1">
            <div class="uk-form wpc-product-form">
                <?php foreach($this->view['data'] as $field): ?>
                    <div id="<?php echo $field['elementId']; ?>" class="form-group awspc-field-widget">
                        <legend><?php echo $field['field']->label; ?>:</legend>

                        <div class="uk-form-row awspc-field <?php echo $field['class']; ?>">
                            <?php echo $field['element']; ?>
                        </div>

                        <div class="awspc-field-error"></div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>