<div class="country-code-container">
    <div class="login-section-box">
        <section class="deals_homesec">
            <div class="heading-section">
                <h1>Select Country/Region</h1> 
                <p id="clountry_close">Cancel</p>
            </div>
        </section>

        <div>
            <input type="search" id="country_code_search" class="form-control btn-search" name="country_code_search" placeholder="search">
            <i class="bi bi-search search-button"></i>
        </div>

        <div class="country_code_list">
            <?php if($country_code): foreach($country_code as $countryCodeKey=>$countryCodeValue): ?>
                <?php $CountryCodeList = explode('+', $countryCodeValue); ?>
                <div class="country-code-section" data-countrycode="<?=$countryCodeKey?>">
                    <p class="country-name"><?=$CountryCodeList['0']?></p>
                    <p class="country-code">+<?=$CountryCodeList['1']?></p>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>