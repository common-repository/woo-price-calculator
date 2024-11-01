<div class="awspc-upgrade-modal remodal" data-remodal-id="<?php echo $this->view['id']; ?>">
    <button data-remodal-action="close" class="remodal-close"></button>
    
    <h2><?php echo $this->trans($this->view['title']); ?> <?php echo $this->trans('aws.upgrade_modal.text'); ?></h2>

    <br/>
    
    <video style="width: 100%; min-height: 85%" width="100%" height="85%" controls preload="none">
     <source src="<?php echo "https://altoswebsolutions.com/video/upgrade/{$this->view['id']}/{$this->view['id']}.mp4"; ?>" type="video/mp4">
     <source src="<?php echo "https://altoswebsolutions.com/video/upgrade/{$this->view['id']}/{$this->view['id']}.ogg"; ?>" type="video/ogg">
        Your browser does not support the video tag.
    </video> 
    
   <br/>

    
    <button data-remodal-action="cancel" class="remodal-cancel">
        <?php echo $this->trans('aws.upgrade_modal.cancel'); ?>
    </button>
   
    <a target="_blank" href="https://altoswebsolutions.com/cms-plugins/woopricecalculator" class="remodal-confirm">
        <?php echo $this->trans('aws.upgrade_modal.ok'); ?>
    </a>
</div>