<?php

// =============================================================================
// CONSTANTS
// =============================================================================

define( 'CHILD_VERSION', '1.0.0' );
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

?>
