    <i class="bi bi-search calendar-input-icon top-right-input"></i>
    <div class="search-container d-none">
        <form class="" method="post">
            <div class="container mt-2">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <input type="date" name="upload_date" id="upload_date" value="<?=$created_date;?>" class="placeholder-box8" placeholder="Select Date">
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
        <div class="row">
            <div class="col-12 px-0">
                <div class="result_list">
                    <?php if($recent_winners):  ?>
                        <?php foreach($recent_winners as $item):?>
                            <div class="Winnerresult">
                                <img src="<?=base_url($item['image']);?>" alt="result-img"  class="banner_Img">
                            </div> 
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="Winnerresult">
                            <h5 class="text-center mt-5">Winner Not Found</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>   
 