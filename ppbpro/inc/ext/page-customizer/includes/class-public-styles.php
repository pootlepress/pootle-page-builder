<?php
class Pootle_Page_Customizer_Public {

	/** @var string Token */
	public $token;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function
	__construct( $token ) {
		$this->token = $token;
		add_action( 'wp_enqueue_scripts', array( $this, 'public_scripts' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Gets value of post meta
	 * @global WP_Post $post
	 *
	 * @param string	$section
	 * @param string	$id
	 * @param mixed		$default
	 * @param int|bool	$post_id
	 *
	 * @return string
	 */
	protected function get_value( $section, $id, $default = null, $post_id = false ) {
		//Getting post id if not set
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		$post_meta = get_post_meta( $post_id, $this->token, true );
		if ( ! empty( $post_meta[ $id ] ) ) {
			$return = $post_meta[ $id ];
		} else {
			$return = $default;
		}

		return apply_filters( "post_meta_customize_setting_$this->token[$id]", $return, $id );
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 */
	public function public_scripts() {

		if ( ! is_single() && ! is_page() ) { return false; }
		wp_enqueue_style( 'ppc-styles', plugins_url( '/../assets/css/style.css', __FILE__ ) );
		wp_enqueue_script( 'page-custo-script', plugins_url( '/../assets/js/public.js', __FILE__ ) );

		//Init $css
		$css = '/* Storefront Page Customizer */';

		$css .= $this->bg_styles();

		$css .= $this->header_styles();

		$css .= $this->content_styles();

		$css .= $this->footer_styles();

		$css .= $this->ux_styles();

		$css .= '@media only screen and (max-width:768px) {';
		$css .= $this->mobile_styles();
		$css .= '}';

		wp_add_inline_style( 'ppc-styles', $css );
	}

	/**
	 * Outputs background styles
	 * @return string Background styles
	 */
	public function bg_styles() {
		$css = '';

		$bodyBgType = $this->get_value( 'Background', 'background-type', false );

		if ( ! $bodyBgType ) { return $css; }

		//Background options
		$bgColor   = $this->get_value( 'Background', 'background-color', null );
		$bgImage   = $this->get_value( 'Background', 'background-image', null );
		if ( 'video' == $bodyBgType ) {
			$this->bg_video();
			$bgImage = $this->get_value( 'Background', 'background-responsive-image', null );
		}

		$BgOptions = ' no-repeat ' . $this->get_value( 'Background', 'background-attachment', null ) . ' center/cover';

		//Background styles
		$css .= 'body.pootle-page-customizer-active {';
		if ( 'color' == $bodyBgType && $bgColor ) {
			$css .= "background: {$bgColor} !important;";
		} else if ( $bgImage ) {
			$css .= "background : url({$bgImage}){$BgOptions} !important;";
			$css .= "background-size : cover";
		}
		//Background styles END
		$css .= "}\n";

		return $css;
	}

	/**
	 * Outputs background video
	 * @return string Background video html
	 */
	public function bg_video() {
		$videoUrl = $this->get_value( 'Background', 'background-video', false );
		if ( ! empty( $videoUrl ) ) {
			echo '<script> window.pageCustoVideoUrl = "' . $videoUrl . '";</script>';
			?>
			<video id="page-customizer-bg-video" style="display: none;"
			       preload="auto" autoplay="true" loop="loop" muted="muted" volume="0">
				<?php
				echo "<source src='{$videoUrl}' type='video/mp4'>";
				echo "<source src='{$videoUrl}' type='video/webm'>";
				?>
				Sorry, your browser does not support HTML5 video.
			</video>
			<?php
		}
	}

	/**
	 * Outputs header styles
	 * @return string Header styles
	 */
	public function header_styles() {

		//Header options
		$hideHeader    = $this->get_value( 'Header', 'hide-header', false );
		$headerBgColor = $this->get_value( 'Header', 'header-background-color', null );
		$headerBgImage = $this->get_value( 'Header', 'header-background-image', null );

		//Header styles
		$css = '#main-header, #masthead, #header, #site-header, .site-header, .tc-header{';
		if ( $hideHeader ) {
			$css .= "display : none !important;";
		}
		if ( $headerBgColor ) {
			$css .= "background-color : {$headerBgColor} !important;";
		}
		if ( $headerBgImage ) {
			$css .= "background-image : url({$headerBgImage}) !important;";
			$css .= "background-size : cover !important;";
		}
		//Header styles END
		$css .= "}\n";

		return $css;
	}

	/**
	 * Outputs content styles
	 * @return string Content styles
	 */
	public function content_styles() {
		$css = '';

		//Content options
		$hideBread = $this->get_value( 'Content', 'hide-breadcrumbs', null );
		$hideTitle = $this->get_value( 'Content', 'hide-title', null );
		$hideSidebar = $this->get_value( 'Content', 'hide-sidebar', null );

		//Content styles
		if ( $hideBread ) {
			$css .= "#breadcrumbs, #breadcrumb, .breadcrumbs, .breadcrumb, .breadcrumbs-trail, .wc-breadcrumbs, .wc-breadcrumb, .woocommerce-breadcrumb, .woocommerce-breadcrumbs {\n" .
			        "display : none !important;\n" .
			        "}\n";
		}
		if ( $hideTitle ) {
			$css .= ".main_title, .entry-title {display : none !important;}\n";
		}
		if ( $hideSidebar ) {
			$css .= "#secondary, .widget-area, #sidebar, .sidebar, .side-bar {display : none !important;}\n";
			$css .= "#primary, #content, .content, .content-area { width : 100% !important;}\n";
		}

		return $css;
	}

	/**
	 * Outputs footer styles
	 * @return string Footer styles
	 */
	public function footer_styles() {
		//Footer options
		$hideFooter = $this->get_value( 'Footer', 'hide-footer', false );
		$footerBgColor = $this->get_value( 'Footer', 'footer-background-color', null );

		//Footer styles
		$css = '.colophon, .pootle-page-customizer-active #footer, .pootle-page-customizer-active #main-footer,' .
		       ' .pootle-page-customizer-active #site-footer, .pootle-page-customizer-active .site-footer{';
		if ( $hideFooter ) {
			$css .= "display : none !important;";
		}
		if ( $footerBgColor ) {
			$css .= "background-color : $footerBgColor !important;";
		}
		//Footer styles END
		$css .= "}\n";

		return $css;
	}

	/**
	 * Outputs styles for better user experience
	 * @return string UX improving styles
	 */
	public function ux_styles() {
		$css     = '';
		$theme = (string) wp_get_theme();

		if ( in_array( $theme, array( 'Espied', 'Divi' ) ) ) {
			$css .= "#page, #main-content { background-color: transparent; }";
		}

		return $css;
	}

	/**
	 * Outputs mobile styles
	 * @return string Mobile styles
	 */
	public function mobile_styles() {
		$css     = '';
		$bgColor = $this->get_value( 'Mobile', 'mob-background-color', null );
		$bgImage = $this->get_value( 'Mobile', 'mob-background-image', null );

		$css .= "body.pootle-page-customizer-active {\n" .
		        "background-color : {$bgColor} !important;\n";
		if ( ! empty( $bgImage ) ) {
			$css .= "background-image : url({$bgImage}) !important;\n";
		}
		$css .= "}\n";

		if ( $this->get_value( 'Mobile', 'mob-hide-footer', false ) ) {
			$css .= "#footer, #main-footer, #site-footer, .site-footer{ display : none !important; }";
		}

		if ( $this->get_value( 'Mobile', 'mob-hide-header', false ) ) {
			$css .= "#main-header, #masthead, #header, #site-header, .site-header, .tc-header{ display : none !important; }";
		}

		if ( $this->get_value( 'Mobile', 'mob-hide-sidebar', null ) ) {
			$css .= "aside, .sidebar, .side-bar {display : none !important;}\n";
			$css .= "#content, .content, .content-area { width : 100% !important;}\n";
		}

		return $css;
	}

	/**
	 * SFX Page Customizer Body Class
	 * @param array $classes Body classes
	 * @return array Body classes
	 */
	public function body_class( $classes ) {
		$classes[] = 'pootle-page-customizer-active';
		return $classes;
	}
}