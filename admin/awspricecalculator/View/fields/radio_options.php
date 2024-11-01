<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 **/

/*AWS_PHP_HEADER*/
?>

<div id="radio_options" style="display: none;">

    <!-- Image Width -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="radio_image_width">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.radio.image_width.tooltip'))); ?> <?php echo $this->trans('field.form.radio.image_width'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="radio_image_width" type="text" value="<?php echo $this->view['form']['radio_image_width']; ?>" />
        </div>
    </div>
    <!-- /Image Width -->

    <!-- Image Height -->
    <div class="form-group">
        <label class="control-label col-sm-4" for="radio_image_height">
            <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.radio.image_height.tooltip'))); ?> <?php echo $this->trans('field.form.radio.image_height'); ?>
        </label>
        <div class="col-sm-8">
            <input class="form-control" name="radio_image_height" type="text" value="<?php echo $this->view['form']['radio_image_height']; ?>" />
        </div>
    </div>
    <!-- /Image Height -->

    <div class="form-group">
        <label class="control-label col-sm-4" for="default_status">
            <?php
            $this->renderView('partial/help.php',
                array('text' => $this->trans('wpc.field.radio.tooltip')));
            ?> <?php echo $this->trans('Picklist Items'); ?>
        </label>
        <div class="col-sm-8">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <button data-sortable-items="#radio_items_sortable" data-sortable-items-data="#radio_items" type="button" class="field_list_add btn btn-primary">
                        <i class="fa fa-plus"></i> <?php echo $this->trans('wpc.add'); ?>
                    </button>
                    <?php if(!isset($_GET['id'])) $_GET['id']="" ?>
                    <input id="import_from_file" value="Import from file" type="submit" formaction="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'importFile', 'type' => "radio", 'id' => $_GET['id'])); ?>" class="btn btn-success">

                    </input>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <ul id="radio_items_sortable">
                        <?php foreach($this->view['radio_items_data'] as $index => $item): ?>
                            <li data-id="<?php echo $item['id']; ?>" data-value="<?php echo $item['value']; ?>" data-label="<?php echo $item['label']; ?>" data-tooltip-message="<?php echo (isset($item['tooltip_message'])?$item['tooltip_message']:""); ?>" data-tooltip-position="<?php echo (isset($item['tooltip_position'])?$item['tooltip_position']:"none"); ?>" data-default-option="<?php echo (isset($item['default_option'])?$item['default_option']:"0"); ?>" data-order-details="<?php echo (isset($item['order_details'])?$item['order_details']:""); ?>" data-image="<?php echo (isset($item['image'])?$item['image']:""); ?>">
                                <a class="btn btn-danger js-remove" data-sortable-items="#radio_items_sortable" data-sortable-items-data="#radio_items">
                                    <i class="fa fa-times"></i>
                                </a>

                                <a class="btn btn-primary sortable-edit" data-sortable-items="#radio_items_sortable" data-sortable-items-data="#radio_items">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <?php if(!empty($item['image'])): ?><img class="sortable-item-image" src="<?php echo $item['image']; ?>" /><?php endif; ?>
                                <?php echo $item['label']; ?> <i>[Value: <?php echo $item['value']; ?>]</i>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <input type="hidden" id="radio_items" name="radio_items" value="<?php $this->e($this->view['form']['radio_items'], true); ?>" />

        </div>
    </div>
</div>
