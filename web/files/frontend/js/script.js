function loadOptionsAlt(input, url, selector, rel) {
    $.get(url, {id: input.value}, function (html) {
        $(selector).html(html).trigger("change");
        $(rel).html("");
    });
}

function uploadFile(btn) {
    var $btn = $(btn);
    var $indicator = $btn.next();
    var $input = $("<input type='file' style='display:none;'/>").on("change", sendFile);
    $("body").append($input);
    $input.trigger("click");

    function sendFile() {
        $btn.toggle();
        $indicator.toggleClass("dn");
        var fd = new FormData();
        fd.append("file", $input.get(0).files[0]);
        fd.append($('meta[name="csrf-param"]').attr("content"), $('meta[name="csrf-token"]').attr("content"));
        $.ajax({
            method: "post",
            url: $btn.data("url"),
            dataType: "json",
            data: fd,
            processData: false,
            contentType: false,
            complete: function (resp) {
                var data = resp.responseJSON;
                if (data && data.res === true) {
                    location.href = data.url;
                } else {
                    $btn.toggle();
                    $indicator.toggleClass("dn");
                }
            }
        });
    }
}

function testWebP(callback) {
    var webP = new Image();
    webP.onload = webP.onerror = function () {
        callback(webP.height == 2);
    };
    webP.src = "data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA";
}

testWebP(function (support) {
    if (support == true) {
        document.querySelector('body').classList.add('webp');
    } else {
        document.querySelector('body').classList.add('no-webp');
    }
});
var CSRF = {};
CSRF[$('meta[name="csrf-param"]').attr("content")] = $('meta[name="csrf-token"]').attr("content");
var btn_up = $('#btn_up'), scrollPrev = 0;

$(window).scroll(function () {
    var scrolled = $(window).scrollTop();
    if (scrolled > 500) {
        btn_up.css('display', 'flex');
    } else {
        btn_up.css('display', 'none');
    }
    scrollPrev = scrolled;
});
$("input.js-focus-on-load").focus();
$("#js_download").click(function () {
    var $modal = $('#modal-download').removeClass('opac0').addClass('opac1');
    $modal.find("a").unbind().bind("click", function (e) {
        e.preventDefault();
        var $footer = $modal.find("div.footer").hide();
        var $indicator = $("#js_upload_indicator").show();
        $.post($(this).attr("href"), $(this).closest("form").serialize(), function (url) {
            if (/download/.test(url)) {
                $modal.removeClass('opac1').addClass('opac0');
            }
            $footer.show();
            $indicator.hide();
            location.href = url;
        });
    });
});
$("#js_download_with_image").click(function () {
    var $modal = $('#modal-download-with-image').removeClass('opac0').addClass('opac1');
    $modal.find("a").unbind().bind("click", function (e) {
        e.preventDefault();
        var $footer = $modal.find("div.footer").hide();
        var $indicator = $("#js_upload_indicator_photo").show();
        $.post($(this).attr("href"), $(this).closest("form").serialize(), function (url) {
            if (/download/.test(url)) {
                $modal.removeClass('opac1').addClass('opac0');
            }
            $footer.show();
            $indicator.hide();
            location.href = url;
        });
    });
});
$(document)
    .on("dblclick", 'div.alert', function () {
        if ($(this).hasClass("skip-click")) {
            return;
        }
        $(this).slideUp(function () {
            $(this).remove();
        });
    })
    .on("click", "[data-confirm-click]", function (e) {
        e.preventDefault();
        var $link = $(this);
        $("#delete-confirm").find("div.text").html($(this).data("confirm-click"));
        $("#delete-confirm").removeClass("opac0").addClass("opac1").find(".confirm").unbind().one("click", function () {
            $.ajax({
                url: $link.attr("href"),
                type: "post",
                data: CSRF,
                success: function (resp) {
                    location.href = resp;
                },
                error: function (xhr, status, error) {
                    alert("Помилка ajax-запиту: " + xhr.responseText);
                }
            });
        });
        $("#delete-confirm").find(".close").unbind().one("click", function () {
            $("#delete-confirm").removeClass('opac1').addClass('opac0');
        });
    })
    .on("change", "form.js_ajax_form", function (event) {
        var $form = $(this);
        var divSelector = $form.data("update");
        $.ajax({
            url: $form.attr("action"),
            type: $form.attr("method"),
            dataType: "html",
            data: $form.serialize(),
            success: function (html) {
                if (divSelector) {
                    var div = $(html).find(divSelector);
                    $(divSelector).html(div);
                } else {
                    $.pjax.reload({
                        container: '#' + $form.closest("[data-pjax-container]").attr("id"),
                        url: $form.data("reloadurl"),
                        timeout: 3000,
                        history: false
                    });
                }
            }
        });
    })
