<?php

function create_custom_post_type_and_taxonomy() {
    // Egyedi bejegyzés
    $labels = array(
        'name' => 'Accordion',
        'singular_name' => 'Accordion ',
        'menu_name' => 'Accordion',
        'name_admin_bar' => 'Accordion ',
        'add_new' => 'Új hozzáadása',
        'add_new_item' => 'Új Accordion hozzáadása',
        'new_item' => 'Új Accordion ',
        'edit_item' => 'Accordion szerkesztése',
        'view_item' => 'Accordion megtekintése',
        'all_items' => 'Összes Accordion ',
        'search_items' => 'Accordion keresése',
        'not_found' => 'Nincs találat',
        'not_found_in_trash' => 'Nincs találat a kukában',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'accordion-modszer'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('accordion-modszer', $args);

    // Taxonómia létrehozása
    $taxonomy_labels = array(
        'name' => 'Accordion Kategóriák',
        'singular_name' => 'Accordion Kategória',
        'search_items' => 'Accordion Kategóriák keresése',
        'all_items' => 'Összes Accordion Kategória',
        'parent_item' => 'Szülő Accordion Kategória',
        'parent_item_colon' => 'Szülő Accordion Kategória:',
        'edit_item' => 'Accordion Kategória szerkesztése',
        'update_item' => 'Accordion Kategória frissítése',
        'add_new_item' => 'Új Accordion Kategória hozzáadása',
        'new_item_name' => 'Új Accordion Kategória név',
        'menu_name' => 'Accordion Kategória',
    );

    $taxonomy_args = array(
        'labels' => $taxonomy_labels,
        'hierarchical' => true,
        'show_in_rest' => true,
    );

    register_taxonomy('modszer-kategoria', 'accordion-modszer', $taxonomy_args);
}
add_action('init', 'create_custom_post_type_and_taxonomy');

// Egyedi mezők létrehozása
function create_custom_fields() {
    if( function_exists('acf_add_local_field_group') ):
    
    acf_add_local_field_group(array(
        'key' => 'group_1',
        'title' => 'Accordion Mezők',
        'fields' => array(
            array(
                'key' => 'field_1',
                'label' => 'Accordion Szöveg',
                'name' => 'modszer_szoveg',
                'type' => 'wysiwyg',
            ),
            array(
                'key' => 'field_2',
                'label' => 'Szakértői Csapatunk',
                'name' => 'szakertoi_csapatunk',
                'type' => 'group',
                'sub_fields' => array(
                    array(
                        'key' => 'field_2a',
                        'label' => 'Titulus',
                        'name' => 'titulus',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_2b',
                        'label' => 'Szakértői Kép',
                        'name' => 'szakertoi_kep',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                ),
            ),
            array(
                'key' => 'field_3',
                'label' => 'Sorrend',
                'name' => 'sorrend',
                'type' => 'number',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'accordion-modszer',
                ),
            ),
        ),
    ));
    
    endif;
}
add_action('acf/init', 'create_custom_fields');


function activate_custom_plugin() {
    create_custom_post_type_and_taxonomy();
    create_custom_fields();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_custom_plugin');

// Shortcode
function custom_accordion_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '', 
    ), $atts, 'custom_accordion');

    $output = '<div class="custom-accordion">';

    $args = array(
        'post_type' => 'accordion-modszer',
        'tax_query' => array(
            array(
                'taxonomy' => 'modszer-kategoria',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($atts['category']),
            ),
        ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'sorrend',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => 'sorrend',
                'compare' => 'NOT EXISTS',
            ),
        ),
        'orderby' => array(
            'meta_value_num' => 'ASC',
            'date' => 'DESC',
        ),
    );
    $query = new WP_Query($args);

    $script_ver = filemtime(plugin_dir_path(__FILE__) . 'js/accordion.js');
    $style_ver = filemtime(plugin_dir_path(__FILE__) . 'css/accordion.css');
    wp_enqueue_script('meletabszoba_script', plugin_dir_url(__FILE__) . 'js/accordion.js', array('jquery'), $script_ver, true);
    wp_enqueue_style('meletabszoba_style', plugin_dir_url(__FILE__) . 'css/accordion.css', array(), $style_ver);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $modszer_szoveg = get_field('modszer_szoveg', $post_id);
            $szakertoi_csapatunk = get_field('szakertoi_csapatunk', $post_id);
            $titulus = '';
            if (isset($szakertoi_csapatunk['titulus']) && !empty($szakertoi_csapatunk['titulus'])) {
                $titulus = ' - <span class="egyedi-class">' . esc_html($szakertoi_csapatunk['titulus']) . '</span>';
            }
            $szakertoi_kep = '';
            if (isset($szakertoi_csapatunk['szakertoi_kep']) && !empty($szakertoi_csapatunk['szakertoi_kep'])) {
                $szakertoi_kep_src = wp_get_attachment_image_src($szakertoi_csapatunk['szakertoi_kep']['id'], 'full');
                if ($szakertoi_kep_src) {
                    $szakertoi_kep = '<img src="' . esc_url($szakertoi_kep_src[0]) . '" alt="' . esc_attr($szakertoi_csapatunk['szakertoi_kep']['alt']) . '">';
                }
            }

            $output .= '<div class="accordion-item">';
            $output .= '<div class="accordion-title">';
            $output .= '<div class="title-text">' . get_the_title() . $titulus . '</div>';
            $output .= '<div class="title-icon"><svg class="svgtabikon" xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14" fill="none">
            <line x1="7.98682" y1="0" x2="7.98682" y2="14" stroke="#BE7C4A" stroke-width="2"/>
            <line x1="0.986816" y1="7" x2="14.9868" y2="7" stroke="#BE7C4A" stroke-width="2"/>
            </svg></div>';
            $output .= '</div>';
            $output .= '<div class="accordion-content">';
            if (!empty($szakertoi_kep)) {
                $output .= '<div class="accordion-content-flex">';
                $output .= '<div class="accordion-content-image">' . $szakertoi_kep . '</div>';
                $output .= '<div class="accordion-content-text">' . wp_kses_post($modszer_szoveg) . '</div>';
                $output .= '</div>';
            } else {
                $output .= wp_kses_post($modszer_szoveg);
            }
            $output .= '</div>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<div class="no-posts">Nincsenek bejegyzések</div>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('custom_accordion', 'custom_accordion_shortcode');

?>