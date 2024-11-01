<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div id="numeric_options" style="width:100%">
    

    <div class="form-group">
        <label class="control-label col-sm-4" for="output_numeric_decimals"><?php $this->renderView('partial/help.php', array('text' => $this->trans('Insert the number of decimal digits'))); ?> <?php echo $this->trans('Decimals'); ?></label>
        <div class="col-sm-8">
            <input class="form-control wpc-numeric" name="output_numeric_decimals" type="text" value="<?php echo htmlspecialchars($this->view['form']['output_numeric_decimals']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="output_numeric_thousand_separator"><?php $this->renderView('partial/help.php', array('text' => $this->trans('The thousand separator'))); ?> <?php echo $this->trans('Thousand Separator'); ?></label>
        <div class="col-sm-8">
            <input class="form-control" name="output_numeric_thousand_separator" type="text" value="<?php echo htmlspecialchars($this->view['form']['output_numeric_thousand_separator']); ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-4" for="output_numeric_decimal_separator"><?php $this->renderView('partial/help.php', array('text' => $this->trans('The decimal separator'))); ?> <?php echo $this->trans('Decimal Separator'); ?></label>
        <div class="col-sm-8">
            <input class="form-control" name="output_numeric_decimal_separator" type="text" value="<?php echo htmlspecialchars($this->view['form']['output_numeric_decimal_separator']); ?>" />
        </div>
    </div>

</div>
