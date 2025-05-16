<div class="section_1 draw_result">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner_First">
                    <img src="<?=base_url('assets/frontend/img/Banner (2).png');?>" alt="" class="banner_Img">
                    <div class="space_Manage">
                        <p class="to_Know">To Know More Information <br> And How to Play in</p>
                        <h3 class="uwinn">Uwinn?</h3>
                        <p class="check_Our">Check Our YouTube Videos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
         
<div class="gallerysection mt-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="winner_Gallery"><span class="winner_1">Draw</span> Result</h1>
            </div>
            <div class="col-md-4">
                <p class="select_Draw">Select Draw Date</p>
                <form action="<?=base_url('draw-results')?>" method="POST" class="my-order">
                   <div class="inner-form-content">
                     <input type="date" name="upload_date" id="upload_date" value="<?=$upload_date;?>" class="placeholder-box8" placeholder="Select Date">
                   </div>
                </form>
            </div>
             
        </div>

        <div class="winner_Line1 mt-4 mb-4">
            <hr>
        </div> 

        <div class="tabs_6 mt-0 mb-4">
            <div class="w3-container " >
                <div class="inner-content-card">
                    <?php if($recent_winners):  ?>
                        <div class="row">
                            <?php foreach($recent_winners as $item):?>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <img src="<?=base_url($item->image);?>" alt="" class="banner_Img">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
        $(document).on('change', '.showRow', function() {
            // let row = $(this).data('section');  
            let row = $(this).val();  
            $('.tickets_Space').addClass('d-none');
            if(row != 'All'){
                $('.'+row).removeClass('d-none');
            }else if(row == 'All'){
             $('.tickets_Space').removeClass('d-none');
            }
        });
    });

    $(document).on('change','#upload_date' , function(){
        $('.my-order').submit();
    });

</script>






