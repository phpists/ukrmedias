$("#js_alert .close").click(function () {
    $("#js_alert").slideUp(function () {
        $(this).remove();
    });
});