;

var $page = $('html, body');
$('a[href*="#header"]').click(function () {
    $page.animate({
        scrollTop: $($.attr(this, 'href')).offset().top
    }, 400);
    return false;
});

// Каталог меню

$("body")
    .on("click", "div.modal svg.close", function () {
        $(this).closest("div.opac1").removeClass('opac1').addClass('opac0');
    })
    //        .on("click", "div.level_data_2>div.cat_title", function () {
    //            var $title = $(this).show();
    //            var $div = $title.closest("div.level_data_2").show();
    //            var $subMenu = $div.find(">div.children").show();
    //            $div.closest("div.line_2_mob").find("div.level_data_2").not($div).hide();
    //            $div.closest("div.line_2_mob").find("div.level_data_2 div.children").not($subMenu).hide();
    //        })
    .on("click", "#line_2_mob div.js_level_data div.cat_title", function (e) {
        var $title = $(this).show();
        if ($title.next("div.children").length > 0) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            return;
        }
        var $div = $title.closest("div.js_level_data").show();
        var $subMenu = $div.find(">div.children").show();
        $div.parent().find(">div.js_level_data").not($div).hide();
        $div.parent().find(">div.js_level_data>div.children").not($subMenu).hide();
    })
;
$("#catalog_menu_btn").click(function () {
    $("#line_2_mob").slideToggle();
});

$("#line_2_mob div.to-back-btn").click(function () {
    var $div = $(this).closest("div.children").hide().closest("div.js_level_data").parent();
    $div.find(">div.js_level_data").show();
});
$('#line_2_mob svg.catalog_menu_close').click(function () {
    $('#line_2_mob').hide();
});
$('#profile_btn').click(function () {
    $('#profile').removeClass('opac0').addClass('opac1');
});


jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var profile = $("#profile");
        var profile_block = $("#profile .profile_block"); // тут указываем ID элемента
        if (!profile_block.is(e.target) // если клик был не по нашему блоку
            && profile_block.has(e.target).length === 0) { // и не по его дочерним элементам
            profile.removeClass('opac1').addClass('opac0'); // скрываем его
        }
    });
});

// search

$('#search_btn').click(function () {
    $('#search').removeClass('opac0').addClass('opac1');
});

$('#search_close').click(function () {
    $('#search').removeClass('opac1').addClass('opac0');
});

jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var search = $("#search");
        var search_block = $("#search .search_block"); // тут указываем ID элемента
        if (!search_block.is(e.target) // если клик был не по нашему блоку
            && search_block.has(e.target).length === 0) { // и не по его дочерним элементам
            search.removeClass('opac1').addClass('opac0'); // скрываем его
        }
    });
});

/// directory_woman_socks
//$('#directory_woman_socks_btn').click(function () {
//    $('#directory_woman_socks').removeClass('opac0').addClass('opac1');
//});
//
//$('#directory_woman_socks_close').click(function () {
//    $('#directory_woman_socks').removeClass('opac1').addClass('opac0');
//});
//
//jQuery(function ($) {
//    $(document).mouseup(function (e) { // событие клика по веб-документу
//        var directory_woman_socks = $("#directory_woman_socks");
//        var directory_woman_socks_block = $("#directory_woman_socks .directory_woman_socks"); // тут указываем ID элемента
//        if (!directory_woman_socks_block.is(e.target) // если клик был не по нашему блоку
//                && directory_woman_socks_block.has(e.target).length === 0) { // и не по его дочерним элементам
//            directory_woman_socks.removeClass('opac1').addClass('opac0'); // скрываем его
//        }
//    });
//});

$('#category .show .quantity').click(function () {
    $('#quantity_choise').removeClass('transY0').addClass('transY1');
});

jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var quantity = $("#category .show");
        var quantity_block = $("#quantity_choise"); // тут указываем ID элемента
        if (!quantity.is(e.target) // если клик был не по нашему блоку
            && quantity_block.has(e.target).length === 0) { // и не по его дочерним элементам
            quantity_block.removeClass('transY1').addClass('transY0'); // скрываем его
        }
    });
});

$('#quantity_choise label').click(function () {
    $('#quantity_choise').removeClass('transY1').addClass('transY0');
    $("#category .show .quantity span").html($(this).text());
});

$('#sort').click(function () {
    $('#category .sort div').removeClass('transY0').addClass('transY1');
});

jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var sort = $("#category .sort");
        var sort_block = $("#category .sort div"); // тут указываем ID элемента
        if (!sort.is(e.target) // если клик был не по нашему блоку
            && sort_block.has(e.target).length === 0) { // и не по его дочерним элементам
            sort_block.removeClass('transY1').addClass('transY0'); // скрываем его
        }
    });
});

$('#category .sort div label').click(function (e) {
    e.stopPropagation();
    $(this).parent().removeClass('transY1').addClass('transY0');
    $("#sort .sort-title").html($(this).text());
});

$('#formation1').click(function () {
    $('#formation1 svg').css('fill', '#2F4858');
    $('#formation2 svg').css('fill', '#A4A9B3');
    $("#category .goods .cont").removeClass("view-lines");
});

$('#formation2').click(function () {
    $('#formation2 svg').css('fill', '#2F4858');
    $('#formation1 svg').css('fill', '#A4A9B3');
    $("#category .goods .cont").addClass("view-lines");
});

//$('#category .options div').click(function () {
//    $(this).css('display', 'none');
//});
//
//$('#category .filters .drop .submit').click(function () {
//    $('#category .options').css('display', 'grid');
//    $('#category .nav').css('grid-template-columns', '135px 1fr max-content max-content');
//    $('#category .show').css('grid-row', '4/5').css('justify-self', 'start');
//    $('#category .sort').css('grid-row', '4/5');
//    $('#formation1').css('grid-row', '4/5');
//    $('#formation2').css('grid-row', '4/5');
//    $('#formation2').css('grid-row', '4/5');
//    $('#category .locality').css('grid-column', '1/6');
//});
function GoodsFilterInit() {
    GoodsFilterBefore();
}

function GoodsFilterBefore() {
    var data = {};
    var $options = $("#js_goods_filter_form div.options");
    var tpl = $("#js_filter_options_item_tpl").html(), $param, key, self, title, id, $label, $values;
    $("#js_goods_filter_form").find("div.filters input").each(function () {
        $param = $(this).closest("div.item").find("p.js_param_title");
        title = $param.text().trim();
        self = $param.hasClass("js_self_value");
        key = title.concat(self);
        if (!data.hasOwnProperty(key)) {
            data[key] = {title: title, values: null, self: self};
        }
        if ($(this).is(":checked")) {
            if (data[key].values === null) {
                data[key].values = {};
            }
            data[key].values[$(this).attr("id")] = $(this).closest("label").text().trim();
        }
    });
    $options.html("");
    for (key in data) {
        if (data[key].values === null) {
            continue;
        }
        var $el = $(tpl);
        $values = $el.find("span.js_p_values");
        if (data[key].self === false) {
            //$el.find("span.js_p_title").text(data[key].title.concat(": "));
        }
        for (id in data[key].values) {
            $label = $("<label>").attr("for", id).text(data[key].values[id]);
            $values.append($label);
        }
        $options.append($el);
    }
}

$('#page_choise div').click(function () {
    $('#page_choise div').removeClass('col_bdr').addClass('col_blc');
    $(this).removeClass('col_blc').addClass('col_bdr');
});

