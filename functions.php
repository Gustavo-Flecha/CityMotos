<?php
/**
 * Funciones del child theme CityMotos para Storefront.
 *
 * @package CityMotos_Child
 */

/**
 * Devuelve una versión basada en fecha de modificación para limpiar caché de assets.
 *
 * @param string $relative_path Ruta del asset relativa al directorio del child theme.
 * @return string
 */
function citymotos_get_asset_version( $relative_path ) {
    $asset_path = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );

    return file_exists( $asset_path ) ? (string) filemtime( $asset_path ) : '1.0';
}

/**
 * Carga los estilos del tema padre Storefront y el bundle CSS del child theme.
 */
function storefront_child_enqueue_styles() {
    $main_css_ver = max(
        (int) citymotos_get_asset_version( 'assets/css/main.css' ),
        (int) citymotos_get_asset_version( 'assets/css/pages/responsive.css' )
    );

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme( 'storefront' )->get( 'Version' ) );
    wp_enqueue_style( 'child-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array( 'parent-style' ), $main_css_ver );
}
add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );


//-----------------------------------------Menu---------------------------------//

/**
 * Imprime una sola vez el overlay del menú lateral antes del contenido.
 */
function citymotos_menu_overlay() {
    echo '<div id="menu-overlay" class="menu-overlay"></div>';
}
add_action( 'storefront_before_content', 'citymotos_menu_overlay', 0 );

/**
 * Imprime el manejador fijo que abre el menú de categorías.
 */
function citymotos_menu_wrapper() {
    echo '
    <div class="citymotos-menu-wrapper">
        <button class="menu-handle" type="button" aria-label="Abrir menú de categorías">MENÚ</button>
    </div>
    ';
}
add_action( 'storefront_before_content', 'citymotos_menu_wrapper', 0 );

/**
 * Devuelve la URL de la miniatura de una categoría WooCommerce cuando existe.
 *
 * @param int $term_id ID del término de categoría de producto.
 * @return string
 */
function citymotos_get_category_thumbnail_url( $term_id ) {
    $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );

    return $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
}

/**
 * Imprime el acordeón lateral de categorías de producto WooCommerce.
 */
