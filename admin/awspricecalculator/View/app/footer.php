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
            <i>Copyright <?php echo date('Y'); ?> <a target="_blank" href="http://www.altosmail.com?wt_source=woo-price-calculator">Altos Web Solutions Inc</a>. All Rights Reserved.</i>
        </div>
        <div class="col-xs-12 text-center mt-3">
            <small>
                <i>Developed by <a target="_blank" href="<?php echo $this->view['credits']; ?>">AltosMail Team (www.altosmail.com)</a></i>
            </small>
        </div>
        
        <div class="col-xs-12 text-center mt-3">
            <a target="_blank" href="<?php echo $this->view['credits']; ?>">
                <img style="width: 100px" src="<?php echo $this->view['logo']; ?>" />
            </a>
        </div>
    </div>
    
    <?php $this->renderView('app/upgrade_modal.php', array(
        'id'      => 'regex_upgrade',
        'title'   => 'wpc.regex',
    )); ?>
    
</div>