//$('#show_filters_btn').click(function () {
//    $('#show_filters').removeClass('transY0').addClass('transY1');
//});
//
//$('#show_filters_close').click(function () {
//    $('#show_filters').removeClass('transY1').addClass('transY0');
//});
//
//$('#js_filters_list div.js_filter_param_title').click(function () {
//    var selector = $(this).data("id");
//    $('#show_filters').removeClass('transY1').addClass('transY0');
//    $(selector).removeClass('transY0').addClass('transY1');
//});
//
//$('#mob_filter svg.show_options_close').click(function () {
//    $(this).closest('div.show_filters').removeClass('transY1').addClass('transY0');
//});
//
//$('#mob_filter .head div, #mob_filter div.btn2, #mob_filter div.btn1').click(function () {
//    $('#show_filters').removeClass('transY0').addClass('transY1');
//    $(this).closest("div.show_filters").removeClass('transY1').addClass('transY0');
//});
//
//$('#mob_filter div.btn1').click(function () {
//    $('#show_filters').removeClass('transY0').addClass('transY1');
//    $('#mob_filter div.show_options').removeClass('transY1').addClass('transY0');
//    // $('#show_filters .body .filter_delete').css('display', 'grid').css('grid-template-columns', 'repeat(3, max-content )');
//});

// product

$('#product .small_pic').click(function () {
    var attr = $(this).find("img").data('big');
    $('#product .big img').attr('src', attr);
});

$('#product .content .info .description').click(function () {
    $('#product .content .info .description .text').slideToggle(200);
});

$('#product .content .info .description').click(function () {
    if ($(this).children('svg').hasClass('rotate0')) {
        $(this).children('svg').removeClass('rotate0').addClass('rotate180');
    } else {
        $(this).children('svg').removeClass('rotate180').addClass('rotate0');
    }
});

$('div.box_piece label input').click(function () {
    var $label = $(this).parent();
    if ($label.hasClass("on")) {
        return;
    }
    $(this).closest("div.box_piece").find("label").toggleClass("on");
});

$("#order_block div.js_btn,#ordering div.js_btn").click(function () {
    var $form = $(this).closest("form");
    var $div = $(this).closest("div.item");
    var $input = $div.find("input.number");
    var $inputType = $form.find("div.box_piece.js_general:visible input:checked");
    if ($inputType.length === 0) {
        $inputType = $div.find("div.box_piece.js_item input:checked");
    }
    var maxQty = Number($input.data("qty_max"));
    var qty = Number($(this).data("qty"));
    if ($inputType.val() === "1") {
        qty = qty * $input.data("qty_pack");
    }
    var currentQty = Number($input.val());
    var resultQty = currentQty + qty;
    if (qty > 0 && resultQty > maxQty) {
        return;
    }
    if (resultQty <= 0) {
        resultQty = 0;
    }
    $input.val(resultQty).trigger("change");
});

$("#order_block input.number, #ordering input.number").change(function () {
    var totalItems = 0, totalWeight = 0, totalVolume = 0, total = 0, $div, weight, volume, price, qty, amount,
        goodsQty = 0;
    $(this).closest("section").find("div.js_goods").each(function () {
        $div = $(this);
        weight = Number($div.data("weight"));
        volume = Number($div.data("volume"));
        price = Number($div.find(".js_price").text());
        qty = Number($div.find("input.number").val());
        amount = price * qty;
        $div.find(".js_amount").text(amount.toFixed(2));
        if (qty > 0) {
            totalItems++;
        }
        goodsQty += qty;
        totalWeight += qty * weight;
        totalVolume += qty * volume;
        total += amount;
    });
    $("#js_goods_qty").text(goodsQty);
    $("#js_total_items").text(totalItems);
    $("#js_total_weight").text(totalWeight.toFixed(2));
    $("#js_total_volume").text(totalVolume.toFixed(6));
    $("#js_total_alt,#js_grand_gotal").text(total.toFixed(2));
    var $divTotal = $("#js_total").text(total.toFixed(2));
    var minAmount = Number($divTotal.data("minamount"));
    var $min = $("#js_to_minimum");
    if ($min.length === 1) {
        $min.text(minAmount > total ? (minAmount - total).toFixed(2) : 0);
    }
    addToCart($(this));
});
$("#order_block").unbind().bind("submit", function (e) {
    e.preventDefault();
    e.stopPropagation();
});

function addToCart($item) {
    var $form = $item.closest("form");
    $form.find("a.to_order").addClass("active");
    $.ajax({
        url: "/client/cart/add",
        data: $form.serialize(),
        type: "post",
        dataType: "json",
        success: function (resp) {
            if (resp.qty > 0) {
                $("#cart_icon").addClass("active");
            } else {
                $("#cart_icon").removeClass("active");

            }
            $("#cart_summary_amount").html(resp.amount);
            $("#cart_summary_info").html(resp.info);
        }
    });
}

