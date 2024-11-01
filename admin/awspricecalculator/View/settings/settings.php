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
        <?php if(count($this->view['errors']) != 0): ?>
            <div class="alert alert-danger">
                <?php echo implode("<br/>", $this->view['errors']); ?> 
            </div>
        <?php endif; ?>

        <form id="wpc_field_form" action="<?php echo $this->adminUrl(array('controller' => 'settings', 'action' => 'index')); ?>" method="POST">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <h4>
                        <i class="fa fa-cog"></i> <?php echo $this->trans('wpc.settings'); ?>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-horizontal wpc-form">
                                
                               <!-- disable_ajax_price_product_page -->
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="disable_ajax_price_product_page">
                                       <?php echo $this->trans('settings.form.disable_ajax_price_product_page.label'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                       <select class="form-control" name="disable_ajax_price_product_page">
                                           <option value="0" <?php if(empty($this->view['form']['disable_ajax_price_product_page'])){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.no'); ?></option>
                                           <option value="1" <?php if($this->view['form']['disable_ajax_price_product_page'] == true){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.yes'); ?></option>
                                       </select>
                                   </div>
                               </div>
                               
                               <!-- single_product_ajax_hook_class -->
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="single_product_ajax_hook_class">
                                       <?php $this->renderView('partial/help.php', array('text' => $this->trans('settings.form.single_product_ajax_hook_class.tooltip'))); ?> <?php echo $this->trans('settings.form.single_product_ajax_hook_class.label'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                       <input type="text" class="form-control" name="single_product_ajax_hook_class" value="<?php echo htmlspecialchars($this->view['form']['single_product_ajax_hook_class']); ?>" />
                                   </div>
                               </div>
                                
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="cart_edit_button_class">
                                       <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.settings.form.cart_edit_button_class.tooltip'))); ?> <?php echo $this->trans('wpc.settings.form.cart_edit_button_class.label'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                       <input type="text" class="form-control" name="cart_edit_button_class" value="<?php echo htmlspecialchars($this->view['form']['cart_edit_button_class']); ?>" />
                                   </div>
                               </div>
                               
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="cart_edit_button_position">
                                       <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.settings.form.cart_edit_button_position.tooltip'))); ?> <?php echo $this->trans('wpc.settings.form.cart_edit_button_position.label'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                       <select class="form-control" name="cart_edit_button_position">
                                           <option value="before-price" <?php if($this->view['form']['cart_edit_button_position'] == "before-price"){echo 'selected="selected"';} ?>><?php echo $this->trans('settings.form.cart_edit_button_position.before_price'); ?></option>
                                           <option value="after-price" <?php if($this->view['form']['cart_edit_button_position'] == "after-price"){echo 'selected="selected"';} ?>><?php echo $this->trans('settings.form.cart_edit_button_position.after_price'); ?></option>
                                       </select>
                                   </div>
                               </div>
                                
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="hide_alert_errors">
                                      <?php echo $this->trans('settings.form.hide_alert_errors'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                       <select class="form-control" name="hide_alert_errors">
                                           <option value="0" <?php if(empty($this->view['form']['hide_alert_errors'])){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.no'); ?></option>
                                           <option value="1" <?php if($this->view['form']['hide_alert_errors'] == 1){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.yes'); ?></option>
                                       </select>
                                   </div>
                               </div>
                               
                               <div class="form-group">
                                   <label class="control-label col-sm-4" for="custom_css">
                                       <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.settings.form.custom_css.tooltip'))); ?> <?php echo $this->trans('wpc.settings.form.custom_css.label'); ?>
                                   </label>
                                   <div class="col-sm-8">
                                        <textarea class="form-control" name="custom_css" rows="20"><?php echo htmlspecialchars($this->view['form']['custom_css']); ?></textarea>
                                   </div>
                               </div>

                                <div class="form-group">
                                    <div class="col-sm-10 col-sm-offset-3 text-center">
                                        <button id="wpc_field_form_submit" type="button" class="btn btn-primary">
                                            <i class="fa fa-floppy-o"></i> <?php echo $this->trans('wpc.save'); ?>
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="task" value="save" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

</div>

<?php $this->renderView('app/footer.php'); ?>
