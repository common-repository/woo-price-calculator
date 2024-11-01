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
        <div class="panel panel-default">
            <div class="panel-heading text-center">
                <h4>
                    <i class="fa fa-archive"></i> <?php echo $this->view['title'] . ' ' . $this->trans('wpc.regex'); ?>
                </h4>
            </div>
            <div class="panel-body">
                <?php if(count($this->view['errors']) != 0): ?>
                <div class="alert alert-danger">
                    <?php echo implode("<br/>", $this->view['errors']); ?> 
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-2">
                        <form class="form-horizontal wpc-form" action="<?php echo $this->adminUrl(array('controller' => 'regex', 'action' => 'form')); ?>" method="POST">

                           <div class="form-group">
                               <label class="control-label col-sm-4 required" for="name">
                                   <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.regex.form.name.tooltip'))); ?> <?php echo $this->trans('wpc.regex.form.name'); ?>
                               </label>
                               <div class="col-sm-8">
                                   <input required="required" class="form-control" name="name" type="text" value="<?php echo htmlspecialchars($this->view['form']['name']); ?>" />
                               </div>
                           </div>

                           <div class="form-group">
                               <label class="control-label col-sm-4" for="regex">
                                   <?php $this->renderView('partial/help.php', array('text' => $this->trans('wpc.regex.form.regex.tooltip'))); ?> <?php echo $this->trans('wpc.regex.form.regex'); ?>
                               </label>
                               <div class="col-sm-8">
                                   <textarea class="form-control" style="height: 100px" name="regex"><?php echo htmlspecialchars($this->view['form']['regex']); ?></textarea>
                               </div>
                           </div>


                           <div class="form-group">
                               <div class="col-sm-10 col-sm-offset-3 text-center">
                                   <button class="btn btn-primary" type="submit">
                                       <i class="fa fa-floppy-o"></i> <?php echo $this->trans('wpc.save'); ?>
                                   </button>
                               </div>
                           </div>

                           <input type="hidden" name="id" value="<?php echo $this->view['id']; ?>" />
                           <input type="hidden" name="task" value="form" />

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->renderView('app/footer.php'); ?>