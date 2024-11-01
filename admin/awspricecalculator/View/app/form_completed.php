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
    <div class="alert alert-success mt-md"<?php if(isset($this->view['database_problem'])){echo "style ='background-color:#FFD2D2; color:#D8000C'";} ?>>
        <h4><?php echo $this->view['recordName']; ?> <?php echo $this->view['verb']; ?> <?php echo $this->view['mode']." ".$this->view['database_problem']; ?>.</h4>

        <?php if(!empty($this->view['url'])): ?>
            <?php echo $this->trans('Click'); ?> <a href="<?php echo $this->view['url']; ?>"><?php echo $this->trans('here'); ?></a> <?php echo $this->trans('to go back'); ?>.
        <?php endif; ?>
    </div>
</div>

