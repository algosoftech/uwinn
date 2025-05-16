<section class="about_Sec">
    <div class="container">
        <?php if($items): ?>
            <?php foreach($items as $index => $ItemArray): ?>
                <div class="row mb-4 justify-content-between align-items-center  <?= ($index % 2 == 0) ? 'text-image-section-left' : 'text-image-section-right'; ?>"  ">
                    <div class="col">
                        <?php if($ItemArray->title): ?>
                            <div class="col secheading"> <h2><?=$ItemArray->title;?></h2> </div>
                        <?php endif;?>
                        <?php if($ItemArray->description): ?>
                            <div class="abo_text"><?=$ItemArray->description;?></div>
                        <?php endif;?>
                    </div>
                    <div class="col">
                        <div class="abo_img">
                            <img src="<?=base_url($ItemArray->image);?>" />
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>   
        