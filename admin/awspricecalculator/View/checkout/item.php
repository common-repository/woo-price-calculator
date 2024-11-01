<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<?php echo $this->view['productTitle']; ?>

<?php if(count($this->view['productItems']) != 0): ?>
    <dl class="variation awspc-variation awspc-variation-checkout">
        <?php foreach($this->view['productItems'] as $productItem): ?>

                <dt class="awspc-variation-label variation-<?php echo $productItem['fieldId']; ?>">
                    <?php echo $productItem['label']; ?>:
                </dt>
                <dd class="awspc-variation-value variation-<?php echo $productItem['fieldId']; ?>">
                    <p>
                        <?php echo $productItem['html']; ?>
                    </p>
                </dd>
        <?php endforeach; ?>
    </dl>
<?php endif; ?>
