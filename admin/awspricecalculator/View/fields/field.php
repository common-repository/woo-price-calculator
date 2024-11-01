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

            <form id="wpc_field_form" action="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'form', 'id' => $this->view['id'])); ?>" method="POST">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <h4>
                            <i class="fa fa-pencil"></i> <?php echo $this->view['title'] . ' ' . $this->trans('Calculator Field'); ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-6 col-xs-offset-2">
                                <div class="form-horizontal wpc-form">

                                    <?php if(!empty($this->view['id'])): ?>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4 required" for="name">
                                                <?php $this->renderView('partial/help.php', array('text' => $this->trans('field.form.field_name.tooltip'))); ?> <?php echo $this->trans('Field Name'); ?>
                                            </label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="calculator_name" type="text" value="<?php echo $this->view['fieldHelper']->getFieldName($this->view['id']); ?>" readonly="readonly" />
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label class="control-label col-sm-4 required" for="label">
                                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Just to remember what it does'))); ?> <?php echo $this->trans('Field Label'); ?>
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" name="label" type="text" value="<?php echo htmlspecialchars($this->view['form']['label']); ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="short_label">
                                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.field.form.short_label.help'))); ?> <?php echo $this->trans('wpc.field.form.short_label'); ?>
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" name="short_label" type="text" value="<?php echo htmlspecialchars($this->view['form']['short_label']); ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="description">
                                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Just to remember what it does'))); ?> <?php echo $this->trans('wpc.field.form.description'); ?>
                                        </label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="description"><?php echo htmlspecialchars($this->view['form']['description']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-4 required" for="label">
                                            <?php
                                            $this->renderView('partial/help.php', array('text' => $this->trans('aws.field.mode.tooltip')));
                                            ?> <?php echo $this->trans('aws.field.mode'); ?>
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="field_mode" name="mode">
                                                <option value="input" <?php if($this->view['form']['mode'] == "input"){echo 'selected="selected"';} ?>><?php echo $this->trans('aws.field.mode.input'); ?></option>
                                                <option value="output" <?php if($this->view['form']['mode'] == "output"){echo 'selected="selected"';} ?>><?php echo $this->trans('aws.field.mode.output'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div id="input_fields_options">
                                        
                                        
                                        <div class="form-group">
                                            <label class="control-label col-sm-4 required" for="label">
                                                <?php
                                                $this->renderView('partial/help.php', array(
                                                        'text' => $this->trans('Type of field') . ":" . "<br/>" .
                                                            "- <b>" . $this->trans('Checkbox') . "</b>: " . $this->trans('Accepts only two states') . "<br/>" .
                                                            "- <b>" . $this->trans('Numeric') . "</b>: " . $this->trans('Accepts only numbers') . "<br/>" .
                                                            "- <b>" . $this->trans('Picklist') . "</b>: " . $this->trans('List of items') . "<br/>" .
                                                            "- <b>" . $this->trans('field.form.field_type.imagelist') . "</b>: " . $this->trans('field.form.field_type.imagelist.tooltip') . "<br/>" .
                                                            "- <b>" . $this->trans('Text') . "</b>: " . $this->trans('Accepts whatever you want') . "<br/>" .
                                                            "- <b>" . $this->trans('wpc.date') . "</b>: " . $this->trans('wpc.date.description') . "<br/>" .
                                                            "- <b>" . $this->trans('wpc.time') . "</b>: " . $this->trans('wpc.time.description') . "<br/>" .
                                                            "- <b>" . $this->trans('wpc.datetime') . "</b>: " . $this->trans('wpc.datetime.description') . "<br/>" .
                                                            "- <b>" . $this->trans('wpc.radio') . "</b>: " . $this->trans('wpc.radio.description') . "<br/>"

                                                    )
                                                );
                                                ?> <?php echo $this->trans('Field Type'); ?>
                                            </label>

                                            <div  class="col-sm-8">
                                                <select class="form-control" id="field_type" name="type">
                                                    <option value="">--<?php echo $this->trans('Select'); ?>--</option>
                                                    
                                                    <?php foreach($this->view['fieldTypes'] as $fieldTypeKey => $fieldTypeLabel): ?>
                                                        <option value="<?php echo $fieldTypeKey; ?>" <?php if($this->view['form']['type'] == $fieldTypeKey){echo 'selected="selected"';} ?>><?php echo $fieldTypeLabel; ?></option>
                                                    <?php endforeach; ?>

                                                </select>
                                            </div>

                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="label">
                                                <?php
                                                $this->renderView('partial/help.php', array('text' => $this->trans('aws.field.required.tooltip')));
                                                ?> <?php echo $this->trans('aws.field.required'); ?>
                                            </label>

                                            <div  class="col-sm-8">
                                                <select class="form-control" id="field_required" name="required">
                                                    <option value="0" <?php if($this->view['form']['required'] == false){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.no'); ?></option>
                                                    <option value="1" <?php if($this->view['form']['required'] == true){echo 'selected="selected"';} ?>><?php echo $this->trans('wpc.yes'); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="label">
                                                <?php
                                                $this->renderView('partial/help.php', array('text' => $this->trans('aws.field.required_error_message.tooltip')));
                                                ?> <?php echo $this->trans('aws.field.required_error_message'); ?>
                                            </label>

                                            <div  class="col-sm-8">
                                                <input class="form-control" name="required_error_message" type="text" value="<?php echo htmlspecialchars($this->view['form']['required_error_message']); ?>" />
                                            </div>
                                        </div>
                                        
                                        <?php foreach($this->view['fieldOptions'] as $fieldTypeKey => $fieldOptions): ?>
                                            <?php echo $fieldOptions; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div id="output_fields_options">
                                        
                                        <?php
                                        $this->renderView('fields/output_numeric_options.php', array(
                                            'form'      => $this->view['form']
                                        ));
                                        ?>
                                    </div>



                                    <div class="form-group">
                                        <div class="col-sm-10 col-sm-offset-3 text-center">
                                            <button id="wpc_field_form_submit" type="button" class="btn btn-primary" <?php echo ($this->view['form']['system_created'] == true)?'disabled="disabled"':''; ?>>
                                                <i class="fa fa-floppy-o"></i> <?php echo $this->trans('wpc.save'); ?>
                                            </button>


                                        </div>
                                    </div>

                                    <input type="hidden" name="task" value="field_form" />
                                    <input type="hidden" id="items_list_id" name="items_list_id" value="<?php echo $this->view['form']['items_list_id']; ?>" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <!-- Modal -->
        <div id="field_choise_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $this->trans('wpc.field.choise.modal.header'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    <?php echo $this->trans('wpc.field.choise.modal.text'); ?>
                                </label>
                                <div class="col-sm-8">
                                    <input id="field_choise_modal_text" type="text" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    <?php echo $this->trans('wpc.field.choise.modal.value'); ?>
                                </label>
                                <div class="col-sm-8">
                                    <input id="field_choise_modal_value" type="text" class="form-control" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="field_choise_modal_ok" type="button" class="btn btn-primary"><?php echo $this->trans('wpc.ok'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->trans('wpc.close'); ?></button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal: Regex -->
        <div id="field_regex_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $this->trans('wpc.field.regex.modal.header'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <select id="field_regex_modal_list" class="form-control">
                            <?php foreach($this->view['regex_list'] as $regex): ?>
                                <option value="<?php $this->e($regex->regex, true); ?>"><?php echo $regex->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button id="field_regex_modal_ok" type="button" class="btn btn-primary"><?php echo $this->trans('wpc.ok'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->trans('wpc.close'); ?></button>
                    </div>
                </div>

            </div>
        </div>


        <!-- Modal: Add/Edit List Element -->
        <div id="field_list_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $this->trans('wpc.field.list_add.modal.header'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-horizontal">
                            <!-- General List Elements -->
                            <div class="form-group">
                                <label class="control-label col-sm-3 required" for="field_list_modal_label">
                                    <?php echo $this->trans('wpc.field.list.add.label'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control required" id="field_list_modal_label">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="field_list_modal_value">
                                    <?php echo $this->trans('wpc.field.list.add.value'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="field_list_modal_value">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="field_list_modal_default_option">
                                    <?php echo $this->trans('wpc.field.list.add.default_option'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <select id="field_list_modal_default_option" class="form-control">
                                        <option value="0" selected><?php echo $this->trans('wpc.no'); ?></option>
                                        <option value="1"><?php echo $this->trans('wpc.yes'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="field_list_modal_order_details">
                                    <?php echo $this->trans('wpc.field.list.add.order_details'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="field_list_modal_order_details">
                                </div>
                            </div>
                            <!-- /General List Elements -->

                            <!-- Radio Element -->
                            <div class="form-group modal-radio-elements">
                                <label class="control-label col-sm-3" for="field_list_modal_tooltip_position">
                                    <?php echo $this->trans('wpc.field.list.add.tooltip.position'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <select id="field_list_modal_tooltip_position" class="form-control">
                                        <option value="none" selected><?php echo $this->trans('wpc.none'); ?></option>
                                        <option value="left"><?php echo $this->trans('wpc.left'); ?></option>
                                        <option value="right"><?php echo $this->trans('wpc.right'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group modal-radio-elements">
                                <label class="control-label col-sm-3" for="field_list_modal_tooltip_message">
                                    <?php echo $this->trans('wpc.field.list.add.tooltip.message'); ?>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="field_list_modal_tooltip_message">
                                </div>
                            </div>
                            <!-- /Radio Element -->

                            <!-- Image List Element -->

                            <!-- Image Selection -->
                            <div class="form-group modal-image-block">
                                <label class="control-label col-sm-2" for="field_list_modal_image">
                                    <?php echo $this->trans('Image'); ?>
                                </label>
                                <div class="col-sm-8">
                                    <div>
                                        <img class="awspc_media_manager" data-image-selector="#field_list_modal_image" data-image-preview-selector="#field_list_modal_image_preview" src="<?php echo $this->getImageUrl('image.png'); ?>" data-empty-img="<?php echo $this->getImageUrl('image.png'); ?>" id="field_list_modal_image_preview" />
                                    </div>

                                    <input type="hidden" id="field_list_modal_image" value="" />
                                </div>
                            </div>
                            <!-- /Image List Element -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="field_list_modal_ok" type="button" class="btn btn-primary"><?php echo $this->trans('wpc.ok'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->trans('wpc.close'); ?></button>
                    </div>
                </div>

            </div>

            <input type="hidden" id="field_list_mode" value="" />
        </div>

    </div>

<?php $this->renderView('app/footer.php'); ?>