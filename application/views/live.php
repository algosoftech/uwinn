<div class="section_1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner_First">
                <img src="<?=base_url('assets/frontend/img/Banner (2).png');?>" alt="" class="banner_Img">
                    <div class="space_Manage">
                        <p class="to_Know">To Know More Information <br> And How to Play in</p>
                        <h3 class="uwinn">Uwinn?</h3>
                        <p class="check_Our">Check Our YouTube Videos</p>
                        <div class="check_out_btn">
                            <a href="<?=base_url('draw-results')?>" class="view_Results">View Results</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gallerysection">

    <div class="container">
        <form action="" id="live-winnwr-gallery" method="POST" class="contact-form">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="winner_Gallery"><span class="winner_1"> Winner</span> Gallery</h1>
                </div>
                <div class="col-md-4">
                    <h2 class="sub_heading5 mb-2">Select Draw Date</h2>
                    <div class="inner-form-content">
                        <input type="date" name="date" id="date" value="<?=$this->input->post('date')?>" class="placeholder-box26" placeholder="Select Date ">
                    </div>
                </div>
              
                <div class="col-md-4">
                    <h2 class="sub_heading mb-2">Select Game Type <?=$this->input->get('game_type')?></h2>
                    <div class="w3-bar w3-black">
                        <select name="game_type" id="game_type" class="placeholder-box26">
                            <option value="All" <?=$this->input->get('game_type') == 'All'?'selected':'';?> >All</option>
                            <?php foreach ($gameType as $key => $value) { ?>
                            <option <?=$this->input->post('game_type') == $value?'selected':'';?> value="<?=$value?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                    </div>
                    
                </div>
            </div>
        </form>

    <div class="tabs_6">
        <div id="All" class="w3-container city">
            <div class="winner_Line1">
                <hr>
            </div>
            <?php if($result): ?>
            <div class="row winner_Space">
                <?php foreach ($result as $key => $value) : ?>
                <div class="col-md-4">
                    <div class="winner_box">
                        <a href="javaScript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            data-bs-whatever="https://www.youtube.com/embed/3twG8aTgRYA?si=WXPq13_cMVuExP6T"> <img
                                src="<?=base_url().$value['link_thumbnail']?>"> </a>
                        <p><?=date('d-M-Y',strtotime($value['created_date']))?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
                <h1 class="data_found">Data No Found</h1>
            <!-- <div class="col-md-12" style="text-align: center;">
                <img src="<?=base_url()?>./assets/img/no-data-found.png" width="300px"/><br/>
                <div class="spinner-grow text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-secondary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-dark" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div> -->
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" >
            <h5 class="modal-title" id="exampleModalLabel"> </h5>
            <button class="btn-close_model" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
            <!-- <button type="button"  data-bs-dismiss="modal" aria-label="Close"></button> -->
            <iframe id="you-tube-video-frame" width="660" height="415" title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
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