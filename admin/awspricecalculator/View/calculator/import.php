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
        
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info">
                <i class="fa fa-question-circle"></i> <?php echo $this->trans('calculator.import.description'); ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 text-center">
            <h2><?php echo $this->trans('calculator.import.title'); ?></h2>
            <strong><?php echo $this->trans('calculator.import.file_selection'); ?>:</strong>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 text-center ma-sm">
            <center>
                <form method="post" action="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'import')); ?>" enctype="multipart/form-data">

                        <input id="file_upload" name="file_upload" type="file" />

                        <input class="btn btn-primary ma-sm" type="submit" value="<?php echo $this->trans('calculator.import.button') ?>" />

                        <input type="hidden" name="task" value="import_calculator" />
                </form>
            </center>
        </div>
    </div>
</div>

<?php $this->renderView('app/footer.php'); ?>