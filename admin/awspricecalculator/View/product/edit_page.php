<?php
/**
 * Created by PhpStorm.
 * User: naidi
 * Date: 31/08/18
 * Time: 10:19
 */

?>


<div id='calculator_product_data' class='panel woocommerce_options_panel hidden'>

    <h4 style='margin-left: 10px'>Selected Calculator : <span style='color: #0e90d2' id='selected_calculator'>"<?php echo $this->view['selectedSimulator']->name ?>"</span></h4>


    <?php

        woocommerce_wp_select( array(
            'id'          => 'calculator',
            'label'   => __( 'Select calculator to attach', 'woocommerce' ),
            'options'     => $this->view['selectCalculatorArray'],
        ));

    ?>

    <div>
        <button style='margin-left: 10px; display: inline-block' type='button' class='button attach_calculator button-primary'>Attach calculator</button>

        <button style='margin-left: 10px; display: <?php echo empty($this->view['selectedSimulator']->name) ? "none":"inline-block"  ?>' type='button' class='button remove_calculator button-danger'>Remove calculator</button>

    </div>

    <input id='availableCalculators' type='hidden' value='<?php echo $this->view['availableCalculators'] ?>'/>
    <input id='productId' type='hidden' value='<?php echo $this->view['productId'] ?>'/>
    <input id='ajaxUrl' type='hidden' value='<?php echo $this->view['ajaxUrl'] ?>'/>

</div>

<script src='<?php echo $this->view['resourceUrl'] ?>'></script>
