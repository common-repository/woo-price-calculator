<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*AWS_PHP_HEADER*/
?>

<?php
    //Controllo della versione PHP
    if(version_compare(phpversion(), '5.0.0', '<=')) {
        die("<div style=\"padding-top:30px\"><b><center>Error: Compatible PHP versions are: >= 5.0.0</center></b></div>");
    }
?>

<div class="wsf-bs wsf-wrap">
    <div class="row">
        <div class="col-xs-12 col">
            <div>
                <a style="" class="pull-left" target="_blank" href="<?php echo $this->view['homeUrl']; ?>">
                    <img style="max-width: 150px;" src="<?php echo $this->view['icon']; ?>">
                </a>
    
                <div class="pull-right">
                    <a target="_blank" href="<?php echo $this->view['documentationUrl']; ?>" class="btn btn-default">
                        <i class="fa fa-book"></i> <?php echo $this->trans('wpc.documentation'); ?>
                    </a>
                    <a target="_blank" href="<?php echo $this->view['forumUrl']; ?>" class="btn btn-default">
                        <i class="fa fa-question"></i> <?php echo $this->trans('wpc.forum'); ?>
                    </a>
                </div>
            </div>
        </div>


        <!--Checking if the required folder are created or not, if not display an error message and the missing folder_name-->

        <div class="col-xs-12 col">

            <?php if (!empty($this->view['directories'])){?>

                <div class="alert alert-danger">

                    <strong>

                        <?php

                        for ($i = 0; $i < count($this->view['directories']); $i++) {

                            echo $this->trans('wpc.required_directories', array(
                                'required_directories' => $this->view['directories'][$i],
                            ));

                            echo "<br/>";

                        }

                        ?>

                    </strong>

                </div>

            <?php }?>

        </div>






        <div class="col-xs-12 col">


            <!--checking if the required php_extensions are installed , otherwise show an error -->
            <?php  if(!empty($this->view['extensions'])){?>

                <div class="mr-md">

                    <p style="margin-top: 15px; padding: 15px; background-color: #FFD2D2; box-shadow: 0 6px 10px #A9A9A9;">
                        <?php echo $this->trans('wpc.required_extensions', array(
                            'php_extension'   =>  $this->view['extensions'],
                        )); ?>
                    </p>


                </div>

            <?php }?>


<div class="mr-md">
<?php if($this->getLicense() == 0): ?>
    <p style="margin-top: 15px; padding: 15px; background-color: #fff; box-shadow: 0 6px 10px #A9A9A9;">
        <?php echo $this->trans('wpc.go_pro', array(
            'homeUrl'   => $this->view['homeUrl']
        )); ?>
    </p>

<?php else: ?>
    <p style="margin-top: 15px; padding: 15px; background-color: #fff; box-shadow: 0 6px 10px #A9A9A9;">
        <?php echo $this->trans('wpc.header.pro'); ?>
    </p>
<?php endif; ?>
</div>
</div>
    </div>

    <br/><br/>

    <ul class="nav nav-tabs">
        <li class="<?php echo ($this->view['controller'] == "field" || empty($this->view['controller']))?"active":""; ?>">
            <a href="<?php echo $this->adminUrl(array('controller' => 'field')); ?>"><?php echo $this->trans('aws.fields'); ?></a>
        </li>
        
        <li class="<?php echo (in_array($this->view['controller'], array("calculator", "productImageLogic")))?"active":""; ?>">
            <a href="<?php echo $this->adminUrl(array('controller' => 'calculator')); ?>"><?php echo $this->trans('Calculator'); ?></a>
        </li>
        
        <li class="<?php echo ($this->view['controller'] == "regex")?"active":""; ?>">
            <a href="<?php echo ($this->getLicense() != 0)?$this->adminUrl(array('controller' => 'regex')):"#regex_upgrade"; ?>"><?php echo $this->trans('wpc.regex'); ?></a>
        </li>
        
        <li class="<?php echo ($this->view['controller'] == "settings")?"active":""; ?>">
            <a href="<?php echo $this->adminUrl(array('controller' => 'settings')); ?>"><?php echo $this->trans('wpc.settings'); ?></a>
        </li>
		
        <li class="<?php echo ($this->view['controller'] == "addons")?"active":""; ?>">
            <a href="<?php echo $this->adminUrl(array('controller' => 'addons')); ?>">
                <i class="fa fa-plug"></i> <?php echo $this->trans('wpc.addons'); ?>
            </a>
        </li>
		
    </ul>
    
</div>
