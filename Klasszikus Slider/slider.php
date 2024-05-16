<?php
// Bejegyzés
function slider_post_type() {
    $labels = array(
        'name' => __('Sliders'),
        'singular_name' => __('Slider'),
        'menu_name' => __('Sliders'),
        'name_admin_bar' => __('Slider'),
        'add_new' => __('Új hozzáadása'),
        'add_new_item' => __('Új Slider hozzáadása'),
        'new_item' => __('Új Slider'),
        'edit_item' => __('Slider szerkesztése'),
        'view_item' => __('Slider megtekintése'),
        'all_items' => __('Összes Slider'),
        'search_items' => __('Sliders keresése'),
        'not_found' => __('Nincs találat'),
        'not_found_in_trash' => __('Nincs találat a kukában'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'slider'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('slider', $args);
}
add_action('init', 'slider_post_type');

function create_slider_custom_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_slider_fields',
            'title' => 'Slider Mezők',
            'fields' => array(
                array(
                    'key' => 'field_slider_image',
                    'label' => 'Slider Kép',
                    'name' => 'sliderkep',
                    'type' => 'image',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_slider_title',
                    'label' => 'Cím',
                    'name' => 'cim',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_slider_text',
                    'label' => 'Szöveg',
                    'name' => 'szoveg',
                    'type' => 'textarea',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'slider',
                    ),
                ),
            ),
        ));
    }
}
add_action('acf/init', 'create_slider_custom_fields');


function activate_slider_plugin() {
    slider_post_type();
    create_slider_custom_fields();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_slider_plugin');

// Shortcode
function slider_shortcode() {
    $args = array('post_type' => 'slider');
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) {
        $output = '<div class="slider">';
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $output .= '<div class="slide" style="background-image: url(' . esc_url(get_field('sliderkep')) . ')">';
            $output .= '<div class="slide-content">';
            $output .= '<h2 class="cim">' . esc_html(get_field('cim')) . '</h2>';
            $output .= '<p class="szoveg">' . esc_html(get_field('szoveg')) . '</p>';
            $output .= '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
    } else {
        $output = 'Nincsenek slide-ok.';
    }
    wp_reset_postdata();
    return $output;
}
add_shortcode('slider', 'slider_shortcode');

// CSS , JS
function add_slick_slider() {
    wp_enqueue_style('slick-style', plugins_url('slick/slick.css', __FILE__));
    wp_enqueue_style('slick-theme-style', plugins_url('slick/slick-theme.css', __FILE__));
    wp_enqueue_style('slider-custom-style', plugins_url('css/slider.css', __FILE__));
    wp_enqueue_script('slick-script', plugins_url('slick/slick.min.js', __FILE__), array('jquery'));
    wp_add_inline_script('slick-script', '
        jQuery(document).ready(function(){
            jQuery(".slider").slick({
                dots: true,
                infinite: true,
                speed: 700,
                slidesToShow: 1,
                adaptiveHeight: true,
                autoplay: true,
                prevArrow: \'<button type="button" class="slick-prev">Előző</button>\',
                nextArrow: \'<button type="button" class="custom-next"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="83" viewBox="0 0 80 83" fill="none"><ellipse opacity="0.2" cx="40" cy="41.4646" rx="40" ry="40.6919" fill="white"/><path d="M60.3536 41.8174C60.5488 41.6222 60.5488 41.3056 60.3536 41.1103L57.1716 37.9283C56.9763 37.7331 56.6597 37.7331 56.4645 37.9283C56.2692 38.1236 56.2692 38.4402 56.4645 38.6354L59.2929 41.4639L56.4645 44.2923C56.2692 44.4876 56.2692 44.8041 56.4645 44.9994C56.6597 45.1947 56.9763 45.1947 57.1716 44.9994L60.3536 41.8174ZM19 41.9639L60 41.9639L60 40.9639L19 40.9639L19 41.9639Z" fill="#F0F0F1"/></svg></button>\',
                customPaging: function(slider, i) {
                    return \'<button type="button" class="dot">\' + (i + 1) + \'</button>\';
                },
            });
        });
    ');
}
add_action('wp_enqueue_scripts', 'add_slick_slider');
