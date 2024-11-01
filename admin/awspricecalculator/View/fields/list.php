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
    <div class="ma-md">
        <div class="row">
            <div class="col-xs-12 text-center">
                <a href="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'form')); ?>" class="btn btn-primary">
                    <i class="fa fa-pencil"></i> <?php echo $this->trans('wpc.field.new'); ?>
                </a>
            </div>
        </div>
        
        <table class="table table-striped table-bordered data-table" width="100%">
            <thead>
                <tr>
                    <?php foreach ($this->view['list_header'] as $headerKey => $headerLabel): ?>
                        <th class="text-center"><?php echo $headerLabel; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($this->view['list_rows'] as $row): ?>
                    <tr>
                        <td><?php echo $row->label; ?></td>
                        <td>aws_price_calc_<?php echo $row->id; ?></td>
                        <td><?php echo $this->trans("aws.field.mode.{$row->mode}"); ?></td>
                        <td><?php echo empty($row->type)?$this->trans("wpc.field.type.numeric"):$this->trans("wpc.field.type.{$row->type}"); ?></td>
                        <td class="col-xs-2"><?php echo $row->description; ?></td>
                        <td class="col-xs-2">
                            <!-- EDIT -->
                            <a id="edit_field_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'form', 'id' => $row->id)); ?>" class="btn btn-primary"><?php echo $this->trans('wpc.edit'); ?></a>

                            <!-- CLONE -->
                            <!-- WPC-PRO -->
                            <?php if($this->getLicense() == 1): ?>
                            <a id="clone_field_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'form', 'clone_id' => $row->id)); ?>" class="btn btn-info"><?php echo $this->trans('wpc.clone'); ?></a>
                            <?php endif; ?>
                            <!-- /WPC-PRO -->
                            
                            <!-- DELETE -->
                            <?php if(empty($row->system_created)): ?>
                            <a id="delete_field_<?php echo $row->id; ?>" onclick="return confirm('<?php echo $this->trans('wpc.delete.warning'); ?>');" href="<?php echo $this->adminUrl(array('controller' => 'field', 'action' => 'delete', 'id' => $row->id)); ?>" class="btn btn-danger">
                                <?php echo $this->trans('wpc.delete'); ?>
                            </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
</div>

<?php $this->renderView('app/footer.php'); ?>