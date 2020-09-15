<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'select2','fancybox','animate','stm_theme_styles','stm-stm_layout_styles-online-light','stm_theme_styles_animation','stm-headers-header_2','stm-headers_transparent-header_2_transparent' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/select2.min.css' ) ):
            wp_deregister_style( 'select2' );
            wp_register_style( 'select2', trailingslashit( get_template_directory_uri() ) . 'assets/css/select2.min.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/jquery.fancybox.css' ) ):
            wp_deregister_style( 'fancybox' );
            wp_register_style( 'fancybox', trailingslashit( get_template_directory_uri() ) . 'assets/css/jquery.fancybox.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/animate.css' ) ):
            wp_deregister_style( 'animate' );
            wp_register_style( 'animate', trailingslashit( get_template_directory_uri() ) . 'assets/css/animate.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/styles.css' ) ):
            wp_deregister_style( 'stm_theme_styles' );
            wp_register_style( 'stm_theme_styles', trailingslashit( get_template_directory_uri() ) . 'assets/css/styles.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/vc_modules/stm_layout_styles/online-light.css' ) ):
            wp_deregister_style( 'stm-stm_layout_styles-online-light' );
            wp_register_style( 'stm-stm_layout_styles-online-light', trailingslashit( get_template_directory_uri() ) . 'assets/css/vc_modules/stm_layout_styles/online-light.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/animation.css' ) ):
            wp_deregister_style( 'stm_theme_styles_animation' );
            wp_register_style( 'stm_theme_styles_animation', trailingslashit( get_template_directory_uri() ) . 'assets/css/animation.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/vc_modules/headers/header_2.css' ) ):
            wp_deregister_style( 'stm-headers-header_2' );
            wp_register_style( 'stm-headers-header_2', trailingslashit( get_template_directory_uri() ) . 'assets/css/vc_modules/headers/header_2.css' );
        endif;
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/vc_modules/headers_transparent/header_2_transparent.css' ) ):
            wp_deregister_style( 'stm-headers_transparent-header_2_transparent' );
            wp_register_style( 'stm-headers_transparent-header_2_transparent', trailingslashit( get_template_directory_uri() ) . 'assets/css/vc_modules/headers_transparent/header_2_transparent.css' );
        endif;
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'custom-style.css', array( 'chld_thm_cfg_parent','stm_theme_style','language_center' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION


