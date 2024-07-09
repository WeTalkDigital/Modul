<?php
function custom_fixed_links() {
    // Linkek és szöveg - HU
    $bookingUrl = 'https://www.sevenrooms.com/reservations/flavakitchenandmore';
    $offersUrl = 'https://www.sevenrooms.com/experiences/flavakitchenandmore';
    $bookingText = 'Foglalás';
    $offersText = 'Ajánlatok';

    // Jelenlegi URL meghatározása
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Linkek és szöveg - EN
    if (strpos($current_url, '/en/') !== false) {
        $bookingUrl = 'https://www.sevenrooms.com/reservations/flavakitchenandmore';
        $offersUrl = 'https://www.sevenrooms.com/experiences/flavakitchenandmore';
        $bookingText = 'Booking';
        $offersText = 'Offers';
    }

    $links = array(
        'booking' => array(
            'url' => esc_url($bookingUrl),
            'text' => esc_html($bookingText)
        ),
        'offers' => array(
            'url' => esc_url($offersUrl),
            'text' => esc_html($offersText)
        )
    );

    $html = '<div class="custom-fixed-links">';
    foreach ($links as $link) {
        $html .= '<a href="' . $link['url'] . '" class="custom-link">' . $link['text'] . '</a> ';
    }
    $html .= '</div>';

    return $html;
}
add_shortcode('fixed_links', 'custom_fixed_links');

function flavamodul_enqueue_styles() {
    $css_path = plugins_url('fixgomb.css', __FILE__);
    $css_file_path = plugin_dir_path(__FILE__) . 'fixgomb.css';
    $version = filemtime($css_file_path);

    wp_register_style('flavamodul-fixgomb', $css_path, array(), $version);
    wp_enqueue_style('flavamodul-fixgomb');
}
add_action('wp_enqueue_scripts', 'flavamodul_enqueue_styles');
?>