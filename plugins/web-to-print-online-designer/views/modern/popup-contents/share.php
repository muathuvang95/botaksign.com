<div class="nbd-popup popup-share" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="overlay-main active">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50" >
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <h2><?php esc_html_e('Share this design','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="share-with">
                <span><?php esc_html_e('Share with','web-to-print-online-designer'); ?>:</span>
                <ul class="socials">
                    <li ng-click="createShareLink('facebook', 'https://facebook.com/sharer/sharer.php?u=')" class="social facebook"><i class="icon-nbd icon-nbd-facebook-circle nbd-hover-shadow"></i></li>
                    <li ng-click="createShareLink('twitter', 'https://twitter.com/share?url=')" class="social twitter"><i class="icon-nbd icon-nbd-twitter-circle nbd-hover-shadow"></i></li>
                </ul>
            </div>
            <div class="share-content">
                <textarea ng-change="updateShareLink()" placeholder="<?php esc_html_e('Write a comment'); ?>" ng-model="resource.social.comment"></textarea>
            </div>
            <div class="share-btn">
                <a href="{{resource.social.link}}" target="_blank" ng-class="resource.social.link != '' ? '' : 'nbd-disabled'" class="nbd-button nbd-hover-shadow"><?php esc_html_e('Share now','web-to-print-online-designer'); ?></a>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>