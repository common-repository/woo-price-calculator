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
                <a href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'add')); ?>" class="btn btn-primary">
                    <i class="fa fa-calculator"></i> <?php echo $this->trans('wpc.calculator.new'); ?>
                </a>
                
                <a href="<?php echo $this->view['loadCalculatorUrl']; ?>" class="btn btn-primary">
                    <i class="fa fa-file-excel-o"></i> <?php echo $this->trans('wpc.calculator.load'); ?>
                </a>

                <?php if($this->getLicense() != 0): ?>
                <a href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'import')); ?>" class="btn btn-success">
                    <i class="fa fa-upload"></i> <?php echo $this->trans('apc.calculator.import'); ?>
                </a>
                <?php endif; ?>
                
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
                        <td><?php echo $row->description; ?></td>
                        <td><?php echo $this->trans("wpc.calculator.type.{$row->type}"); ?></td>
                        <td class="col-xs-3">

                            <div class="btn-group">
                                <a id="edit_calculator_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'edit','id' => $row->id)); ?>" class="btn btn-primary"><?php echo $this->trans('wpc.edit'); ?></a>
                                
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu" role="menu">
                                    <?php if($row->type == 'excel'): ?>
                                    <li>
                                        <a id="editmapping_calculator_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'loadermapping','calculator_id' => $row->id)); ?>">
                                            <?php echo $this->trans('wpc.calculator.edit_mapping'); ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>


                                    <li>
                                        <a id="conditional_logic_calculator_<?php echo $row->id; ?>" href="<?php echo ($this->getLicense() != 0)?$this->adminUrl(array('controller' => 'calculator', 'action' => 'conditionalLogic','id' => $row->id)):"#conditional_logic"; ?>">
                                            <?php echo $this->trans('wpc.conditional_logic'); ?>
                                        </a>
                                    </li>
                                                                        
                                    <li>
                                        <a id="export_calculator_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'export','id' => $row->id, 'raw' => 1)); ?>">
                                            <?php echo $this->trans('apc.calculator_export'); ?>
                                        </a>
                                    </li>
                                    
                                    <!-- Download Spreadsheet -->
                                    <?php if($row->type == 'excel'): ?>
                                    <li>
                                        <a id="download_calculator_<?php echo $row->id; ?>" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'downloadSpreadsheet','simulator_id' => $row->id, 'raw' => 1)); ?>">
                                            <?php echo $this->trans('calculator.download_spreadsheet'); ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <!-- /Download Spreadsheet -->
                                    
                                    <!-- Addons -->
                                    <?php echo apply_filters('awspc_filter_calculator_dropdown', null, $row); ?>
                                    <!-- /Addons -->

                                </ul>
                                
                            </div>
                            
                            <?php if(empty($row->system_created)): ?>
                            <a id="delete_calculator_<?php echo $row->id; ?>" onclick="return confirm('<?php echo $this->trans('wpc.delete.warning'); ?>');" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'delete', 'id' => $row->id)); ?>" class="btn btn-danger">
                                <?php echo $this->trans('wpc.delete'); ?>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
    
    <?php $this->renderView('app/upgrade_modal.php', array(
        'id'      => 'load_calculator',
        'title'   => 'aws.upgrade_modal.load_calculator.title',
    )); ?>
    
    <?php $this->renderView('app/upgrade_modal.php', array(
        'id'      => 'conditional_logic',
        'title'   => 'wpc.conditional_logic',
    )); ?>
    
</div>


<?php $this->renderView('app/footer.php'); ?>
