<?php
if ( ! class_exists( 'Lib_Customize_Alpha_Color_Control' ) ) {
	/**
	 * Color picker with alpha color support for Customizer
	 */
	class Lib_Customize_Alpha_Color_Control extends WP_Customize_Control {
		public $type = 'lib_color';

		public $statuses;

		public function __construct( $manager, $id, $args = array() ) {
			parent::__construct( $manager, $id, $args );
		}

		public function enqueue() {
			wp_enqueue_script(
				'lib-alpha-color-picker',
				plugin_dir_url( __FILE__ ) . '../assets/alpha-color-picker.js',
				array( 'jquery', 'wp-color-picker' )
			);
			wp_enqueue_style( 'wp-color-picker' );
		}

		public function to_json() {
			parent::to_json();
			$this->json['statuses']     = $this->statuses;
			$this->json['defaultValue'] = $this->setting->default;
		}

		public function render_content() {
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
				<input class="color-picker-hex" data-alpha="true" type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			</label>
			<?php
		}
	}
}