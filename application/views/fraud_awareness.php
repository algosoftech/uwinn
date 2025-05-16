<?php if($fraud_awareness): ?>
    <section class="about_Sec">
        <div class="container">
            <?php if($fraud_awareness['title']): ?>
                <div class="row">
                    <div class="col secheading">
                        <h2 class="mt-0"><?=$fraud_awareness['title'];?></h2>
                    </div>
                </div>
            <?php endif; ?>

             <?php if($fraud_awareness['title']): ?>
                <div class="row">
                    <div class="col desc">
                        <?=$fraud_awareness['description'];?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php endif;; ?>