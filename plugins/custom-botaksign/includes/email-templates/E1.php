<?php 
$email_button_title = "Password Reset";
$email_button_color = "#1BCB3F";
?>

<table id="header-logo" style="width:100%;padding-top:20px;border-collapse:collapse;margin-bottom:45px;">
    <tbody>
        <tr>
            <td align="left" style="width:50%;"><img class="logo" src="<?php echo CUSTOM_BOTAKSIGN_URL . '/assets/images/logo-transparent.png'; ?>" style="margin-left:0px;margin-top:0px;height: 56px; width: auto;"></td>
            <td align="right" style="width:50%;">
                <?php if($email_button_title && $email_button_color) {
                    echo '<button class="status-button" style="background: '. $email_button_color .';box-shadow: 0px 10px 20px #00000029; border: none; color: #fff; padding: 14px 40px; font-size: 22px; line-height: 28px; border-radius: 10px;">'. $email_button_title .'</button>';
                } ?>
            </td>
        </tr>
    </tbody>
</table>

<div style="margin-bottom: 25px;">
    <span class="info-title" style="display:block;font-size:17px !important; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></span>
    <span class="info-subtext" style="font-size:14px !important; line-height: 24px; color:#231f20;">A new password has been requested for the following account on Botak Sign.</span>
</div>
<div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa;color:#000000;font-weight: 400;font-size:14px; line-height: 24px;">
    <div class="stt" align="left" style="padding-bottom:5px;font-weight: 400;font-size:14px;line-height: 25px; color:#000000;">
        <div style="padding-bottom:5px;font-size:17px;font-weight: 500;">Username : <?php printf( esc_html__( 'Hi %s', 'woocommerce' ), esc_html( $user_login ) ); ?></div>
        <div>If you didn’t make this request, kindly ignore this email.</div>
        <div>If you’d like to proceed with this request, click <a class="link" style="color: #1BCB3F;" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>">HERE</a> to reset your password.</div>
    </div>
</div>