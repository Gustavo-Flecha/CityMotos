<?php
// Carga hojas de estilo del child theme
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
function storefront_child_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-main', get_stylesheet_directory_uri() . '/assets/css/main.css', ['parent-style'], '1.0');
};


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


        // Subcategorías
        $subcats = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $cat->term_id,
            'hide_empty' => false
        ]);

        echo '<li class="cat-item">';

        //  Boton acordeón categoría padre
        echo '<a class="accordion-toggle" href="' . esc_url(get_term_link($cat)) . '">';
        echo '  <img src="' . esc_url($icon) . '" alt="' . esc_attr($cat->name) . '">';
        echo '  <span>' . esc_html($cat->name) . '</span>';
        echo '  <svg class="arrow" width="14" height="14"><path d="M4 5l3 3 3-3" stroke="#333" stroke-width="2" fill="none"/></svg>';
        echo '</a>';

        // Lista de subcategorías con íconos
        if (!empty($subcats)) {
            echo '<ul class="subcat-list">';

            foreach ($subcats as $sub) {

                add_action('storefront_before_content', 'citymotos_menu_overlay', 0);
                // Imagen subcategoría
                $sub_thumb_id = get_term_meta($sub->term_id, 'thumbnail_id', true);
                $sub_icon = $sub_thumb_id ? wp_get_attachment_url($sub_thumb_id) : '';

                echo '<li class="subcat-item">';
                echo '  <a href="' . esc_url(get_term_link($sub)) . '">';
                echo '      <img src="' . esc_url($sub_icon) . '" alt="' . esc_attr($sub->name) . '">';
                echo '      <span>' . esc_html($sub->name) . '</span>';
                echo '  </a>';
                add_action('storefront_before_content', 'citymotos_menu_overlay', 0);
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



//-----------------------------------------Checkout minimalista---------------------------------//

add_filter('woocommerce_checkout_fields', 'cm_checkout_minimalista');

function cm_checkout_minimalista($fields)
{

    $billing = array(
        'billing_first_name' => $fields['billing']['billing_first_name'],
        'billing_email'      => $fields['billing']['billing_email'],
    );

    $fields['billing'] = $billing;

    return $fields;
}


// ------------------------------Slider para homepage con imágenes hotspots-------------------------//

function cm_slider_scripts()
{
    wp_enqueue_script(
        'citymotos-slider',
        get_stylesheet_directory_uri() . '/assets/js/cm-slider.js',
        array(),
        false,
        true
    );
}
add_action('wp_enqueue_scripts', 'cm_slider_scripts');



function cm_slider_shortcode() {

    ob_start();

    /**
     * Acá definimos:
     * - imagen
     * - hotspots (posición + slug de categoría)
     */

    $slides = [

        [
            'img' => '/wp-content/uploads/2026/04/moto.webp',
            'alt' => 'Moto',
            'hotspots' => [
                ['top' => '60%', 'left' => '80%', 'cat' => 'Cubiertas y Llantas'],
                ['top' => '30%', 'left' => '65%', 'cat' => 'Parte Eléctrica'],
                ['top' => '55%', 'left' => '55%', 'cat' => 'Repuestos Motos'],
                ['top' => '65%', 'left' => '30%', 'cat' => 'Transmisión'],
                ['top' => '70%', 'left' => '50%', 'cat' => 'Aceites 4T'],
            ]
        ],

        [
            'img' => '/wp-content/uploads/2026/04/motosierra.webp',
            'alt' => 'Motosierra',
            'hotspots' => [
                ['top' => '60%', 'left' => '20%', 'cat' => 'Espadas y cadenas'],
                ['top' => '65%', 'left' => '65%', 'cat' => 'Repuestos Motosierra'],
                ['top' => '35%', 'left' => '70%', 'cat' => 'Motosierras'],
            ]
        ],

        [
            'img' => '/wp-content/uploads/2026/04/motoguadania.webp',
            'alt' => 'Motoguadaña',
            'hotspots' => [
                ['top' => '40%', 'left' => '20%', 'cat' => 'Cabezal y Cuchillas'],
                ['top' => '60%', 'left' => '75%', 'cat' => 'Motoguadañas'],
                ['top' => '75%', 'left' => '80%', 'cat' => 'Aceites 2T'],
                ]
        ]

    ];

    echo '<div class="cm-slider">';
    echo '<div class="cm-slides">';

    /**
     *  Recorremos cada slide
     */
    foreach ($slides as $index => $slide) {

        // Primer slide activo
        $active = ($index === 0) ? ' active' : '';

        echo '<div class="cm-slide' . $active . '">';

            // Imagen
            echo '<img src="' . home_url($slide['img']) . '">';

            /**
             * Hotspots: por cada hotspot, traemos la categoría por slug, 
             * obtenemos su link y nombre para tooltip, 
             * y generamos el enlace posicionado sobre la imagen.
             */
            foreach ($slide['hotspots'] as $hotspot) {

                // Traemos la categoría por SLUG definido en el array
                $term = get_term_by('slug', $hotspot['cat'], 'product_cat');

                // Si existe la categoría seguimos, sino no hacemos nada
                if ($term) {

                    //  Link de la categoría
                    $link = get_term_link($term);

                    // Nombre de la categoría (para tooltip)
                    $name = $term->name;

                    echo '<a href="' . esc_url($link) . '" 
                            class="cm-hotspot" 
                            style="top:' . $hotspot['top'] . '; left:' . $hotspot['left'] . ';">
                            <span class="cm-tooltip">' . esc_html($name) . '</span>
                          </a>';
                }
            }

        echo '</div>';
    }

    echo '</div>';

    echo '<button class="cm-prev">‹</button>';
    echo '<button class="cm-next">›</button>';

    echo '</div>';

    return ob_get_clean();
}

add_shortcode('cm_slider', 'cm_slider_shortcode');

