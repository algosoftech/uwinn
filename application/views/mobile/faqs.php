<div class="inner_section">
  <div class="container">
      <div class="row">
          <div class="col-12 px-0">
              <div class="result_list">
                     <?php if($faqs): ?>
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
                              <p><?=$items->description;?>.</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach;?>
                    
                    <?php endif; ?>
              </div>
          </div>
      </div>
  </div>
</div>
