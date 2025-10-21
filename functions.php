<?php
// Carga hojas de estilo del child theme
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
function storefront_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-main', get_stylesheet_directory_uri() . '/assets/css/main.css', ['parent-style'], '1.0');
};
// Carga scripts del child theme
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_scripts');
function storefront_child_enqueue_scripts() {
    wp_enqueue_script('child-main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0', true);
    wp_script_add_data('child-main-js', 'type', 'module');
}


