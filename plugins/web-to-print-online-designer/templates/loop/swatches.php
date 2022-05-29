<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbo-archive-swatches-wrap">
    <?php foreach( $archive_options as $key => $swatches ): ?>
        <div class="nbo-swatches-wrap <?php echo $key === 0 ? 'colors' : '' //CS botak check is color swatch (offset 0)?>">
            <?php foreach( $swatches as $swatch ): ?>
                <?php switch($swatch['different_display_type']) { 
                    case 'l': ?>
                        <div class="nbo-swatch-label-wrap nbo-swatch-hover"
                            <?php if( isset($swatch['srcset']) ): ?>
                                data-srcset="<?php echo $swatch['srcset']; ?>"
                                data-src="<?php echo $swatch['src']; ?>"
                            <?php endif; ?>
                        >
                            <span class="nbo-swatch-label" >
                                <?php echo $swatch['name']; ?>
                            </span>
                        </div>
                    <?php break;
                    case 's': ?>
                        <div class="nbo-swatch-wrap">
                            <div class="nbo-swatch-hover"
                                <?php if( isset($swatch['srcset']) ): ?>
                                    data-srcset="<?php echo $swatch['srcset']; ?>"
                                    data-src="<?php echo $swatch['src']; ?>"
                                <?php endif; ?>
                            >
                                <span class="nbo-swatch" 
                                    style="<?php if( $swatch['preview_type'] == 'i' ){echo 'background: url('.$swatch['preview'] . ') 0% 0% / cover';}else{ echo 'background: '.$swatch['color']; }; ?>" 
                                    <?php if( isset($swatch['srcset']) ): ?>
                                    data-srcset="<?php echo $swatch['srcset']; ?>"
                                    data-src="<?php echo $swatch['src']; ?>"
                                    <?php endif; ?>>
                                    <?php 
                                        if( $swatch['preview_type'] == 'c' && isset( $swatch['color2'] ) ):
                                        $style = "border-bottom-color:{$swatch['color']};border-left-color:{$swatch['color2']}";
                                    ?>
                                    <span class="nbo-swatch-bicolor" style="<?php echo $style; ?>"></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    <?php break;
                    case 'b': ?>
                        <div class="nbo-swatch-wrap">
                            <div class="nbo-swatch-container nbo-swatch-hover"
                                <?php if( isset($swatch['srcset']) ): ?>
                                    data-srcset="<?php echo $swatch['srcset']; ?>"
                                    data-src="<?php echo $swatch['src']; ?>"
                                <?php endif; ?>
                            >
                                <span class="nbo-swatch" 
                                    style="<?php if( $swatch['preview_type'] == 'i' ){echo 'background: url('.$swatch['preview'] . ') 0% 0% / cover';}else{ echo 'background: '.$swatch['color']; }; ?>" 
                                    <?php if( isset($swatch['srcset']) ): ?>
                                    data-srcset="<?php echo $swatch['srcset']; ?>"
                                    data-src="<?php echo $swatch['src']; ?>"
                                    <?php endif; ?>>
                                    <?php 
                                        if( $swatch['preview_type'] == 'c' && isset( $swatch['color2'] ) ):
                                        $style = "border-bottom-color:{$swatch['color']};border-left-color:{$swatch['color2']}";
                                    ?>
                                    <span class="nbo-swatch-bicolor" style="<?php echo $style; ?>"></span>
                                    <?php endif; ?>
                                </span>
                                <span class="nbo-swatch-tooltip">
                                    <span><?php echo $swatch['name']; ?></span>
                                </span>
                                <div class="nbo-color-title"><?php echo $swatch['name']; ?></div>
                            </div>
                        </div>
                    <?php break; 
                } ?>
            <?php endforeach; ?>  
        </div>
    <?php endforeach; ?>
</div> 
