<?php
/**
 * Created by PhpStorm.
 * User: naidi
 * Date: 26/03/18
 * Time: 12:18
 */

?>

    <div class="wsf-bs wsf-wrap">
        <?php if(isset($this->view['errors'])) if(count($this->view['errors']) != 0): ?>
            <div class="alert alert-danger">
                <?php echo $this->view['errors']; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-info">
                    <i class="fa fa-question-circle"></i> <?php echo $this->trans($this->view['type'].'.import.description'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center">
                <h2><?php echo $this->trans('field.import.title').$this->view['type']; ?></h2>
                <strong><?php echo $this->trans('field.import.file_selection'); ?>:</strong>
            </div>
        </div>


        <div class="row">
            <div class="col-xs-12 text-center ma-sm">
                <center>

                    <form id="import_items_from_file" method="post" field_id="<?php echo $_GET['id']; ?>" action="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'importFile' , 'type'=> $this->view['type'], 'id'=>$_GET['id'], 'label' => $this->view['form']['label'],
                        'short_label'                                       => $this->view['form']['short_label'],
                        'description'                                       => $this->view['form']['description'],
                        'check_errors'                                      => $this->view['form']['check_errors'],
                        'required'                                          => $this->view['form']['required'],
                        'required_error_message'                            => $this->view['form']['required_error_message'],
                        
                        'hide_field_product_page'                           => $this->view['form']['hide_field_product_page'],
                        'hide_field_cart_if_empty'                          => $this->view['form']['hide_field_cart_if_empty'],
                        'hide_field_checkout_if_empty'                      => $this->view['form']['hide_field_checkout_if_empty'],
                        'hide_field_cart'                                   => $this->view['form']['hide_field_cart'],
                        'hide_field_checkout'                               => $this->view['form']['hide_field_checkout'],
                        'hide_field_order'                                  => $this->view['form']['hide_field_order'],
                        
                        'radio_image_width'                                 => $this->view['form']['radio_image_width'],
                        'radio_image_height'                                => $this->view['form']['radio_image_height'],
                        'imagelist_field_image_width'                       => $this->view['form']['imagelist_field_image_width'],
                        'imagelist_field_image_height'                      => $this->view['form']['imagelist_field_image_height'],
                        'imagelist_popup_image_width'                       => $this->view['form']['imagelist_popup_image_width'],
                        'imagelist_popup_image_height'                      => $this->view['form']['imagelist_popup_image_height'] )) ?>" enctype="multipart/form-data">

                        <input id="file_upload" name="file_upload" type="file" />
                        <button id="cancel_import" type="button" class="btn btn-danger">Cancel</button>

                        <input id="upload_list_items" class="btn btn-primary ma-sm" type="submit" value="<?php echo $this->trans('calculator.import.button') ?>" />


                        <input type="hidden" name="task" value="import_calculator" />


                    </form>
                </center>
            </div>
        </div>
    </div>

<?php $this->renderView('app/footer.php'); ?>