$('#header .tels_choise svg').click(function () {
    if ($('#header .tels_choise .drop').hasClass('transY0')) {
        $('#header .tels_choise .drop').removeClass('transY0').addClass('transY1');
    } else {
        $('#header .tels_choise .drop').removeClass('transY1').addClass('transY0');
    }

});

jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var tels_choise = $("#header .tels_choise svg");
        var tels_choise_block = $("#header .tels_choise .drop"); // тут указываем ID элемента
        if (!tels_choise.is(e.target) // если клик был не по нашему блоку
            && tels_choise_block.has(e.target).length === 0) { // и не по его дочерним элементам
            tels_choise_block.removeClass('transY1').addClass('transY0'); // скрываем его
        }
    });
});

$('#header .tels_choise .drop span:nth-child(1)').click(function () {
    $('#header .tels_choise a').attr('href', 'tel:+380962788052').html('+38 096 278 80 52');
});

$('#header .tels_choise .drop span:nth-child(2)').click(function () {
    $('#header .tels_choise a').attr('href', 'tel:+380638762019').html('+38 063 876 20 19');
});

$('#header .tels_choise .drop span:nth-child(3)').click(function () {
    $('#header .tels_choise a').attr('href', 'tel:+380661287571').html('+38 066 128 75 71');
});
$("#delivery div.variants input").click(function () {
    var id = $(this).val();
    var $div = $("#delivery div.description");
    var $item = $div.find(".js_delivery_".concat(id)).slideDown();
    var $items = $div.find(".js_delivery");
    if ($item.length === 0) {
        $item = $div.find("div.js_delivery_all").slideDown();
    }
    $items.not($item).slideUp();
});
//$("#preorders-address_id").change(function () {
//    $.ajax({
//        url: "/client/data/address",
//        type: "get",
//        data: {id: $(this).val()},
//        dataType: "json",
//        success: function (resp) {
//            for (var attr in resp) {
//                $("#preorders-".concat(attr)).val(resp[attr]);
//            }
//        }
//    });
//});
//$('#ordering .warehouse .item .close').click(function () {
//    $.post($(this).attr("action"), $(this).serialize(), function () {
//        $("#order_block input.number").val(0).first().trigger("change");
//    });
//});
//
//$('#payment .cont .variants label:nth-child(1)').click(function () {
//    $('#payment .cont .description .variant').removeClass('db').addClass('dn');
//    $('#payment .cont .description .variant:nth-child(1)').removeClass('dn').addClass('db');
//});
//
//$('#payment .cont .variants label:nth-child(2)').click(function () {
//    $('#payment .cont .description .variant').removeClass('db').addClass('dn');
//    $('#payment .cont .description .variant:nth-child(2)').removeClass('dn').addClass('db');
//});
//
//$('#payment .cont .variants label:nth-child(3)').click(function () {
//    $('#payment .cont .description .variant').removeClass('db').addClass('dn');
//    $('#payment .cont .description .variant:nth-child(3)').removeClass('dn').addClass('db');
//});
//
//$('#delivery .cont .description .variant .city_choise .city_name').click(function () {
//    $(this).siblings('.cities').css('visibility', 'visible').css('transform', 'scale(1)').css('opacity', '1');
//});
//
//$('#delivery .cont .description .variant .city_choise .cities span').click(function () {
//    var city = $(this).html();
//    $(this).parent('.cities').siblings('.city_name').children('span').html(city);
//    $(this).siblings('span').removeClass('Selected').addClass('notSelected');
//    $(this).removeClass('notSelected').addClass('Selected');
//});
//
//jQuery(function ($) {
//    $(document).mouseup(function (e) { // событие клика по веб-документу
//        var cities = $("#delivery .cont .description .variant .city_choise .cities");
//        var cities_block = $("#delivery .cont .description .variant .city_choise .cities span"); // тут указываем ID элемента
//        if (!cities_block.is(e.target) // если клик был не по нашему блоку
//                && cities_block.has(e.target).length === 0) { // и не по его дочерним элементам
//            cities.css('visibility', 'hidden').css('transform', 'scale(0.7)').css('opacity', '0'); // скрываем его
//        }
//    });
//});
//
//$(document).keydown(function (event) {
//    if (event.keyCode == 27) {
//        $('#delivery .cont .description .variant .city_choise .cities').css('visibility', 'hidden').css('transform', 'scale(0.7)').css('opacity', '0');
//    }
//});
//
//$('#delivery .cont .variants label:nth-child(1)').click(function () {
//    $('#delivery .cont .description .variant').removeClass('db').addClass('dn');
//    $('#delivery .cont .description .variant:nth-child(1)').removeClass('dn').addClass('db');
//});
//
//$('#delivery .cont .variants label:nth-child(2)').click(function () {
//    $('#delivery .cont .description .variant').removeClass('db').addClass('dn');
//    $('#delivery .cont .description .variant:nth-child(2)').removeClass('dn').addClass('db');
//});
//
//$('#delivery .cont .variants label:nth-child(3)').click(function () {
//    $('#delivery .cont .description .variant').removeClass('db').addClass('dn');
//    $('#delivery .cont .description .variant:nth-child(3)').removeClass('dn').addClass('db');
//});

