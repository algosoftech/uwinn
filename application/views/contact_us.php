<div class="wrapper pt-0">
    <section class="contact_banner">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="contactbanner_txt">
                        <h3>Let's talk with us</h3>
                        <p>Questions, comments, or suggestions? Simply fill in the form and we'll be in touch shortly.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="contact_form">
                        <h4>Let's talk with us</h4>
                        <form method="POST" action="<?=base_url('contact-us-submit')?>"  autocomplete="off" >
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="first_name" id="first_name" value="<?php echo set_value('first_name'); ?>" placeholder="First Name*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('first_name')): ?>
                                    <span for="first_name" generated="true" class="help-inline"><?php echo form_error('first_name'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" value="<?php echo set_value('last_name'); ?>" placeholder="Last Name*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('last')): ?>
                                    <span for="last_name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" value="<?php echo set_value('email'); ?>" placeholder="Email*" class="form-control"  required />
                                    </div>
                                    <?php if(form_error('email')): ?>
                                    <span for="email" generated="true" class="help-inline"><?php echo form_error('email'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" name="mobile" id="mobile" value="<?php echo set_value('mobile'); ?>" placeholder="Phone Number*" class="form-control" required />
                                    </div>
                                    <?php if(form_error('mobile')): ?>
                                    <span for="mobile" generated="true" class="help-inline"><?php echo form_error('mobile'); ?></span>
                                    <?php endif; ?>
                                </div>
                                 <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" name="subject" id="subject" value="<?php echo set_value('subject'); ?>" placeholder="Subject*" class="form-control" required />
                                    </div>
                                    <?php if(form_error('subject')): ?>
                                    <span for="subject" generated="true" class="help-inline"><?php echo form_error('subject'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea rows="3" id="message" name="message"  placeholder="Your Message..."  class="form-control" required ><?php echo set_value('message'); ?></textarea>
                                    </div>
                                    <?php if(form_error('message')): ?>
                                    <span for="message" generated="true" class="help-inline"><?php echo form_error('message'); ?></span>
                                    <?php endif; ?>
                                </div>
                                 <div class="row">
                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <input type="submit" value="Send Message" class="btn btn_pink" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                        </form>
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact_info">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact_detail">
                        <h3>Company Details</h3>
                        <div>
                            <p><span><i class="bi bi-telephone-fill"></i></span> <?=strip_tags($general_info['contact_no']);?> </p>
                            <p><span><i class="bi bi-envelope-fill"></i></span>  <?=strip_tags($general_info['email_id']);?>   </p>
                            <p><span><i class="bi bi-geo-alt-fill"></i></span>   <?=strip_tags($general_info['address']);?>    </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="contact_map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d112263.12238554446!2d77.17516923951737!3d28.442706182361427!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sshiv%20nadar%20school!5e0!3m2!1sen!2sin!4v1715658158480!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>     
</div>
           
          