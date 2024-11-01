<div class="wsf-bs wsf-wrap awspc-select-products-modal remodal" data-remodal-id="select-products-modal">
    <button data-remodal-action="close" class="remodal-close"></button>
    
    <h3><?php echo $this->trans('aws.select_products.title'); ?></h3>

    <div class="row">
        <div class="col-xs-12">
            <table id="awspricecalculator_select_products_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="text-center"><?php echo $this->trans('aws.select_products.column.product_id'); ?></th>
                        <th class="text-center"><?php echo $this->trans('aws.select_products.column.product_name'); ?></th>
                        <th class="text-center"><?php echo $this->trans('aws.select_products.column.actions'); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <button data-remodal-action="cancel" class="remodal-cancel">
                <?php echo $this->trans('aws.select_products.close'); ?>
            </button>
        </div>
    </div>


</div>