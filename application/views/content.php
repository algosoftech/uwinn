<?php if($content): ?>
    <section class="about_Sec mt-0">
        <div class="container">
            <?php if($content['title']): ?>
                <div class="row">
                    <div class="col secheading">
                        <h2><?=$content['title'];?></h2>
                    </div>
                </div>
            <?php endif; ?>

             <?php if($content['title']): ?>
                <div class="row">
                    <div class="col desc">
                        <?=$content['description'];?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif;; ?>

 
       
        