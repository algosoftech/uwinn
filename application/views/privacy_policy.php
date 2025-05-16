<?php if($privacy_policy): ?>
    <section class="about_Sec mt-0">
        <div class="container">
            <?php if($privacy_policy['title']): ?>
                <div class="row">
                    <div class="col secheading">
                        <h2><?=$privacy_policy['title'];?></h2>
                    </div>
                </div>
            <?php endif; ?>

             <?php if($privacy_policy['title']): ?>
                <div class="row">
                    <div class="col desc">
                        <?=$privacy_policy['description'];?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif;; ?>

 
       
        