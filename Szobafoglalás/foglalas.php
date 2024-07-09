<?php
function szalloda_foglalas_shortcode() {
    // Bootstrap Datepicker script és stílusok
    wp_enqueue_style('bootstrap-datepicker-css', get_site_url() . '/wp-content/plugins/datum/css/bootstrap-datepicker.min.css');
    wp_enqueue_script('bootstrap-datepicker-js', get_site_url() . '/wp-content/plugins/datum/js/bootstrap-datepicker.min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-datepicker-hu', get_site_url() . '/wp-content/plugins/datum/locales/bootstrap-datepicker.hu.min.js', array('bootstrap-datepicker-js'), null, true);

    // Nyelvi beállítás
    $current_lang = pll_current_language();
    $texts = [
        'en' => [
            'arrival_date' => 'Arrival Date',
            'departure_date' => 'Departure Date',
            'request_offer' => 'Request an Offer',
            'select_dates_alert' => 'Please select an arrival and departure date!',
        ],
        'hu' => [
            'arrival_date' => 'Érkezési dátum',
            'departure_date' => 'Távozási dátum',
            'request_offer' => 'Kérjen ajánlatot',
            'select_dates_alert' => 'Kérjük, válasszon érkezési és távozási dátumot!',
        ],
        'de' => [
            'arrival_date' => 'Ankunftsdatum', 
            'departure_date' => 'Abreisedatum', 
            'request_offer' => 'Angebot anfordern', 
            'select_dates_alert' => 'Bitte wählen Sie ein Ankunfts- und Abreisedatum!',
        ],
    ];

    // Aktuális nyelv szövegeinek kiválasztása
    $lang_texts = $texts[$current_lang] ?? $texts['hu']; // Alapértelmezésben magyar, ha nincs megadva

    // Shortcode HTML kimenete
    ob_start();
    ?>
    <div class="szalloda-foglalas-form">
        <div class="datepicker-container">
            <div class="datepicker-field">
                <input type="text" class="datepicker-input rnw_arrive" placeholder="<?php echo esc_attr($lang_texts['arrival_date']); ?>">
            </div>
            <div class="datepicker-field">
                <input type="text" class="datepicker-input rnw_departure" placeholder="<?php echo esc_attr($lang_texts['departure_date']); ?>">
            </div>
        </div>
        <div class="foglalas-gomb-container">
            <div class="foglalas-gomb">
                <?php echo esc_html($lang_texts['request_offer']); ?>
                <svg class="szsvgfog" xmlns="http://www.w3.org/2000/svg" width="207" height="8" viewBox="0 0 207 8" fill="none">
                    <path d="M206.354 4.35355C206.549 4.15829 206.549 3.84171 206.354 3.64645L203.172 0.464466C202.976 0.269204 202.66 0.269204 202.464 0.464466C202.269 0.659728 202.269 0.976311 202.464 1.17157L205.293 4L202.464 6.82843C202.269 7.02369 202.269 7.34027 202.464 7.53553C202.66 7.7308 202.976 7.7308 203.172 7.53553L206.354 4.35355ZM0 4.5H206V3.5L0 3.5L0 4.5Z"></path>
                </svg>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.szalloda-foglalas-form .datepicker-input').datepicker({
                format: 'yyyy-mm-dd',
                language: '<?php echo esc_js($current_lang); ?>'
            });

            $('.szalloda-foglalas-form .foglalas-gomb').click(function() {
                var $form = $(this).closest('.szalloda-foglalas-form');
                var arrive = $form.find('.rnw_arrive').val();
                var departure = $form.find('.rnw_departure').val();
                if(arrive && departure) {
                    var baseUrl = '<?php 
                        if ($current_lang === "en") {
                            echo esc_url("https://melea.hu/en/booking/");
                        } elseif ($current_lang === "de") {
                            echo esc_url("https://melea.hu/de/reservierung/");
                        } else {
                            echo esc_url("https://melea.hu/foglalas/");
                        }
                    ?>';
                    var url = baseUrl + '?rnw_arrive=' + encodeURIComponent(arrive) + '&rnw_departure=' + encodeURIComponent(departure);
                    window.location.href = url;
                } else {
                    alert('<?php echo esc_js($lang_texts['select_dates_alert']); ?>');
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('szalloda_foglalas', 'szalloda_foglalas_shortcode');