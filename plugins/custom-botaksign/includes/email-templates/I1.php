<table id="header-infor" style="border-collapse: collapse; width: 100%;" width="100%">
    <tbody><tr>
            <td style="width:100%;padding-top:30px;" class="bill-to-th" align="left"><img style="width:100%" class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/failed.jpg"></td>
        </tr>
    </tbody></table>
<div id="infor" style="margin-top: 20px; width: 100%; height: auto; margin-right: 25px; margin-left: 25px;">
    <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;"><b>Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</b></span><br>
    <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">The order you have placed is not successful as payment has failed</span>
</div>
<div class="text-content-3" style="width: 95%;padding-top: 25px;padding-left: 25px;padding-right: 25px;">
    <span class="order-link-text-none" style="color: #231f20; font-size: 15pt;">If you'd like to proceed with this order, click <a href="<?php echo wc_get_page_permalink( 'myaccount' ).'view-order/'.$order_id; ?>" class="order-link-here" style="color: #fcaf17; font-family: segoe-bold; font-size: 15pt;"> HERE</a> to make the payment again.</span><br><br>
    <span class="order-link-text" style="color: #15171b; font-family: segoe-bold; font-size: 15pt;"> Thanks for reading! </span>
</div>
<div id="thanks-div" style="width: 95%;text-align: right;padding-left: 25px;padding-right: 25px;"><span class="thank-name" style="color: #231f20; display: block; font-size: 17pt;">Cheers,</span><br><span class="thank-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Botak Sign</span></div>
<div id="line-border" style="border-bottom: 1px solid #a3cf62; width: 200pt; margin: 0px auto; margin-top: 10px; padding-top: 20px;"></div>