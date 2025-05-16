<?php if($faqs): ?>
  <link rel="stylesheet" href="<?=base_url('assets/frontend/css/style-faq.css');?>">
    
    <div class="container mt-4" id="faq_container">
      <div class="row">
        <div class="col-md-12 accordion_all_section">
            <?php foreach($faqs['faq_list'] as $key => $items): ?>
          <div class="accordion" id="accordionExample<?=$key?>">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$key?>" aria-expanded="false" aria-controls="collapseSeven<?=$key?>">
                <?=$items->heading;?>
                </button>
              </h2>
              <div id="collapse<?=$key?>" class="accordion-collapse collapse" data-bs-parent="#accordionExample<?=$key?>">
                <div class="accordion-body">
                  <strong><?=$items->description;?>.</strong>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
  </div> 

<?php endif;; ?>

 
       
        