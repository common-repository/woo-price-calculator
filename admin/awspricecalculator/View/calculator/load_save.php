<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<h2><?php echo $this->view['title'] . ' ' . $this->trans('Calculator'); ?></h2>

<?php if(count($this->view['errors'] != 0)){ ?>
<div style="color: #FF0000">
    <?php echo implode("<br/>", $this->view['errors']); ?> 
</div>
<?php } ?>

<div class="postbox">
    <h3><span><?php echo $this->trans('Calculator Information'); ?></span></h3>

        <form action="<?php echo $this->adminUrl(array('controller' => 'calculator', 'action' => $this->view['action'])); ?>" method="POST">
            <div class="inside">
                <table>
                    <tr>
                        <td>
                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Just for remember'))); ?>
                            <b><font color="#FF0000">*</font> 
                                <?php echo $this->trans('Name'); ?></b>
                        </td>
                        <td>
                            <input style="width: 300px" name="name" type="text" value="<?php echo $this->view['form']['name']; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('Just for remember'))); ?>
                            <b><?php echo $this->trans('Description'); ?></b>
                        </td>
                        <td>
                            <textarea style="width: 300px;height: 100px" name="description"><?php echo $this->view['form']['description']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php $this->renderView('partial/help.php', 
                                        array(
                                            'text' => $this->trans('To which products do you want to enable the calculator?') . "<br/>" .
                                                      '<b>' . $this->trans("Note: You can't use different calculators for the same product") . "</b><br/>"
                                        )
                                    ); ?>
                            <b><?php echo $this->trans('Products'); ?></b>
                        </td>
                        <td>
                            <select style="min-width: 300px;" name="products[]" multiple="multiple">
                                <?php foreach($this->view['products'] as $product){ ?>
                                    <option value="<?php echo $product->get_id(); ?>" <?php if(in_array($product->get_id(), $this->view['form']['products'])){echo 'selected="selected"';} ?>>
                                        <?php echo $product->get_title(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php $this->renderView('partial/help.php', array('text' => $this->trans('If you want to redirect the user directly to the checkout after user added a product to cart, set Yes'))); ?>
                            <b><?php echo $this->trans('Redirect to checkout on Add to Cart'); ?></b>
                        </td>
                        <td>
                            <select style="width: 300px;" name="redirect">
                                <option value="0" <?php if(empty($this->view['form']['redirect'])){echo 'selected="selected"';} ?>><?php echo $this->trans('No'); ?></option>
                                <option value="1" <?php if($this->view['form']['redirect'] == 1){echo 'selected="selected"';} ?>><?php echo $this->trans('Yes'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <input type="submit" class="btn btn-primary" value="<?php echo $this->trans('Save'); ?>" />
                <input type="hidden" name="id" value="<?php echo $this->requestValue('id'); ?>" />
                <input type="hidden" name="type" value="excel" />

                <?php foreach($this->view['loader_fields'] as $loader_field_key => $loader_field_value){ ?>
                        <input type="hidden" name="<?php echo $loader_field_key; ?>" value="<?php echo $loader_field_value; ?>" />
                <?php } ?>
            </div>
        </form>
</div>

<?php $this->renderView('app/footer.php'); ?>