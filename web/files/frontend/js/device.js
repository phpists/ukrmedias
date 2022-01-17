(function () {
    var level = 0;
    function stringify(data) {
        var i, res = "";
        level++;
        if (level > 50) {
            return res;
        }
        for (i in data) {
            if (i === "enabledPlugin") {
                continue;
            }
            if (typeof data[i] === "function") {
                res = res.concat(i, ";");
            } else if (typeof data[i] === "object") {
                res = res.concat(i, ":", stringify(data[i]), ";");
            } else {
                res = res.concat(i, ":", data[i], ";");
            }
        }
        return res;
    }
    var $form = $("form");
    $form.append($("<input name='device' type='hidden'>").val(stringify(window.navigator) + stringify(window.screen)));
    if ($form.hasClass("js_autosubmit")) {
        $form.submit();
    }
})();