<style type="text/css">
    .gallboxtop{
        padding: 0px !important;
    }
</style>


<div class="section_1">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="banner_First">
                        <img src="<?=base_url('assets/frontend/img/Banner (2).png')?>" alt="" class="banner_Img">
                        <div class="space_Manage12">
                            <p class="to_Know">To Know More Information <br> And How to Play in</p>
                            <h3 class="uwinn">Uwinn?</h3>
                            <p class="check_Our">Check Our YouTube Videos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <section class="draresult">
                <div class="container">
                    <div class="row">
                        <div class="col secheading">
                            <h2>Winner Gallery</h2>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="draw_form">
                            <div class="form-group">
                                <label>Game Name</label>
                                <select class="form-control">
                                    <option>Select Option</option>
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Winner Date</label>
                                <input type="date" name="winningDate" class="form-control winningDate" value="<?=$winnerDate?>"  />
                            </div>
                             <div class="form-group">
                                <a href="<?=base_url('winner-gallery')?>"  class="form-control winningDate text-center" >Reset</a>
                            </div>
                        </div>
                    </div> -->
                </div>
            </section>

            <section class="gallery-sec">
                <div class="container">
                    <div class="row">

                       <?php if($winnerlist): ?> 
                        <?php foreach ($winnerlist as $key => $winneritems): ?>
                            <div class="col col-4 col-xs-12">
                                <div class="gallbox">
                                    <div class="gallboxtop">
                                        <div class="gall_social">
                                            <a href="<?=$general_info['insta_link'];?>" target="_blank"><i class="bi bi-instagram"></i></a>
                                            <a href="<?=$general_info['facebook_link'];?>" target="_blank"><i class="bi bi-facebook"></i></a>
                                            <a href="<?=$general_info['you_tube'];?>" target="_blank"><i class="bi bi-youtube"></i></a>
                                        </div>
                                        <div class="lealslogo">
                                            <!-- <img src="<?=base_url($general_info['logo']);?>" alt="Logo" /> -->
                                        </div>
                                    </div>
                                    <img src="<?=base_url($winneritems['winner_image']);?>"  style="width:100%" />
                                    
                                </div>
                            </div>
                        <?php endforeach;?>
                    <?php else: ?>
                        <div class="col col-12 col-xs-12">
                            <h3 class="text-center"> Winner Not Found</h3>
                        </div>

                    <?php endif;?>

                    </div>
                    <!-- <div class="row">
                        <div class="loadmorebtn">
                            <button class="loadmore">Load More</button>
                        </div>
                    </div> -->
                </div>
            </section>

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script>
        $('.winningDate').on('change', function(){
          var winningDate = $(this).val();
          window.location.href = "<?=base_url('winner-gallery?winningDate=')?>"+winningDate;
        });
        </script>