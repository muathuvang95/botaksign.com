<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="nb-input-group">
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder', 'printcart' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <span class="search-button">
            <button class="bt-4" type="submit"><i class="pt-icon-search"></i></button>
        </span>
    </div>
</form>