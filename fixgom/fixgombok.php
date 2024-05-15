<?php
function custom_fixed_links() {
    // Az aktuális URL lekérdezése
    $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Alapértelmezett linkek és szövegek (magyar)
    $bookingUrl = 'https://www.sevenrooms.com/reservations/flavakitchenandmore';
    $offersUrl = 'https://www.sevenrooms.com/experiences/flavakitchenandmore';
    $bookingText = 'Foglalás';
    $offersText = 'Ajánlatok';

    // Angol fordítások és linkek, ha az URL tartalmazza az 'en' slugot
    if (strpos($current_url, '/en/') !== false) {
        $bookingUrl = 'https://www.sevenrooms.com/reservations/flavakitchenandmore';
        $offersUrl = 'https://www.sevenrooms.com/experiences/flavakitchenandmore';
        $bookingText = 'Booking';
        $offersText = 'Offers';
    }

    // Linkek és szövegek definiálása
    $links = array(
        'booking' => array(
            'url' => $bookingUrl,
            'text' => $bookingText
        ),
        'offers' => array(
            'url' => $offersUrl,
            'text' => $offersText
        )
    );

    // HTML kód generálása
    $html = '<div class="custom-fixed-links">';
    foreach ($links as $link) {
        $html .= '<a href="'. $link['url'] .'" class="custom-link">'. $link['text'] .'</a> ';
    }
    $html .= '</div>';

    return $html;
}
add_shortcode('fixed_links', 'custom_fixed_links');



function flavamodul_enqueue_styles() {
    $css_path = plugins_url('fixgomb.css', __FILE__);
    $css_file_path = plugin_dir_path(__FILE__) . 'fixgomb.css';
    $version = filemtime($css_file_path); // A fájl utolsó módosításának időpontja

    wp_register_style('flavamodul-fixgomb', $css_path, array(), $version);
    wp_enqueue_style('flavamodul-fixgomb');
}
add_action('wp_enqueue_scripts', 'flavamodul_enqueue_styles');