//$('#confirm_btn').click(function () {
//    $('#confirm_window').removeClass('opac0').addClass('opac1');
//});

jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var confirm_window = $("#confirm_window");
        var confirm_window_block = $("#confirm_window .confirm_window"); // тут указываем ID элемента
        if (!confirm_window_block.is(e.target) // если клик был не по нашему блоку
            && confirm_window_block.has(e.target).length === 0) { // и не по его дочерним элементам
            confirm_window.removeClass('opac1').addClass('opac0'); // скрываем его
        }
    });
});

$('#orders .cont .table .filters .unit').click(function () {
    $('#orders .cont .table .filters .unit').removeClass('selected');
    $(this).addClass('selected');
});

$('#orders .cont .table .page_selection .page').click(function () {
    $('#orders .cont .table .page_selection .page').css('color', '#18262F');
    $(this).not('#orders .cont .table .page_selection .page:nth-child(5)').css('color', '#EA2227');
});

$('#orders .cont .table .filters_sm .sort').click(function () {
    $('#orders .cont .table .filters_sm .sort div').slideToggle(300);
});

$('#orders .cont .table .filters_sm .sort div span').click(function () {
    var sort = $(this).html();
    $(this).parent('div').siblings('span').html(sort);
});

$('#orders .repeat').click(function () {
    var url = $(this).data("url");
    var $div = $('#reorder').removeClass('opac0').addClass('opac1');
    $div.find("a.js-add").attr("href", url);
    $div.find("a.js-replace").attr("href", url.concat("&clean=1"));
});

$('#reorder .reorder svg').click(function () {
    $('#reorder').removeClass('opac1').addClass('opac0');
});

$('#orders .cont .table .brands_choise .brands .item').click(function () {
    $(this).siblings('.item').removeClass('Selected').addClass('notSelected');
    $(this).removeClass('notSelected').addClass('Selected');
});

//$('#orders .cont .table .profile .item .checkbox input').click(function () {
//    if ($(this).hasClass('checked')) {
//        $("#orders .cont .table .profile .saved").removeClass('transX0').addClass('transX1');
//        setTimeout(function () {
//            $("#orders .cont .table .profile .saved").removeClass('transX1').addClass('transX0');
//        }, 3000);
//        $(this).removeClass('checked');
//    } else {
//        $('#orders .cont .table .profile .saved').removeClass('transX1').addClass('transX0');
//        $(this).addClass('checked');
//    }
//
//});

$('#orders .cont .table .staff_sort .unit').click(function () {
    $('#orders .cont .table .staff_sort .unit').removeClass('selected');
    $(this).addClass('selected');
});

$('#orders .cont .table .staff .person .btns a:nth-child(2), #orders .cont .table .profile .item .delete_btn').click(function () {
    $('#staff_delete').removeClass('opac0').addClass('opac1');
});

$('#staff_delete .staff_delete .buttons div').click(function () {
    $('#staff_delete').removeClass('opac1').addClass('opac0');
});

$('#choose_delivery_method label:nth-child(2)').click(function () {
    $('#choose_delivery_method .variant1').removeClass('dn').addClass('db').removeClass('vh');
    $('#choose_delivery_method .variant2').removeClass('db').addClass('dn');
});

