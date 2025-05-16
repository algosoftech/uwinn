<style>
    footer{
        position: absolute;
    bottom: 0px;
    width: 100%;
    }
    </style>
<?php if($news): ?>
    <section class="about_Sec">
        <div class="container">
            <?php if($news['title']): ?>
                <div class="row">
                    <div class="col secheading">
                        <h2><?=$news['title'];?></h2>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($news['title']): ?>
                <div class="row">
                    <div class="col desc">
                        <?=$news['description'];?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif;; ?>

 
       
        