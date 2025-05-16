    <i class="bi bi-search calendar-input-icon top-right-input"></i>
    <div class="search-container d-none">
        <form class="" method="post">
            <div class="container mt-2">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <input type="date" name="date" id="date" value="<?=$this->input->post('date')?>" class="placeholder-box26" placeholder="Select Date ">
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <select name="game_type" id="game_type" class="placeholder-box26">
                            <option value="All" <?=$this->input->get('game_type') == 'All'?'selected':'';?> >All</option>
                            <?php foreach ($gameType as $key => $value) { ?>
                            <option <?=$this->input->post('game_type') == $value?'selected':'';?> value="<?=$value?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="text-center">
                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                            <button type="submit" value="Submit" class="contact_btn">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

     <div class="inner_section">
        <?php if($result): ?>
            <?php foreach ($result as $key => $value) : ?>
                <div class="row">
                    <div class="col-12 px-0">
                        <div class="result_list">
                            <div class="Winnerresult">
                                <a href="javaScript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="https://www.youtube.com/embed/3twG8aTgRYA?si=WXPq13_cMVuExP6T"> 
                                    <img src="<?=base_url().$value['link_thumbnail']?>"> 
                                </a>
                                <h2 class="subheading"><?=$value['game_type']?>  </h2>
                            </div> 
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="data_found text-center">Data No Found</p>
        <?php endif; ?>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" >
            <h5 class="modal-title" id="exampleModalLabel"> </h5>
            <button class="btn-close_model" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
            <iframe id="you-tube-video-frame" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var exampleModal = document.getElementById('exampleModal');
    exampleModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var recipient = button.getAttribute('data-bs-whatever');
        document.getElementById('you-tube-video-frame').src = recipient;
    });
    exampleModal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('you-tube-video-frame').src = '';
    });
});
</script>

<script>
    document.getElementById('date').addEventListener('change', function() {
        document.getElementById('live-winnwr-gallery').submit();
    });

    document.getElementById('game_type').addEventListener('change', function() {
        document.getElementById('live-winnwr-gallery').submit();
    });
</script>

 
