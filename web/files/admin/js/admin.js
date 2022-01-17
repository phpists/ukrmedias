function createCpu(from, to) {
    String.prototype.createCpu = (function () {
        var L = {
            "А": "A", "а": "a", "Б": "B", "б": "b", "В": "V", "в": "v", "Г": "G", "г": "g",
            "Д": "D", "д": "d", "Е": "E", "е": "e", "Ё": "e", "ё": "e", "Ж": "Zh", "ж": "zh",
            "З": "Z", "з": "z", "И": "I", "и": "i", "Й": "Y", "й": "y", "К": "K", "к": "k",
            "Л": "L", "л": "l", "М": "M", "м": "m", "Н": "N", "н": "n", "О": "O", "о": "o",
            "П": "P", "п": "p", "Р": "R", "р": "r", "С": "S", "с": "s", "Т": "T", "т": "t",
            "У": "U", "у": "u", "Ф": "F", "ф": "f", "Х": "Kh", "х": "kh", "Ц": "Ts", "ц": "ts",
            "Ч": "Ch", "ч": "ch", "Ш": "Sh", "ш": "sh", "Щ": "Sch", "щ": "sch", "Ъ": "", "ъ": "",
            "Ы": "Y", "ы": "y", "Ь": "", "ь": "", "Э": "E", "э": "e", "Ю": "Yu", "ю": "yu",
            "Я": "Ya", "я": "ya", "І": "I", "і": "i", "Ї": "I", "ї": "i", "Є": "e", "є": "e"
        }, r = "", k;
        for (k in L) {
            r += k;
        }
        r = new RegExp("[" + r + "]", "g");
        k = function (a) {
            return a in L ? L[a] : "";
        };
        return function () {
            return this.replace(r, k)
                    .replace(/[^a-z0-9-]/gi, "-")
                    .replace(/-{2,}/g, "-")
                    .replace(/(^-{1,}|-{1,}$)/g, "")
                    .substring(0, 255).toLowerCase();
        };
    })();
    var cpu = $(from).val().createCpu();
    if (/^[^a-z]/.test(cpu)) {
        cpu = "a".concat(cpu);
    }
    $(to).val(cpu);
    return false;
}
function loadOptions(input, selector) {
    $.get(input.dataset.url, {value: input.value}, function (html) {
        $(selector).html(html);
    });
}
function loadOptionsAlt(input, url, selector, rel) {
    $.get(url, {id: input.value}, function (html) {
        $(selector).html(html).trigger("change");
        $(rel).html("");
    });
}
function getMenuState() {
    var menu = window.localStorage.getItem("menuA");
    if (menu === null) {
        menu = "{}";
    }
    menu = JSON.parse(menu);
    return menu;
}
function setMenuState(menu) {
    window.localStorage.setItem("menuA", JSON.stringify(menu));
}
function videoPreview(selectorVideo, value) {
    $(selectorVideo).html(value);
//        var r = new RegExp("embed/([a-z0-9\-_]+)\"", "i");
//        if (value.length === 0) {
//            $(selectorImage).html("");
//            return;
//        }
//        var match = r.exec(value), src;
//        if (match) {
//            src = "https://img.youtube.com/vi/" + match[1] + "/0.jpg";
//        } else {
//            src = "/images/default.jpg";
//        }
//        $(selectorImage).html($("<img>").attr("src", src));
}
$(function () {
    var csrf = {};
    csrf[$('meta[name="csrf-param"]').attr("content")] = $('meta[name="csrf-token"]').attr("content");
    $("#menu-toggle").click(function (e) {
        $("#wrapper").toggleClass("opened");
    });
    $('#menu-catalog,#menu-users,#menu-devices,#menu-system')
            .on('shown.bs.collapse', function () {
                var menu = getMenuState();
                menu[$(this).attr("id")] = true;
                setMenuState(menu);
            })
            .on('hide.bs.collapse', function () {
                var menu = getMenuState();
                delete menu[$(this).attr("id")];
                setMenuState(menu);
            });
    (function () {
        for (var key in getMenuState()) {
            $("#".concat(key)).prev().filter(".collapsed").trigger("click");
        }
    })();
    $(document)
            .on("click", 'div.alert .close', function () {
                $(this).closest("div.alert").slideUp(function () {
                    $(this).remove();
                });
            })
            .on("click", "a[data-confirm-message]", function (e) {
                e.preventDefault();
                var $link = $(this);
                var pjaxContainer = $(this).closest('[data-pjax-container]').attr("id");
                $('#delete-confirm').find('div.modal-body p').html($(this).data("confirm-message"));
                $('#delete-confirm').modal('show').find('.btn.confirm').unbind().one('click', function () {
                    $.ajax({
                        url: $link.attr("href"),
                        type: 'post',
                        error: function (xhr, status, error) {
                            alert('Помилка ajax-запиту: ' + xhr.responseText);
                        }
                    }).done(function (data) {
                        $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
                    });
                });
            })
            .on("click", "[data-confirm-click]", function (e) {
                e.preventDefault();
                var $btn = $(this);
                $('#delete-confirm').find('div.modal-body p').html($(this).data("confirm-click"));
                $('#delete-confirm').modal('show').find('.btn.confirm').unbind().one('click', function () {
                    if ($btn.is("a")) {
                        location.replace($btn.attr("href"));
                    } else {
                        $btn.closest("form").submit();
                    }
                });
            })
            .on("click", "[data-confirm-jsclick]", function (e) {
                e.preventDefault();
                var self = this;
                var $btn = $(this);
                $('#delete-confirm').find('div.modal-body p').html($(this).data("confirm-jsclick"));
                $('#delete-confirm').modal('show').find('.btn.confirm').unbind().one('click', function () {
                    $.ajax({
                        url: $btn.data("url"),
                        type: 'post',
                        error: function (xhr, status, error) {
                            alert('Помилка ajax-запиту: ' + xhr.responseText);
                        }
                    }).done(function (data) {
                        if (data === "1") {
                            window[$btn.data("callback")].apply(self);
                        }
                    });
                });
            })
            .on('click', 'a[data-pjax=1]', function (event) {
                event.preventDefault();
                var pjaxContainer = $(this).closest('[data-pjax-container]').attr("id");
                $.ajax({
                    url: $(this).attr("href"),
                    type: 'post',
                    error: function (xhr, status, error) {
                        alert('Помилка ajax-запиту: ' + xhr.responseText);
                    }
                }).done(function (data) {
                    $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
                });
            })
            .on("click", "[data-import-csv]", function () {
                var btn = this;
                var input = document.getElementById("csv-import-file");
                if (!input) {
                    input = document.createElement('input');
                    input.setAttribute("id", "csv-import-file");
                    input.setAttribute("type", "file");
                    input.setAttribute("hidden", true);
                    document.body.appendChild(input);
                }
                input.click();
                input.addEventListener("change", function () {
                    var fd = new FormData();
                    fd.append("file", input.files.item(0));
                    for (var key in csrf) {
                        fd.append(key, csrf[key]);
                    }
                    var request = new XMLHttpRequest();
                    request.open("POST", btn.dataset.url, true);
                    request.setRequestHeader("X-REQUESTED-WITH", "XMLHttpRequest");
                    request.onload = function () {
                        if (request.status == 200) {
                            var data = JSON.parse(request.response);
                            if (data.res === true) {
                                location.reload();
                            } else {
                                $('#alert-dialog').find('div.modal-body p').html(data.message);
                                $('#alert-dialog').modal('show');
                            }
                        }
                        input.remove();
                    };
                    request.send(fd);
                });
            })
            ;
    (function () {
        var $progressBar = $("#progress_bar");
        if ($progressBar.length === 0 || Number($progressBar.data("progress")) >= 100) {
            return;
        }
        var timer = setInterval(check, 3000);
        function check() {
            $.get($progressBar.data("url"), function (resp) {
                if (Number(resp) > 0) {
                    $progressBar.css("width", resp.concat("%"));
                    $progressBar.removeClass("bg-info").addClass("progress-bar-striped").html("");
                } else {
                    $progressBar.addClass("bg-info").removeClass("progress-bar-striped").html("очікується обробка...");
                }
                if (Number(resp) >= 100) {
                    location.reload();
                    clearInterval(timer);
                }
            });
        }
    })();
    $("#np_expressdoc-backwarddeliverydata-0-cargotype").trigger("change");
});
