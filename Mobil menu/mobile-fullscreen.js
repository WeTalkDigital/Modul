jQuery(document).ready(function($) {
    $('#mobile-fullscreen-button').click(function() {
        $('body').css('overflow', 'hidden');
        $('#mobile-fullscreen-modal').show();
    });

    $('#close-modal').click(function(e) {
        e.preventDefault();
        $('body').css('overflow', 'auto');
        $('#mobile-fullscreen-modal').hide();
    });

    $('#mobile-fullscreen-modal a.link').on('click', function() {
        $('body').css('overflow', 'auto');
        $('#mobile-fullscreen-modal').fadeOut();
    });
});