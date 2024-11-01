<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div id="text_options" style="display: none;">
    <div class="form-group">
        <label class="control-label col-sm-4" for="text_default_value"><?php echo $this->trans('Default Value'); ?></label>
        <div class="col-sm-8">
            <input class="form-control" name="text_default_value" type="text" value="<?php echo htmlspecialchars($this->decode($this->view['form']['text_default_value'])); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="text_regex"><?php $this->renderView('partial/help.php', array('text' => $this->trans('Yes, You can use a Regular Expression'))); ?> <?php echo $this->trans('Regex Validation'); ?></label>
        <div class="col-sm-8">
            <?php if($this->getLicense() == 0): ?>
                <input id="text_regex" class="form-control" name="text_regex" type="text" value="<?php echo htmlspecialchars($this->view['form']['text_regex']); ?>" />
            <?php else: ?>
                <div class="input-group">
                    <input id="text_regex" class="form-control" name="text_regex" type="text" value="<?php echo htmlspecialchars($this->view['form']['text_regex']); ?>" />
                    <span class="input-group-btn">
                        <button data-toggle="modal" data-target="#field_regex_modal" class="btn btn-default wsf-tooltip" title="<?php echo $this->trans('wpc.field.regex.tooltip'); ?>" type="button">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="text_regex_error">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Error message displayed for the Regular Expression'))); ?> <?php echo $this->trans('Regex Error Message'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="text_regex_error" type="text" value="<?php echo htmlspecialchars($this->view['form']['text_regex_error']); ?>" />
        </div>
    </div>
    
</div>

