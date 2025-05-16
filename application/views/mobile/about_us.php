<div class="container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="result_list">
                <div class="inner_section">
                    <h2 class="subheading">  About Us </h2>
                    <?php if($items): ?>
                        <?php foreach($items as $index => $ItemArray): ?>
                            <div class="about_banner">
                                <?php if($ItemArray->image): ?>
                                    <img src="<?=base_url($ItemArray->image);?>"  class="aboutus_banner" />
                                <?php endif;?>
                                <h3><?=$ItemArray->title;?></h3>
                                <p class="cancel_details py-2"><?=$ItemArray->description;?></p> 
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?> 
                </div>
            </div>
        </div>
    </div>
</div>