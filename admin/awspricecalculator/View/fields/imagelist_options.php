<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 **/

/*AWS_PHP_HEADER*/
?>

<div id="imagelist_options" style="display: none;width: 100%">

    <!-- Image Field Width -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="imagelist_field_image_width">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.image_list.field_image_width.tooltip'))); ?> <?php echo $this->trans('field.form.image_list.field_image_width'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="imagelist_field_image_width" type="text" value="<?php echo $this->view['form']['imagelist_field_image_width']; ?>" />
        </div>
    </div>
    <!-- /Image Field Width -->

    <!-- Image Field Height -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="imagelist_field_image_height">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.image_list.field_image_height.tooltip'))); ?> <?php echo $this->trans('field.form.image_list.field_image_height'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="imagelist_field_image_height" type="text" value="<?php echo $this->view['form']['imagelist_field_image_height']; ?>" />
        </div>
    </div>
    <!-- /Image Field Height -->

    <!-- Image Popup Width -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="imagelist_popup_image_width">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.image_list.popup_image_width.tooltip'))); ?> <?php echo $this->trans('field.form.image_list.popup_image_width'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="imagelist_popup_image_width" type="text" value="<?php echo $this->view['form']['imagelist_popup_image_width']; ?>" />
        </div>
    </div>
    <!-- /Image Popup Width -->

    <!-- Image Popup Height -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="imagelist_popup_image_height">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.image_list.popup_image_height.tooltip'))); ?> <?php echo $this->trans('field.form.image_list.popup_image_height'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="imagelist_popup_image_height" type="text" value="<?php echo $this->view['form']['imagelist_popup_image_height']; ?>" />
        </div>
    </div>
    <!-- /Image Popup Height -->

    <!-- Image Items -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="default_status">
            <?php
            $this->renderView('partial/help.php',
                array('text' => $this->trans('wpc.field.picklist.tooltip')));
            ?> <?php echo $this->trans('Picklist Items'); ?>
        </label>
        <div class="col-sm-8">

            <div class="row">
                <div class="col-xs-12 text-center">
                    <button data-sortable-items="#imagelist_items_sortable" data-sortable-items-data="#imagelist_items" type="button" class="field_list_add btn btn-primary">
                        <i class="fa fa-plus"></i> <?php echo $this->trans('wpc.add'); ?>
                    </button>
                    <?php if(!isset($_GET['id'])) $_GET['id']="" ?>
                    <input id="import_from_file" value="Import from file" type="submit" formaction="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'importFile', 'type' => "imagelist", 'id' => $_GET['id'])); ?>" class="btn btn-success">

                    </input>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <ul id="imagelist_items_sortable">
                        <?php foreach($this->view['imagelist_items_data'] as $index => $item): ?>
                            <li data-id="<?php echo $item['id']; ?>" data-value="<?php echo $item['value']; ?>" data-label="<?php echo $item['label']; ?>" data-tooltip-message="<?php echo (isset($item['tooltip_message'])?$item['tooltip_message']:""); ?>" data-tooltip-position="<?php echo (isset($item['tooltip_position'])?$item['tooltip_position']:"none"); ?>" data-default-option="<?php echo (isset($item['default_option'])?$item['default_option']:"0"); ?>" data-order-details="<?php echo (isset($item['order_details'])?$item['order_details']:""); ?>" data-image="<?php echo (isset($item['image'])?$item['image']:""); ?>">
                                <a class="btn btn-danger js-remove" data-sortable-items="#imagelist_items_sortable" data-sortable-items-data="#picklist_items">
                                    <i class="fa fa-times"></i>
                                </a>

                                <a class="btn btn-primary sortable-edit" data-sortable-items="#imagelist_items_sortable" data-sortable-items-data="#imagelist_items">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <img class="sortable-item-image" src="<?php echo $item['image']; ?>" /> <?php echo $item['label']; ?> <i>[Value: <?php echo $item['value']; ?>]</i>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <input type="hidden" id="imagelist_items" name="imagelist_items" value="<?php $this->e($this->view['form']['imagelist_items'], true); ?>" />

        </div>
    </div>
    <!-- /Image Items -->

</div>
