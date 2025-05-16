<?php if($help): ?>
<section class="about_Sec">
    <div class="container">
        <?php foreach($help as $key => $items): ?>
          <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="flush-heading<?=$key?>">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?=$key?>" aria-expanded="false" aria-controls="flush-collapse<?=$key?>">
                 <div class="heading">
                    <h5><?=$items['question'];?></h5>
                 </div>
              </button>
            </h2>
            <div id="flush-collapse<?=$key?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?=$key?>" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body text-left">
                <p class="description"><?=$items['answer'];?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif;; ?>

 
       
        