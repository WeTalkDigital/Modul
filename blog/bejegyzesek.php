<?php

function custom_posts_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 5,
            'length' => 100,
            'category' => '', // Add category attribute
        ), 
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'category_name' => sanitize_text_field($atts['category']), // Use category attribute
        'lang' => pll_current_language(), // Polylang current language
    );

    $posts = get_posts($args);

    $translations = array(
        'read_more' => array(
            'en' => 'Read More',
            'hu' => 'Tovább',
            // Add more languages here
        ),
    );

    $output = '<div class="custom-posts">';

    foreach($posts as $post) {
        setup_postdata($post);

        $output .= '<div class="post">'; 
        $output .= '<a href="' . esc_url(get_the_permalink($post)) . '" class="post-link">';
        $output .= '<div class="post-inner">';
        $output .= '<div class="post-image" style="background-image: url(' . esc_url(get_the_post_thumbnail_url($post)) . ')"></div>';
        $output .= '<div class="post-content">';
        $output .= '<h2>' . esc_html(get_the_title($post)) . '</h2>';
        $output .= '<p>' . esc_html(wp_trim_words(get_the_content($post), intval($atts['length']))) . '</p>';
        $output .= '<span>' . esc_html($translations['read_more'][pll_current_language()]) . '</span>';
        $output .= '</div></div>';
        $output .= '</a>';
        $output .= '</div>'; 
    }

    wp_reset_postdata();

    $output .= '</div>';

    return $output;
}
add_shortcode('custom_posts', 'custom_posts_shortcode');

function enqueue_custom_posts_styles() {
    global $post;

    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'custom_posts')) {
        $css_path = plugin_dir_path(__FILE__) . 'bejegyzes.css';
        wp_enqueue_style('custom-posts-style', plugin_dir_url(__FILE__) . 'bejegyzes.css', array(), filemtime($css_path));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_posts_styles');
?>