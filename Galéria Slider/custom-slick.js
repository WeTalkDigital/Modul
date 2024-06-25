jQuery(document).ready(function($) {
    var $carousel = $(".galeria-slider");

    // Slick Slider beállítások 
    $carousel.slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        centerMode: true,
        slidesToScroll: 1,
        arrows: false,
        dotsClass: "slick-dots dot"
    });


    function adjustSlideHeights() {
        $carousel.find('.slick-slide').height('640px');
        $carousel.find('.slick-current').height('600px');
    }
    $carousel.on('init afterChange', function(event, slick) {
        adjustSlideHeights();
    });
    adjustSlideHeights();
});
