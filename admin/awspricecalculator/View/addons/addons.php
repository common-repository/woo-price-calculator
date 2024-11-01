<div class="wsf-bs wsf-wrap">
    
    <?php foreach($this->view['addonsRows'] as $addons): ?>
    <div class="row">
        <?php foreach($addons as $addon): ?>
            <div class="col-sm-6 col-md-4">
              <div class="thumbnail">
                    <div style="min-height: 100px" class="caption text-center">
                        <h3><?php echo $addon['name']; ?></h3>
                        <p class="mt-md mb-md"><h4><?php echo $addon['description']; ?></h4></p>
                    </div>
                  
                    <a target="_blank" href="<?php echo $addon['product_page']; ?>">
                        <img class="pl-md pr-md" style="min-height: 150px;" src="<?php echo $addon['image']; ?>" alt="">
                    </a>

                    <div class="caption text-center">
                      
                      <p></p>
                      <p>
                          <a target="_blank" href="<?php echo $addon['product_page']; ?>" class="btn btn-primary" role="button">More Info</a> 
                          <a target="_blank" href="<?php echo $addon['documentation_page']; ?>" class="btn btn-default" role="button">Documentation</a>
                          <a target="_blank" href="<?php echo $addon['demo_page']; ?>" class="btn btn-info" role="button">Demo</a>
                      </p>
                    </div>
              </div>
            </div>
        <?php endforeach; ?>  

    </div>
    <?php endforeach; ?>
</div>