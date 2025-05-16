<?php if($winnerlist): ?> 
    <?php foreach ($winnerlist as $key => $winneritems): ?>
        <div class="Winnerresult">
            <img src="<?=base_url($winneritems['winner_image']);?>" alt="result-img" >
        </div> 
    <?php endforeach;?>
<?php else: ?>
    <div class="col col-12 col-xs-12">
        <h3 class="text-center"> Winner Not Found</h3>
    </div>
<?php endif;?>