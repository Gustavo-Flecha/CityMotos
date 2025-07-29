<?php
// Carga hojas de estilo del child theme
function citymotos_child_enqueue_styles() {
    $parent_style = 'storefront-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('citymotos-child-style', get_stylesheet_directory_uri() . '/style.css', [$parent_style], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'citymotos_child_enqueue_styles');
