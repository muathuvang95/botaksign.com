<div class="middle-section-wrap">
    <div class="container">
        <div class="row middle-section">
            <div class="col-lg-2 col-md-2 col-sm-3 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>

            <div class="header-right-wrap-top col-sm-7 col-md-9">
                <?php printcart_main_nav();?>
            </div>

            <div class="header-right-cart-search col-xs-5 col-sm-2 col-md-1">
                <div class="middle-right-content">
                    <div class="search_text">
                        <?php printcart_search_section(false);?>
                        <span class="text-search"><a href="#">Search</a></span>
                    </div>
                    <div class='close_popup'></div>
                    <?php printcart_header_woo_section(); ?>
                </div>
            </div>

        </div>
    </div>
</div>