function citymotos_category_menu_widget() {

    $args = [
        'taxonomy'   => 'product_cat',
        'parent'     => 0, // solo categorías principales
        'hide_empty' => false,
    ];

    $categories = get_terms( $args );

    if ( empty( $categories ) || is_wp_error( $categories ) ) {
        return;
    }

    echo '<div class="citymotos-category-menu">';
    echo '<h3 class="menu-title">Categorías</h3>';
    echo '<ul class="accordion-categories">';

    foreach ( $categories as $cat ) {
        $icon = citymotos_get_category_thumbnail_url( $cat->term_id );

        // Subcategorías
        $subcats = get_terms( [
            'taxonomy'   => 'product_cat',
            'parent'     => $cat->term_id,
            'hide_empty' => false,
        ] );

        echo '<li class="cat-item">';

        //  Boton acordeón categoría padre
        echo '<a class="accordion-toggle" href="' . esc_url( get_term_link( $cat ) ) . '">';
        if ( $icon ) {
            echo '  <img src="' . esc_url( $icon ) . '" alt="' . esc_attr( $cat->name ) . '" loading="lazy" decoding="async">';
        }
        echo '  <span>' . esc_html( $cat->name ) . '</span>';
        echo '  <svg class="arrow" width="14" height="14" aria-hidden="true" focusable="false"><path d="M4 5l3 3 3-3" stroke="#333" stroke-width="2" fill="none"/></svg>';
        echo '</a>';

        // Lista de subcategorías con íconos
        if ( ! empty( $subcats ) && ! is_wp_error( $subcats ) ) {
            echo '<ul class="subcat-list">';

            foreach ( $subcats as $sub ) {
                $sub_icon = citymotos_get_category_thumbnail_url( $sub->term_id );

                echo '<li class="subcat-item">';
                echo '  <a href="' . esc_url( get_term_link( $sub ) ) . '">';
                if ( $sub_icon ) {
                    echo '      <img src="' . esc_url( $sub_icon ) . '" alt="' . esc_attr( $sub->name ) . '" loading="lazy" decoding="async">';
                }
                echo '      <span>' . esc_html( $sub->name ) . '</span>';
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

/**
 * Carga el comportamiento de acordeón del menú de categorías CityMotos.
 */
function citymotos_enqueue_scripts() {
    wp_enqueue_script(
        'citymotos-accordion',
        get_stylesheet_directory_uri() . '/assets/js/menu-accordion.js',
        array( 'jquery' ),
        citymotos_get_asset_version( 'assets/js/menu-accordion.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'citymotos_enqueue_scripts' );
add_action( 'storefront_before_content', 'citymotos_category_menu_widget', 1 );

/**
 * Mueve la paginación del catálogo al final y evita que Storefront la duplique arriba.
 */
function citymotos_move_catalog_pagination_to_bottom() {
    remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 10 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 30 );

    if ( ! has_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' ) ) {
        add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
    }
}
add_action( 'wp', 'citymotos_move_catalog_pagination_to_bottom', 20 );



//-----------------------------------------Checkout minimalista---------------------------------//

add_filter( 'woocommerce_checkout_fields', 'cm_checkout_minimalista' );

/**
 * Mantiene el checkout mínimo para compras móviles más rápidas.
 *
 * @param array $fields Campos de checkout de WooCommerce.
 * @return array
 */
function cm_checkout_minimalista( $fields ) {

    $billing = array(
        'billing_first_name' => $fields['billing']['billing_first_name'],
        'billing_email'      => $fields['billing']['billing_email'],
    );

    $fields['billing'] = $billing;

    return $fields;
}


// ------------------------------Slider para homepage con imágenes hotspots-------------------------//

/**
 * Carga el script del slider de portada con hotspots.
 */
function cm_slider_scripts() {
    wp_enqueue_script(
        'citymotos-slider',
        get_stylesheet_directory_uri() . '/assets/js/cm-slider.js',
        array(),
        citymotos_get_asset_version( 'assets/js/cm-slider.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'cm_slider_scripts' );

/**
 * Sanea un valor de posición CSS usado por los hotspots.
 *
 * @param string $position Posición CSS en porcentaje.
 * @return string
 */
function citymotos_sanitize_hotspot_position( $position ) {
    return preg_match( '/^\d{1,3}(\.\d+)?%$/', $position ) ? $position : '50%';
}

/**
 * Resuelve una categoría de producto por slug y usa el nombre como respaldo.
 *
 * @param string $category Slug o nombre visible de la categoría.
 * @return WP_Term|false
 */
function citymotos_get_product_category_for_hotspot( $category ) {
    $term = get_term_by( 'slug', sanitize_title( $category ), 'product_cat' );

    if ( ! $term ) {
        $term = get_term_by( 'name', $category, 'product_cat' );
    }

    return $term;
}

/**
 * Renderiza el slider de portada con hotspots clickeables hacia categorías WooCommerce.
 *
 * @return string
 */
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
            ],
        ],

        [
            'img' => '/wp-content/uploads/2026/04/motosierra.webp',
            'alt' => 'Motosierra',
            'hotspots' => [
                ['top' => '60%', 'left' => '20%', 'cat' => 'Espadas y cadenas'],
                ['top' => '65%', 'left' => '65%', 'cat' => 'Repuestos Motosierra'],
                ['top' => '35%', 'left' => '70%', 'cat' => 'Motosierras'],
            ],
        ],

        [
            'img' => '/wp-content/uploads/2026/04/motoguadania.webp',
            'alt' => 'Motoguadaña',
            'hotspots' => [
                ['top' => '40%', 'left' => '20%', 'cat' => 'Cabezal y Cuchillas'],
                ['top' => '60%', 'left' => '75%', 'cat' => 'Motoguadañas'],
                ['top' => '75%', 'left' => '80%', 'cat' => 'Aceites 2T'],
            ],
        ],

    ];

    echo '<div class="cm-slider">';
    echo '<div class="cm-slides">';

    /**
     *  Recorremos cada slide
     */
    foreach ( $slides as $index => $slide ) {

        // Primer slide activo
        $active = ( 0 === $index ) ? ' active' : '';

        echo '<div class="cm-slide' . esc_attr( $active ) . '">';

            // Imagen
            echo '<img src="' . esc_url( home_url( $slide['img'] ) ) . '" alt="' . esc_attr( $slide['alt'] ) . '" loading="lazy" decoding="async">';

            /**
             * Hotspots: por cada hotspot, traemos la categoría por slug, 
             * obtenemos su link y nombre para tooltip, 
             * y generamos el enlace posicionado sobre la imagen.
             */
            foreach ( $slide['hotspots'] as $hotspot ) {

                // Traemos la categoría por SLUG definido en el array
                $term = citymotos_get_product_category_for_hotspot( $hotspot['cat'] );

                // Si existe la categoría seguimos, sino no hacemos nada
                if ( $term && ! is_wp_error( $term ) ) {

                    //  Link de la categoría
                    $link = get_term_link( $term );

                    if ( is_wp_error( $link ) ) {
                        continue;
                    }

                    // Nombre de la categoría (para tooltip)
                    $name = $term->name;
                    $style = sprintf(
                        'top:%s; left:%s;',
                        citymotos_sanitize_hotspot_position( $hotspot['top'] ),
                        citymotos_sanitize_hotspot_position( $hotspot['left'] )
                    );

                    echo '<a href="' . esc_url( $link ) . '" 
                            class="cm-hotspot" 
                            aria-label="' . esc_attr( $name ) . '"
                            style="' . esc_attr( $style ) . '">
                            <span class="cm-tooltip">' . esc_html( $name ) . '</span>
                          </a>';
                }
            }

        echo '</div>';
    }

    echo '</div>';

    echo '<button class="cm-prev" type="button" aria-label="Slide anterior">‹</button>';
    echo '<button class="cm-next" type="button" aria-label="Slide siguiente">›</button>';

    echo '</div>';

    return ob_get_clean();
}

add_shortcode( 'cm_slider', 'cm_slider_shortcode' );