$('#choose_delivery_method label:nth-child(3)').click(function () {
    $('#choose_delivery_method .variant1').removeClass('db').addClass('dn');
    $('#choose_delivery_method .variant2').removeClass('dn').addClass('db');
});

$('#choose_delivery_method label:nth-child(4)').click(function () {
    $('#choose_delivery_method .variant1').removeClass('dn').addClass('db').addClass('vh');
    $('#choose_delivery_method .variant2').removeClass('db').addClass('dn');
});

$('#orders .cont .table .profile .item .choose_delivery_place span:nth-child(2)').click(function () {
    $(this).removeClass('notselected').addClass('selected');
    $(this).siblings('span').removeClass('selected').addClass('notselected');
    $(this).parent('.choose_delivery_place').next('input').css('display', 'block');
    $(this).parent('.choose_delivery_place').next('input').next('input').css('display', 'none');
});

$('#orders .cont .table .profile .item .choose_delivery_place span:nth-child(1)').click(function () {
    $(this).removeClass('notselected').addClass('selected');
    $(this).siblings('span').removeClass('selected').addClass('notselected');
    $(this).parent('.choose_delivery_place').next('input').css('display', 'none');
    $(this).parent('.choose_delivery_place').next('input').next('input').css('display', 'block');
});

$('#show_params').click(function () {
    $('#drop_filters').slideToggle(300);
    if ($('#show_params p').hasClass('rolledUp')) {
        $('#show_params p').html('Показати параметри').removeClass('rolledUp');
        $('#show_params svg').css('transform', 'rotate(180deg)');
    } else {
        $('#show_params p').html('Скрити параметри').addClass('rolledUp');
        $('#show_params svg').css('transform', 'rotate(0deg)');
    }
});

function GoodsPage_photoVariantClick(selector) {
    $(selector.concat(" img")).trigger("click");
}

//$(function () {
//    if ($(window).width() <= 768) {
//        $('#drop_filters').css('display', 'none');
//    }
//});
//
//$(window).resize(function () {
//    if ($(window).width() <= 768) {
//        $('#drop_filters').css('display', 'none');
//    } else {
//        $('#drop_filters').css('display', 'flex');
//    }
//});
//
//

$(document).on("click", "#copy", function (e) {
    e.preventDefault();
    let $input = $(this).prev().select();
    try {
        document.execCommand('copy');
    } catch (err) {
    }
    $input.blur();
});
//window.addEventListener("pageshow", function (event) {
//    var historyTraversal = event.persisted ||
//            (typeof window.performance != "undefined" &&
//                    window.performance.navigation.type === 2);
//            console.log(historyTraversal);
//    if (historyTraversal) {
//        window.location.reload(true);
//    }
//});


function parseResponse(id, resp) {
    $("#" + id).html(resp['html']);

    $.each(resp['facets'], function (param, items) {

        //console.log('items', items);

        var checkedItems = [];

        var checkedOpts = $('#param_' + param).find('label.checkbox input:checked');

        $.each(checkedOpts, function (_idx, el) {
            checkedItems.push($(el).val().trim());
        });


        var s = '';
        $.each(items, function (idx, item) {

            var elId = "p_" + param + "_" + item['id'];

            var isChecked = checkedItems.indexOf(item['id']) > -1;

            if (isChecked) {

                s += `<label class="checkbox">
					<input id="${elId}" type="checkbox" name="p[${param}][]" value="${item['id']}" checked="checked">                                        
					<span>${item['value']}</span>
					</label>`;

            } else {
                s += `<label class="checkbox">
					<input id="${elId}" type="checkbox" name="p[${param}][]" value="${item['id']}">                                        
					<span>${item['value']}</span>
					</label>`;
            }


        });

        $('#param_' + param).html(s);
    });
}

/**
 * Pagination scroll up page
 */
$(document).on("click", ".pagination_link", function (e) {
    e.preventDefault();

    window.scroll({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });

});

// $('.dark_fond').on('click', function () {
//
//     if ($('#modal-download').hasClass('opac1')) {
//         $('#modal-download').removeClass('opac1').addClass('opac0');
//     }
//
//     if ($('#modal-download-with-image').hasClass('opac1')) {
//         $('#modal-download-with-image').removeClass('opac1').addClass('opac0');
//     }
//
// });





