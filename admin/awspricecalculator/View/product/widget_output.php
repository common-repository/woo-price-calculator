<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<div 
    class="awspc-output-result-row <?php echo $this->view['outputResult']['fieldName']; ?>" 
    style="<?php echo (count($this->view['errors']) != 0)?"display:none":"" ?>"
    >
    <span class="awspc-output-result-label"><b><?php echo $this->view['outputResult']['field']->label; ?></b>: </span>
    <span class="awspc-output-result-value"><?php echo $this->view['outputResult']['value']; ?></span>

</div>