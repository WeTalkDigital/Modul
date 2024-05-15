<?php

/
function create_galeria_post_type_and_taxonomy() {
    // Egyedi bejegyzés
    $labels = array(
        'name' => 'Galériák',
        'singular_name' => 'Galéria',
        'menu_name' => 'Galériák',
        'name_admin_bar' => 'Galéria',
        'add_new' => 'Új hozzáadása',
        'add_new_item' => 'Új Galéria hozzáadása',
        'new_item' => 'Új Galéria',
        'edit_item' => 'Galéria szerkesztése',
        'view_item' => 'Galéria megtekintése',
        'all_items' => 'Összes Galéria',
        'search_items' => 'Galériák keresése',
        'not_found' => 'Nincs találat',
        'not_found_in_trash' => 'Nincs találat a kukában',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'galeria'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('galeria', $args);

    // Kategória
    $taxonomy_labels = array(
        'name' => 'Galéria Kategóriák',
        'singular_name' => 'Galéria Kategória',
        'search_items' => 'Galéria Kategóriák keresése',
        'all_items' => 'Összes Galéria Kategória',
        'parent_item' => 'Szülő Galéria Kategória',
        'parent_item_colon' => 'Szülő Galéria Kategória:',
        'edit_item' => 'Galéria Kategória szerkesztése',
        'update_item' => 'Galéria Kategória frissítése',
        'add_new_item' => 'Új Galéria Kategória hozzáadása',
        'new_item_name' => 'Új Galéria Kategória név',
        'menu_name' => 'Galéria Kategória',
    );

    $taxonomy_args = array(
        'labels' => $taxonomy_labels,
        'hierarchical' => true,
        'show_in_rest' => true,
    );

    register_taxonomy('galeria-kategoria', 'galeria', $taxonomy_args);
}
add_action('init', 'create_galeria_post_type_and_taxonomy');


function activate_galeria_plugin() {
    create_galeria_post_type_and_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_galeria_plugin');

// Shortcode 
function galeria_slider_shortcode($atts) {

    $atts = shortcode_atts(
        array(
            'kategoria' => '',
        ),
        $atts,
        'galeria_slider'
    );

    $args = array(
        'post_type' => 'galeria',
        'posts_per_page' => -1,
    );

    if (!empty($atts['kategoria'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'galeria-kategoria',
                'field'    => 'slug',
                'terms'    => $atts['kategoria'],
            ),
        );
    }

    $query = new WP_Query($args);

    $slider_html = '<div class="galeria-slider">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $kep_id = get_post_meta(get_the_ID(), 'kepek', true);
            $kep_url = wp_get_attachment_url($kep_id);

            $slider_html .= '
                <div class="slider-item" style="background-image: url(\'' . $kep_url . '\'); background-size: cover; background-position: center; height: 650px;">
                </div>
            ';
        }
    }

    $slider_html .= '</div>';
    $slider_html .= '
        <div class="galeria-controls">
            <span id="galeria-prev"> <svg class="galeria-perv" xmlns="http://www.w3.org/2000/svg" width="61" height="8" viewBox="0 0 61 8" fill="none">
            <path d="M0.646447 3.64645C0.451184 3.84171 0.451184 4.15829 0.646447 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646447 3.64645ZM1 4.5H61V3.5H1V4.5Z" fill="#BE7C4B"/>
            </svg></span>
            <div class="galeria-counter">
                <span class="galeria-current">1</span> <span class="galeria-divider">/</span> <span class="galeria-total"></span>
            </div>
            <span id="galeria-next"> <svg class="galeria-next" xmlns="http://www.w3.org/2000/svg" width="61" height="8" viewBox="0 0 61 8" fill="none">
            <path d="M60.3536 4.35355C60.5488 4.15829 60.5488 3.84171 60.3536 3.64645L57.1716 0.464466C56.9763 0.269204 56.6597 0.269204 56.4645 0.464466C56.2692 0.659728 56.2692 0.976311 56.4645 1.17157L59.2929 4L56.4645 6.82843C56.2692 7.02369 56.2692 7.34027 56.4645 7.53553C56.6597 7.7308 56.9763 7.7308 57.1716 7.53553L60.3536 4.35355ZM0 4.5H60V3.5H0V4.5Z" fill="#BE7C4B"/>
            </svg></span>
        </div>
    ';

    wp_reset_postdata();

    return $slider_html;
}
add_shortcode('galeria_slider', 'galeria_slider_shortcode');

function galeria_slider_styles() {
    wp_add_inline_style('slick-css', '');
}
add_action('wp_enqueue_scripts', 'galeria_slider_styles');

function galeria_slider_scripts() {
    wp_enqueue_style('slick-css', plugins_url('slick/slick.css', __FILE__));
    wp_enqueue_script('slick-js', plugins_url('slick/slick.min.js', __FILE__), array('jquery'), '1.8.1', true);
    wp_enqueue_script('custom-slick-js', plugins_url('slick/custom-slick.js', __FILE__), array('jquery', 'slick-js'), '1.0.6', true);
}
add_action('wp_enqueue_scripts', 'galeria_slider_scripts');

?>
