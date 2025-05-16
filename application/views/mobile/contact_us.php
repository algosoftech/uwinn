    <div class="mobile_warpper">
        <div class="container">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="result_list">
                        <div class="inner_section">
                            <form method="POST" action="<?=base_url('contact-us-submit')?>"  autocomplete="off" >
                                <div class="contact_form">
                                    <div class="form-contact">
                                        <input type="text" name="first_name" id="first_name" value="<?=set_value('first_name'); ?>" placeholder="Enter Fisrt Name" required />
                                        <?php if(form_error('first_name')): ?>
                                            <span for="first_name" generated="true" class="help-inline"><?php echo form_error('first_name'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-contact">
                                        <input type="text" name="last_name" id="last_name" value="<?=set_value('last_name');?>" placeholder="Enter Last Name" required />
                                        <?php if(form_error('last_name')): ?>
                                            <span for="last_name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-contact">
                                        <input type="text" name="mobile" id="mobile" value="<?=set_value('mobile'); ?>" placeholder="Enter Mobile Number" required />
                                        <?php if(form_error('mobile')): ?>
                                            <span for="mobile" generated="true" class="help-inline"><?php echo form_error('mobile'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-contact">
                                        <input type="email" name="email" id="email" value="<?=set_value('email'); ?>" placeholder="Enter Email Id" required />
                                        <?php if(form_error('email')): ?>
                                            <span for="email" generated="true" class="help-inline"><?php echo form_error('email'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-contact">
                                        <input type="text" name="subject" id="subject" value="<?=set_value('subject');?>" placeholder="Enter Subject" required />
                                        <?php if(form_error('subject')): ?>
                                            <span for="subject" generated="true" class="help-inline"><?php echo form_error('subject'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-contact">
                                        <textarea  id="message" name="message" required placeholder="Enter Message"><?php echo set_value('message'); ?> </textarea>
                                        <?php if(form_error('message')): ?>
                                            <span for="message" generated="true" class="help-inline"><?php echo form_error('message'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" value="Submit" class="contact_btn">Submit</button>
                                    </div>
                                </div>


                            </form>
                            <div class="contact_usdetails">
                                <h2 class="subheading">Company Details</h2>
                                <ul>
                                    <li><a href="#"><i class="bi bi-geo-alt"></i> U WINN L.L.C</a></li>
                                    <li><a href="mailto:info@u-winn.com"><i class="bi bi-envelope"></i>
                                            info@u-winn.com</a></li>
                                    <li><a href="tel:+971554691351"><i class="bi bi-telephone"></i> 971554691351</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    </div>
    <!--More menu List End-->
    