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

    <?php if(count($this->view['errors']) == 0): ?>
    
        <div class="row">
            <div class="col-xs-12 text-center">
                <h2><?php echo $this->trans('calculator.import.complete.title'); ?></h2>
            </div>
        </div>
    
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-warning">
                    <strong><?php echo $this->trans('calculator.import.complete.description'); ?></strong>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12  ma-sm">

                    <!-- Calcolatori Creati -->
                    <center>
                        <h3><?php echo $this->trans('calculator.import.complete.calculators', array('count' => count($this->view['calculators']))); ?></h3>
                    </center>

                    <ul class="list-group">
                        <?php foreach($this->view['calculators'] as $calculator): ?>
                        <li class="list-group-item">

                            <?php echo $this->trans('calculator.import.complete.calculator', array('label' => $calculator['name'])) ?>
                            <a class="btn btn-primary" href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'edit','id' => $calculator['id'])); ?>">
                                <?php echo $this->trans('calculator.import.complete.calculator.edit'); ?>
                            </a>

                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Campi Creati -->
                    <center>
                        <h3><?php echo $this->trans('calculator.import.complete.new_fields', array('count' => count($this->view['newFields']))); ?></h3>
                    </center>

                    <ul class="list-group">
                        <?php foreach($this->view['newFields'] as $newField): ?>
                        <li class="list-group-item"><?php echo $this->trans('calculator.import.complete.new_field', array('label' => $newField['label'])) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Campi Mappati -->
                    <center>
                        <h3><?php echo $this->trans('calculator.import.complete.mapped_fields', array('count' => count($this->view['mappedFields']))); ?></h3>
                    </center>

                    <ul class="list-group">
                        <?php foreach($this->view['mappedFields'] as $mappedField): ?>
                        <li class="list-group-item"><?php echo $this->trans('calculator.import.complete.mapped_field', array('label' => $mappedField->label)) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Temi Creati -->
                    <center>
                        <h3><?php echo $this->trans('calculator.import.complete.new_themes', array('count' => count($this->view['themesMapping']['created']))); ?></h3>
                    </center>

                    <ul class="list-group">
                        <?php foreach($this->view['themesMapping']['created'] as $newTheme): ?>
                        <li class="list-group-item"><?php echo $this->trans('calculator.import.complete.new_theme', array('label' => $newTheme)) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Temi Mappati -->
                    <center>
                        <h3><?php echo $this->trans('calculator.import.complete.mapped_themes', array('count' => count($this->view['themesMapping']['mapped']))); ?></h3>
                    </center>

                    <ul class="list-group">
                        <?php foreach($this->view['themesMapping']['mapped'] as $mappedTheme): ?>
                        <li class="list-group-item"><?php echo $this->trans('calculator.import.complete.mapped_theme', array('label' => $mappedTheme)) ?></li>
                        <?php endforeach; ?>
                    </ul>


            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach($this->view['errors'] as $error): ?>
                            <strong> - <?php echo $error; ?></strong>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="col-xs-12 text-center ma-sm">
                <a href="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => 'import')); ?>" class="btn btn-success">
                    <i class="fa fa-upload"></i> <?php echo $this->trans('apc.calculator.import'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->renderView('app/footer.php'); ?>