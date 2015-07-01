jQuery(function ($) {
    $('.ppb-panels-dismiss').click(function (e) {
        e.preventDefault();
        var $$ = $(this);
        $.get($$.attr('href'));
        $$.closest('.updated, .error').slideUp(function () {
            $(this).remove();
        });
    });
});