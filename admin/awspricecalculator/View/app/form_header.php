<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/
?>

<?php if(count($this->view['errors']) != 0): ?>
<div class="alert alert-danger">
    <h4><?php echo $this->trans('errors'); ?></h4>
    
    <ul>
        <?php foreach($this->view['errors'] as $error): ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if(count($this->view['warnings']) != 0): ?>
<div class="alert alert-warning">
    <h4><?php echo $this->trans('wpc.warnings'); ?></h4>
    
    <ul>
        <?php foreach($this->view['warnings'] as $warning): ?>
            <li><?php echo $warning; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
