</div>

<?php
    //General Info ..
    $tblName              = 'uw_general_data';
    $whereCon['where']    = array();    
    $shortField           = array('testi_name'=>'ASC');
    $general_info         = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
    if($general_info):
        $general_info['alt_text']         = $general_info['alt_text'];
        $general_info['email_id']         = $general_info['email_id'];
        $general_info['contact_no']       = $general_info['contact_no'];
        $general_info['address']          = $general_info['address'];
        $general_info['facebook_link']    = $general_info['facebook_link'];
        $general_info['linkedin_link']    = $general_info['linkedin_link'];
        $general_info['twitter_link']     = $general_info['twitter_link'];
        $general_info['insta_link']       = $general_info['insta_link'];
        $general_info['you_tube']         = $general_info['you_tube'];
        $general_info['whatsapp_no']      = $general_info['whatsapp_no'];
        $general_info['logo']             = $general_info['logo'];
    endif;

?>

   <footer id="top_Space">
     <div class="container">
         <div class="row align-items-center justify-content-between">
             <div class="col-md-2">
                 <div class="foot_logo">
                     <a href="">
                         <img src="<?=base_url('assets/frontend/img/footlogo.png');?>" />
                     </a>
                 </div>
             </div>
             <div class="col-md-6">
                 <div class="foote_menu">
                     <a href="<?=base_url('about-us');?>">About</a>
                     <a href="<?=base_url('join-our-team');?>">Join Our Team</a>
                     <a href="<?=base_url('news');?>">News</a>
                     <a href="<?=base_url('contact-us');?>">Contact Us</a>
                 </div>
             </div>
             <div class="col-md-4">
                <ul class="download_apps">
                    <li><a href="#">  <img src="<?=base_url('assets/frontend/img/google_btns.png');?>" /></a></li>
                    <li><a href="#">  <img src="<?=base_url('assets/frontend/img/app_store.png');?>" /></a></li>
                    <li><a href="#">  <img src="<?=base_url('assets/frontend/img/Badge-Black.jpg');?>" /></a></li>
                </ul>
             </div>
           
         </div>
     </div>
     <div class="foot_bottom">
         <div class="container">
            <div class="row">
                <div class="col-md-10">
                <div class="foot_page">
                 <a href="<?=base_url('terms&conditions');?>">Terms and Conditions</a>
                 <a href="<?=base_url('fraud-awareness');?>">Fraud Awareness</a>
                 <a href="<?=base_url('faqs');?>">FAQs</a>
                 <a href="<?=base_url('privacy-policy');?>">Privacy Policy</a>
                 <a href="<?=base_url('help');?>">Help</a>
             </div>
                </div>
                <div class="col-md-2">
                <?php if(!empty($general_info['facebook_link']) || !empty($general_info['insta_link']) || !empty($general_info['you_tube']) ): ?>
             
                 <div class="footsocial">
                    <?php if(!empty($general_info['facebook_link'])): ?>
                        <a href="<?=$general_info['facebook_link'];?>" target="_blank" >
                             <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path d="M20 12.05C19.9813 10.5255 19.5273 9.03809 18.6915 7.76295C17.8557 6.48781 16.673 5.47804 15.2826 4.85257C13.8921 4.2271 12.3519 4.01198 10.8433 4.23253C9.33473 4.45309 7.92057 5.10013 6.7674 6.09748C5.61422 7.09482 4.77005 8.40092 4.3343 9.86195C3.89856 11.323 3.88938 12.8781 4.30786 14.3442C4.72634 15.8103 5.55504 17.1262 6.69637 18.1371C7.83769 19.148 9.24412 19.8117 10.75 20.05V14.38H8.75001V12.05H10.75V10.28C10.7037 9.86846 10.7483 9.45175 10.8807 9.05931C11.0131 8.66687 11.23 8.30827 11.5161 8.00882C11.8022 7.70936 12.1505 7.47635 12.5365 7.32624C12.9225 7.17612 13.3368 7.11255 13.75 7.14003C14.3498 7.14824 14.9482 7.20173 15.54 7.30003V9.30003H14.54C14.3676 9.27828 14.1924 9.29556 14.0276 9.35059C13.8627 9.40562 13.7123 9.49699 13.5875 9.61795C13.4627 9.73891 13.3667 9.88637 13.3066 10.0494C13.2464 10.2125 13.2237 10.387 13.24 10.56V12.07H15.46L15.1 14.4H13.25V20C15.1399 19.7011 16.8601 18.7347 18.0985 17.2761C19.3369 15.8175 20.0115 13.9634 20 12.05Z" fill="#fff"/>
                             </svg>
                        </a>
                    <?php endif;?>
                    <?php if(!empty($general_info['insta_link'])): ?>
                        <a href="<?=$general_info['insta_link'];?>" target="_blank" >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 11C3 7.22876 3 5.34315 4.17157 4.17157C5.34315 3 7.22876 3 11 3H13C16.7712 3 18.6569 3 19.8284 4.17157C21 5.34315 21 7.22876 21 11V13C21 16.7712 21 18.6569 19.8284 19.8284C18.6569 21 16.7712 21 13 21H11C7.22876 21 5.34315 21 4.17157 19.8284C3 18.6569 3 16.7712 3 13V11Z" stroke="#fff" stroke-width="2"/>
                            <circle cx="16.5" cy="7.5" r="1.5" fill="#fff"/>
                            <circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="2"/>
                            </svg>
                        </a>
                    <?php endif;?>
                    <?php if(!empty($general_info['you_tube'])): ?>
                        <a href="<?=$general_info['you_tube'];?>"  target="_blank" >
                            <svg viewBox="0 -3 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                 <title>youtube</title>
                                 <desc>Created with Sketch.</desc>
                                 <defs></defs>
                                 <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                     <g id="Dribbble-Light-Preview" transform="translate(-300.000000, -7442.000000)" fill="#fff">
                                         <g id="icons" transform="translate(56.000000, 160.000000)">
                                             <path d="M251.988432,7291.58588 L251.988432,7285.97425 C253.980638,7286.91168 255.523602,7287.8172 257.348463,7288.79353 C255.843351,7289.62824 253.980638,7290.56468 251.988432,7291.58588 M263.090998,7283.18289 C262.747343,7282.73013 262.161634,7282.37809 261.538073,7282.26141 C259.705243,7281.91336 248.270974,7281.91237 246.439141,7282.26141 C245.939097,7282.35515 245.493839,7282.58153 245.111335,7282.93357 C243.49964,7284.42947 244.004664,7292.45151 244.393145,7293.75096 C244.556505,7294.31342 244.767679,7294.71931 245.033639,7294.98558 C245.376298,7295.33761 245.845463,7295.57995 246.384355,7295.68865 C247.893451,7296.0008 255.668037,7296.17532 261.506198,7295.73552 C262.044094,7295.64178 262.520231,7295.39147 262.895762,7295.02447 C264.385932,7293.53455 264.28433,7285.06174 263.090998,7283.18289" id="youtube-[#168]">
                                           </path>
                                         </g>
                                     </g>
                                 </g>
                            </svg>
                        </a>
                    <?php endif;?>
                 </div>
            
            <?php endif; ?>
                </div>
            </div>
           
         </div>
     </div>
   </footer>

