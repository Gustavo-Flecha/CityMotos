<?php
// Carga hojas de estilo del child theme
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
function storefront_child_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-main', get_stylesheet_directory_uri() . '/assets/css/main.css', ['parent-style'], '1.0');
};
// Carga scripts del child theme
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_scripts');
function storefront_child_enqueue_scripts()
{
    wp_enqueue_script('child-main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0', true);
    wp_script_add_data('child-main-js', 'type', 'module');
}

//-----------------------------------------Menu---------------------------------//

// Overlay detrás del menú
add_action('storefront_before_content', 'citymotos_menu_overlay', 0);
function citymotos_menu_overlay()
{
    echo '<div id="menu-overlay" class="menu-overlay"></div>';
}
// Contenedor del menú + manejador vertical
add_action('storefront_before_content', 'citymotos_menu_wrapper', 0);
function citymotos_menu_wrapper()
{
    echo '
    <div class="citymotos-menu-wrapper">
        <div class="menu-handle">MENÚ</div>
    </div>
    ';
}

// Función encargada de generar el bloque HTML del menú lateral.
function citymotos_category_menu_widget()
{

    $args = [
        'taxonomy'   => 'product_cat',
        'parent'     => 0, // solo categorías principales
        'hide_empty' => false
    ];

    $categories = get_terms($args);

    if (empty($categories) || is_wp_error($categories)) return;

    echo '<div class="citymotos-category-menu">';
    echo '<h3 class="menu-title">Categorías</h3>';
    echo '<ul class="accordion-categories">';

    foreach ($categories as $cat) {

        // Ícono categoría padre
        $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
        $icon = $thumb_id ? wp_get_attachment_url($thumb_id) : '';

        if (!$icon) {
            $icon = get_stylesheet_directory_uri() . '/assets/default-icon.png';
        }

        // Subcategorías
        $subcats = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $cat->term_id,
            'hide_empty' => false
        ]);

        echo '<li class="cat-item">';

        // 🔥 BOTÓN CON LINK AL PADRE
        echo '<a class="accordion-toggle" href="' . esc_url(get_term_link($cat)) . '">';
        echo '  <img src="' . esc_url($icon) . '" alt="' . esc_attr($cat->name) . '">';
        echo '  <span>' . esc_html($cat->name) . '</span>';
        echo '  <svg class="arrow" width="14" height="14"><path d="M4 5l3 3 3-3" stroke="#333" stroke-width="2" fill="none"/></svg>';
        echo '</a>';

        // Lista de subcategorías con íconos
        if (!empty($subcats)) {
            echo '<ul class="subcat-list">';

            foreach ($subcats as $sub) {

                // Imagen subcategoría
                $sub_thumb_id = get_term_meta($sub->term_id, 'thumbnail_id', true);
                $sub_icon = $sub_thumb_id ? wp_get_attachment_url($sub_thumb_id) : '';

                if (!$sub_icon) {
                    $sub_icon = get_stylesheet_directory_uri() . '/assets/default-icon.png';
                }

                echo '<li class="subcat-item">';
                echo '  <a href="' . esc_url(get_term_link($sub)) . '">';
                echo '      <img src="' . esc_url($sub_icon) . '" alt="' . esc_attr($sub->name) . '">';
                echo '      <span>' . esc_html($sub->name) . '</span>';
                echo '  </a>';
                echo '</li>';
            }

            echo '</ul>';
        }

        echo '</li>';
    }


    echo '</ul>';
    echo '</div>';
}


// Función para abrir/cerrar el acordeón
function citymotos_enqueue_scripts()
{
    wp_enqueue_script(
        'citymotos-accordion',
        get_stylesheet_directory_uri() . '/assets/js/menu-accordion.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'citymotos_enqueue_scripts');
add_action('storefront_before_content', 'citymotos_category_menu_widget', 1);
