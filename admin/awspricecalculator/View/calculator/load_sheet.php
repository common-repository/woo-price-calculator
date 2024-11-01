<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div class="wsf-bs">
    <div class="row">
        <div class="col-xs-12 text-center">
            <h2><?php echo $this->trans('wpc.load_calculator'); ?></h2>
            <strong><?php echo $this->trans('Select your worksheet'); ?>:</strong>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
            <center>
                <p>
                    <form method="POST" action="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'loadermapping', 'file' => $this->view['file'], 'filename' => $this->view['filename'], 'calculator_id' => $this->view['calculator_id'])); ?>">
                        <select class="form-control wpc-sheet-list" name="worksheet" style="max-width: 400px">
                            <?php foreach ($this->view['loadedSheetNames'] as $sheetIndex => $loadedSheetName) { ?>
                                    <option value="<?php echo $sheetIndex; ?>"><?php echo $loadedSheetName; ?></option>
                            <?php } ?>
                        </select>
                        <br/><br/>



                        <a href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'loader')); ?>" class="btn btn-primary"><?php echo $this->trans('wpc.previous'); ?></a>
                        <button type="submit" class="btn btn-primary"><?php echo $this->trans('wpc.next'); ?></button>
                    </form>
                </p>
            </center>
        </div>
    </div>
</div>

<?php $this->renderView('app/footer.php'); ?>