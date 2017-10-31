<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 30/9/15
 * Time: 1:50 PM
 */
global $page_customizer_fields;
$page_customizer_fields = array(

	//Background Controls
	'background-type' => array(
		'id'      => 'background-type',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Background', 'pootle-page-builder' ),
		'type'    => 'select',
		'choices' => array(
			''      => __( 'Default', 'pootle-page-builder' ),
			'color' => __( 'Color', 'pootle-page-builder' ),
			'image' => __( 'Image', 'pootle-page-builder' ),
			'video' => __( 'Video', 'pootle-page-builder' ),
		),
		'default' => '',
	),
	'background-video' => array(
		'id'      => 'background-video',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Background Video', 'pootle-page-builder' ),
		'type'    => 'upload',
		'mime-type' => 'video',
		'default' => '',
	),
	'background-responsive-image' => array(
		'id'      => 'background-responsive-image',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Responsive image', 'pootle-page-builder' ),
		'type'    => 'image',
		'default' => '',
	),
	'background-image' => array(
		'id'      => 'background-image',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Page background image', 'pootle-page-builder' ),
		'type'    => 'image',
		'default' => '',
	),
	'background-attachment' => array(
		'id'      => 'background-attachment',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Background attachment', 'pootle-page-builder' ),
		'type'    => 'radio',
		'default' => 'scroll',
		'choices' => array( 'fixed' => 'Fixed', 'scroll' => 'Scroll' )
	),
	'background-color' => array(
		'id'      => 'background-color',
		'section' => __( 'Background', 'pootle-page-builder' ),
		'label' => __( 'Page background color', 'pootle-page-builder' ),
		'type'    => 'lib_color',
		'default' => '',
	),

	//Header Options
	'hide-header' => array(
		'id'      => 'hide-header',
		'section' => __( 'Header', 'pootle-page-builder' ),
		'label' => __( 'Hide header', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'header-background-image' => array(
		'id'      => 'header-background-image',
		'section' => __( 'Header', 'pootle-page-builder' ),
		'label' => __( 'Header background image', 'pootle-page-builder' ),
		'type'    => 'image',
		'default' => '',
	),
	'header-background-color' => array(
		'id'      => 'header-background-color',
		'section' => __( 'Header', 'pootle-page-builder' ),
		'label' => __( 'Header background color', 'pootle-page-builder' ),
		'type'    => 'lib_color',
		'default' => '',
	),

	//Content
	'hide-breadcrumbs' => array(
		'id'      => 'hide-breadcrumbs',
		'section' => __( 'Content', 'pootle-page-builder' ),
		'label' => __( 'Hide breadcrumbs', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'hide-sidebar' => array(
		'id'      => 'hide-sidebar',
		'section' => __( 'Content', 'pootle-page-builder' ),
		'label' => __( 'Hide sidebar', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'hide-title' => array(
		'id'      => 'hide-title',
		'section' => __( 'Content', 'pootle-page-builder' ),
		'label' => __( 'Hide title', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),

	//Footer
	'hide-footer' => array(
		'id'      => 'hide-footer',
		'section' => __( 'Footer', 'pootle-page-builder' ),
		'label' => __( 'Hide footer', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'footer-background-color' => array(
		'id'      => 'footer-background-color',
		'section' => __( 'Footer', 'pootle-page-builder' ),
		'label' => __( 'Footer background color', 'pootle-page-builder' ),
		'type'    => 'lib_color',
		'default' => '',
	),

	//Mobile
	'mob-background-image' => array(
		'id'      => 'mob-background-image',
		'section' => __( 'Mobile', 'pootle-page-builder' ),
		'label' => __( 'Page background image for Mobile view', 'pootle-page-builder' ),
		'type'    => 'image',
		'default' => '',
	),
	'mob-background-color' => array(
		'id'      => 'mob-background-color',
		'section' => __( 'Mobile', 'pootle-page-builder' ),
		'label' => __( 'Page background color for Mobile view', 'pootle-page-builder' ),
		'type'    => 'lib_color',
		'default' => '',
	),
	'mob-hide-footer' => array(
		'id'      => 'mob-hide-footer',
		'section' => __( 'Mobile', 'pootle-page-builder' ),
		'label' => __( 'Hide footer in Mobile view', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'mob-hide-header' => array(
		'id'      => 'mob-hide-header',
		'section' => __( 'Mobile', 'pootle-page-builder' ),
		'label' => __( 'Hide header in Mobile view', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
	'mob-hide-sidebar' => array(
		'id'      => 'mob-hide-sidebar',
		'section' => __( 'Mobile', 'pootle-page-builder' ),
		'label' => __( 'Hide sidebar in Mobile view', 'pootle-page-builder' ),
		'type'    => 'checkbox',
		'default' => '',
	),
);
