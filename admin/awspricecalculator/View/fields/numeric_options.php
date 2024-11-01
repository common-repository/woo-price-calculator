<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div id="numeric_options" style="display: none;width:100%">
    
    <div class="form-group">
        <label class="control-label col-sm-4" for="default_value">
            <?php echo $this->trans('Default Value'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control wpc-numeric-decimals" name="numeric_default_value" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_default_value']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_max_value">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Maximum value the field can reach'))); ?> <?php echo $this->trans('Max Value'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control wpc-numeric-decimals" name="numeric_max_value" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_max_value']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_max_value_error">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Message displayed if max value is reached'))); ?> <?php echo $this->trans('Max Value Error Message'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="numeric_max_value_error" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_max_value_error']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_min_value">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Minimum value the field can reach'))); ?> <?php echo $this->trans('Min Value'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control wpc-numeric-decimals" name="numeric_min_value" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_min_value']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_min_value_error">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Message displayed if minumum value is reached'))); ?> <?php echo $this->trans('Min Value Error Message'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="numeric_min_value_error" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_min_value_error']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_decimals">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Insert the number of decimal digits'))); ?> <?php echo $this->trans('Decimals'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control wpc-numeric" name="numeric_decimals" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_decimals']); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="numeric_decimal_separator">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('The decimal separator'))); ?> <?php echo $this->trans('Decimal Separator'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="numeric_decimal_separator" type="text" value="<?php echo htmlspecialchars($this->view['form']['numeric_decimal_separator']); ?>" />
        </div>
    </div>

</div>
