<?php

class NBT_Customize_Control_Switch extends WP_Customize_Control{
	public $type = 'switch';
	
	public function render_content(){
		?>
		<div class="customize-control-content" id="nb-<?php echo esc_attr($this->type)?>-<?php echo esc_attr($this->id)?>">
			<div class="control-switch-wrap">
				<?php if( !empty($this->label) ): ?>
				<span class="customize-control-title">
					<?php echo esc_html($this->label); ?>						
				</span>
				<?php endif; ?>			
				<div class="onoffswitch">				
					<input type="checkbox" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" class="onoffswitch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>		
					<label class="onoffswitch-label" for="<?php echo esc_attr($this->id); ?>"></label>	   
				</div>	
			</div>
			<?php if( !empty($this->description) ): ?>
			<div class="description customize-control-description">
				<?php echo esc_html($this->description); ?>            
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
?>