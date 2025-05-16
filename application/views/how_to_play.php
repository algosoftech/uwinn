<div class="container">
    <section class="play_Sec">
        <?php if($items): ?>
            <?php foreach($items as $index  => $ItemArray): $Sno = $index+1; ?>
                <div class="section"> 
                    <div class="row <?= ($index % 2 == 0) ? 'text-image-section-left' : 'text-image-section-right'; ?> mt-4">
                        <div class="col-md-6 <?= ($index % 2 == 0) ? 'text-right' : 'text-left'; ?>">
                        <div class="flex">
                        <h2 class="heading2"><?= sprintf('%02d', $Sno); ?></h2>
                        <h2 class="identity_What"><?=$ItemArray->title;?></h2>
                        </div> 
                        
                            <div class="description"><?=$ItemArray->description;?></div>
                        </div>
                        <div class="col-md-6">
                            <img src="<?=base_url($ItemArray->image);?>" alt="" class="rectangle_43">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>  
    <?php  if($where_to_play): ?>
    <div class="section">
      <div class="container" id="how_play">
        <div class="row main_Center">
            <div class="col-md-12">
                <h2 class="where_To">Where to <span class="play_1">Play</span></h2>
                <div class="main_Color"><?=$where_to_play;?></div>
            </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
</div>