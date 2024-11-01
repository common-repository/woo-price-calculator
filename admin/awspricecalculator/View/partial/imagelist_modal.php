<!-- Modal for Image List -->
<?php foreach($this->view['simulator_fields'] as $imagelistField): ?>
    <?php if($imagelistField->type == "imagelist"): ?>
    <?php $imagelistOptions     = json_decode($imagelistField->options, true); ?>

    <?php
        $height          = (empty($imagelistOptions['imagelist']['imagelist_popup_image_height']))?"":"height: {$imagelistOptions['imagelist']['imagelist_popup_image_height']};";
        $width           = (empty($imagelistOptions['imagelist']['imagelist_popup_image_width']))?"":"width: {$imagelistOptions['imagelist']['imagelist_popup_image_width']};";
        $defaultValue    = $this->view['data']["aws_price_calc_{$imagelistField->id}"]['value'];
    ?>

        <div class="remodal awspc-modal-imagelist" data-remodal-id="awspc_modal_imagelist_<?php echo $imagelistField->id; ?>">   
            <div class="awspc-modal-imagelist-title">
                <h3><?php echo $this->userTrans($imagelistField->label); ?></h3>
                <small><?php echo $this->trans('imagelist.modal.description'); ?></small>
            </div>

            <div class="awspc-modal-imagelist-table">
                <table>
                    
                    <!-- Iterating Image List Items -->
                    <?php foreach($this->view['fieldHelper']->getFieldItems('imagelist', $imagelistField) as $item): ?>
                    <tr class="awspc-modal-imagelist-row <?php echo ($defaultValue == $item['id'])?"awspc-modal-imagelist-clicked":""; ?>" data-item-id="<?php echo $item['id']; ?>" data-imagelist-id="<?php echo $imagelistField->id; ?>" data-label="<?php echo $item['label']; ?>" data-cart-item-key="<?php echo $this->view['cartItemKey']; ?>">
                        <td class="awspc-modal-imagelist-image-column"><img style="<?php echo $width; ?><?php echo $height; ?>" src="<?php echo $item['image']; ?>" /></td>
                        <td class="awspc-modal-imagelist-image-text"><?php echo $item['label']; ?></td>
                    </tr>
                    
                    <?php endforeach; ?>
                    <!-- /Iterating Image List Items -->
                    
                </table>
            </div>

            <div class="awspc-modal-imagelist-footer">
                <button data-remodal-action="cancel" class="button"><?php echo $this->trans('wpc.close'); ?></button>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<!-- /Modal for Image List -->