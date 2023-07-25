<?php

// =============================================================================
// CONSTANTS
// =============================================================================

define( 'CHILD_VERSION', '1.0.1' );
define( 'CHILD_NAME', 'Fictioneer Child Theme' );

// =============================================================================
// CHILD THEME SETUP
// =============================================================================

/**
 * Enqueue child theme styles and scripts
 */

function fictioneer_child_enqueue_styles_and_scripts() {
  $parenthandle = 'fictioneer-application';

  // Enqueue styles
  wp_enqueue_style(
    'fictioneer-child-style',
    get_stylesheet_directory_uri() . '/css/fictioneer-child-style.css',
    array( $parenthandle )
  );

  // Register scripts
  wp_register_script(
    'child-script-handle',
    get_stylesheet_directory_uri() . '/js/fictioneer-child-script.js',
    ['fictioneer-application-scripts'],
    false,
    true
  );

  // Enqueue scripts
  wp_enqueue_script( 'fictioneer-child-scripts' );
}
add_action( 'wp_enqueue_scripts', 'fictioneer_child_enqueue_styles_and_scripts', 99 );

/**
 * Remove or customize parent filters and actions on the frontend
 */

function fictioneer_child_customize_parent() {
  /*

  Example: Prevent custom story/chapter CSS from being applied

  remove_action( 'wp_head', 'fictioneer_add_fiction_css', 10 );

  */
}
add_action( 'init', 'fictioneer_child_customize_parent' );

/**
 * Remove or customize parent filters and actions in the admin panel
 */

function fictioneer_child_customize_admin() {
  /*

  Example: Remove SEO meta box for non-administrators

  if ( ! current_user_can( 'administrator' ) ) {
    remove_action( 'add_meta_boxes', 'fictioneer_add_seo_metabox', 10 );
  }

  */
}
add_action( 'admin_init', 'fictioneer_child_customize_admin' );

?>
