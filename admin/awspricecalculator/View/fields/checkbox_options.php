<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div id="checkbox_options" style="display: none;width: 100%">
    <div class="form-group">
        <label class="control-label col-sm-4" for="default_status"><?php echo $this->trans('Default Status'); ?></label>
        <div class="col-sm-8">
            <select class="form-control" name="checkbox_default_status">
                <option value="0" <?php if($this->view['form']['checkbox_default_status'] == 0){echo"selected";}?>><?php echo $this->trans('Unchecked'); ?></option>
                <option value="1" <?php if($this->view['form']['checkbox_default_status'] == 1){echo"selected";}?>><?php echo $this->trans('Checked'); ?></option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="pwd">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('This is the value used in formula when the checkbox is checked'))); ?> <?php echo $this->trans('Checked Value'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="checkbox_check_value" type="text" value="<?php echo $this->view['form']['checkbox_check_value']; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="pwd">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('This is the value used in formula when the checkbox is unchecked'))); ?> <?php echo $this->trans('Unchecked Value'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="checkbox_uncheck_value" type="text" value="<?php echo $this->view['form']['checkbox_uncheck_value']; ?>" />
        </div>
    </div>
</div>
