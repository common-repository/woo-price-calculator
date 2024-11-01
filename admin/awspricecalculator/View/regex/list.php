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
                <a href="<?php echo $this->adminUrl(array('controller' => 'regex', 'action' => 'form')); ?>" class="btn btn-primary">
                    <i class="fa fa-archive"></i> <?php echo $this->trans('wpc.regex.new'); ?>
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
                        <td><?php echo $row->name; ?></td>
                        <td><?php echo ($row->user_created == 1)?$this->trans('wpc.yes'):$this->trans('wpc.no'); ?></td>
                        <td class="col-xs-2">
                            <?php if($row->user_created == 0): ?>
                                <i><?php echo $this->trans('wpc.regex.system_created'); ?></i>
                            <?php else: ?>
                                <a href="<?php echo $this->adminUrl(array('controller' => 'regex', 'action' => 'form','id' => $row->id)); ?>" class="btn btn-primary"><?php echo $this->trans('wpc.edit'); ?></a>
                                <a onclick="return confirm('<?php echo $this->trans('wpc.delete.warning'); ?>');" href="<?php echo $this->adminUrl(array('controller' => 'regex', 'action' => 'delete', 'id' => $row->id)); ?>" class="btn btn-danger"><?php echo $this->trans('wpc.delete'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
</div>

<?php $this->renderView('app/footer.php'); ?>