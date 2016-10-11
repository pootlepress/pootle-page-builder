<?php
/**
 * Pootle Page Builder Mobile Designer Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class PPB_Mobile_Designer_Admin {

	/**
	 * @var    PPB_Mobile_Designer_Admin Instance
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Page Builder Mobile Designer Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return PPB_Mobile_Designer_Admin instance
	 * @since    1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since    1.0.0
	 */
	private function __construct() {
		$this->token   = PPB_Mobile_Designer::$token;
		$this->url     = PPB_Mobile_Designer::$url;
		$this->path    = PPB_Mobile_Designer::$path;
		$this->version = PPB_Mobile_Designer::$version;
	} // End __construct()

	/**
	 * Adds row settings panel tab
	 *
	 * @param array $tabs The array of tabs
	 *
	 * @return array Tabs
	 * @filter pootlepb_row_settings_tabs
	 * @since    1.0.0
	 */
	public function row_settings_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label'    => 'Mobile Designer',
			'priority' => 5,
		);

		return $tabs;
	}

	/**
	 * Adds row settings panel fields
	 *
	 * @param array $fields Fields to output in row settings panel
	 *
	 * @return array Tabs
	 * @filter pootlepb_row_settings_fields
	 * @since    1.0.0
	 */
	public function row_settings_fields( $fields ) {
		$fields[ $this->token . '_hide' ] = array(
			'name'    => 'Hide',
			'type'    => 'multi-select',
			'tab'     => 'layout',
			'options' => array(
				'hide-on-mobile'  => 'On mobile',
				'hide-on-tablet'  => 'On tablets',
				'hide-on-desktop' => 'On desktop',
			),
		);

		return $fields;
	}

	/**
	 * Adds editor panel tab
	 *
	 * @param array $tabs The array of tabs
	 *
	 * @return array Tabs
	 * @filter pootlepb_content_block_tabs
	 * @since    1.0.0
	 */
	public function content_block_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label'    => 'Mobile Designer',
			'priority' => 5,
		);

		return $tabs;
	}

	/**
	 * Adds content block panel fields
	 *
	 * @param array $fields Fields to output in content block panel
	 *
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since    1.0.0
	 */
	public function content_block_fields( $fields ) {
		$mob_designer = array(
			'font-size'           => array(
				'name' => 'Font Size',
				'type' => 'number',
				'unit' => 'px',
			),
			'letter-spacing'      => array(
				'name' => 'Letter spacing',
				'type' => 'number',
				'unit' => 'px',
			),
			'text-transform'      => array(
				'name'    => 'Text transform',
				'type'    => 'select',
				'options' => array(
					''           => 'Default',
					'uppercase'  => 'Uppercase',
					'lowercase'  => 'Lowercase',
					'capitalize' => 'Capitalize',
				),
			),
			'text-align'          => array(
				'name'    => 'Text align',
				'type'    => 'select',
				'options' => array(
					''       => 'Default',
					'center' => 'Center',
					'left'   => 'Left',
					'right'  => 'Right',
				),
			),
			'margin'              => array(
				'name' => 'Margin',
				'type' => 'number',
				'unit' => 'px',
			),
			'padding'             => array(
				'name' => 'Padding',
				'type' => 'number',
				'unit' => 'px',
			),
			'background-position' => array(
				'name'    => 'Background position',
				'type'    => 'select',
				'options' => array(
					''              => 'Default',
					'center top'    => 'Top',
					'center center' => 'Center',
					'center bottom' => 'Bottom',
					'left center'   => 'Left',
					'right center'  => 'Right',
					'left top'      => 'Left top',
					'left bottom'   => 'Left bottom',
					'right top'     => 'Right top',
					'right bottom'  => 'Right bottom',
				),
			),
		);

		$breakpoints = array(
			'mob' => 'Mobile phones <small>Smaller than <code>700px</code></small>',
			'tab' => 'Tablets <small><code>700px</code> to <code>1024px</code></small>',
			'dsk' => 'Desktops <small>Larger than <code>1024px</code></small>',
		);

		$i = 1;

		$fields["{$this->token}-styles"] = array(
			'name'     => <<<HTML
<style>
	.mob-dez-section {border-bottom: 1px solid #ccc;padding: 1px;}
	.mob-dez-section h4 {font-size: 1em;font-weight: normal;letter-spacing: 2px;background: #eee;margin: 0;padding: 0.5em;cursor: pointer;}
	.mob-dez-section h4 small {float: right;}
	.mob-dez-section h4 + div {margin: 0;padding: 0 1em 1em;}
</style>
HTML
		,
			'tab'      => $this->token,
			'type'     => 'html',
			'priority' => $i ++,
		);

		foreach ( $breakpoints as $bp => $hd ) {
			$fields["{$this->token}-$bp-open"] = array(
				'name'     => "<div class='mob-dez-section mob-dez-section-$bp'><h4 onclick=\"jQuery('.mob-dez-section-fields-$bp').slideToggle();\">$hd</h4><div class='mob-dez-section-fields-$bp' style='display: none;'>",
				'tab'      => $this->token,
				'type'     => 'html',
				'priority' => $i ++,
			);
			foreach ( $mob_designer as $k => $f ) {
				$k             = "{$this->token}|$bp|$k";
				$f['tab']      = $this->token;
				$f['priority'] = $i ++;
				$fields[ $k ]  = $f;
			}
			$fields["{$this->token}-$bp-close"] = array(
				'name'     => "</div></div>",
				'tab'      => $this->token,
				'type'     => 'html',
				'priority' => $i ++,
			);
		}

		return $fields;
	}
}