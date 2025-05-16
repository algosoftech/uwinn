<?php if($term_conditions): ?>
    <section class="about_Sec mt-0">
        <div class="container">
            <?php if($term_conditions['title']): ?>
                <div class="row">
                    <div class="col secheading">
                        <h2><?=$term_conditions['title'];?></h2>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($term_conditions['title']): ?>
                <div class="row">
                    <div class="col desc">
                        <?=$term_conditions['description'];?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif;; ?>

 
       
        