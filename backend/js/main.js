// @ts-nocheck

/* For sidebar remember previous state */
$.AdminLTESidebarTweak = {};

$.AdminLTESidebarTweak.options = {
    EnableRemember: true,
    NoTransitionAfterReload: true
    //Removes the transition after page reload.
};

$(function () {
    "use strict";

    $("body").on("collapsed.pushMenu", function () {
        if ($.AdminLTESidebarTweak.options.EnableRemember) {
            localStorage.setItem("toggleState", "closed");
        }
    });

    $("body").on("expanded.pushMenu", function () {
        if ($.AdminLTESidebarTweak.options.EnableRemember) {
            localStorage.setItem("toggleState", "opened");
        }
    });

    if ($.AdminLTESidebarTweak.options.EnableRemember) {
        var toggleState = localStorage.getItem("toggleState");
        if (toggleState == 'closed') {
            if ($.AdminLTESidebarTweak.options.NoTransitionAfterReload) {
                $("body").addClass('sidebar-collapse hold-transition').delay(100).queue(function () {
                    $(this).removeClass('hold-transition');
                });
            } else {
                $("body").addClass('sidebar-collapse');
            }
        }
    }
});

/* For select2 */
if ($.fn.modal != undefined) {
    $.fn.modal.Constructor.prototype.enforceFocus = function () { };
}


function formatErrorMessage(jqXHR, exception) {
    if (jqXHR.status === 0) {
        return ajax_errors.http_not_connected;
    } else if (jqXHR.status == 400) {
        return ajax_errors.request_forbidden;
    } else if (jqXHR.status == 404) {
        return ajax_errors.not_found_request;
    } else if (jqXHR.status == 500) {
        return ajax_errors.session_expire;
    } else if (jqXHR.status == 503) {
        return ajax_errors.service_unavailable;
    } else if (exception === 'parsererror') {
        return ajax_errors.parser_error;
    } else if (jqXHR.status == 419 || exception === 'timeout') {
        return ajax_errors.request_timeout;
    } else if (exception === 'abort') {
        return ajax_errors.request_abort;
    } else {
        var message = '';
        try {
            var r = jQuery.parseJSON(jqXHR.responseText);
            if (jQuery.isEmptyObject(r) == false) {
                $.each(r.errors, function (key, value) {
                    if (jQuery.isEmptyObject(r) != false) {
                        $.each(value, function (key, row) {
                            message += '<p>' + row + '</p>';
                        });
                    } else {
                        message += '<p>' + value + '</p>';
                    }
                });
            }
        } catch (e) {
            message = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        return message;
    }
}

function active_current_url() {
    let findAllLink = $('ul.sidebar-menu').find('a');
    if (findAllLink.length) {
        $.each(findAllLink, function (index, value) {
            let href = $(this).attr('href');
            if (CURRENT_URL == href) {
                $(this).parent('li').addClass('active');
            }
        });
        let $this = $('ul.sidebar-menu').find('li.active');
        $this.parentsUntil(".sidebar-menu").addClass('open active');
    }
}
active_current_url();

function initBasicCkEditor() {
    CKEDITOR.config.toolbar = [
        ['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Find', 'Replace', '-', 'Outdent', 'Indent'],
        ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['Smiley', 'TextColor', 'BGColor', 'Source']
    ];
}

function reloadTable(table) {
    $(`#${table}`).DataTable().ajax.reload();
}

function initStarRating(read_only) {
    read_only = read_only !== undefined ? read_only : false;

    $('.rating-widget').starRating({
        initialRating: 5,
        strokeColor: '#894A00',
        strokeWidth: 5,
        starSize: 17,
        readOnly: read_only
    });
}

function getLatLong(address) {
    var location = { latitude: null, longitude: null };
    if (!$.trim(address)) return location;
    return new Promise((resolve, reject) => {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ address }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                location.latitude = results[0].geometry.location.lat();
                location.longitude = results[0].geometry.location.lng();
            }
            resolve(location);
        });
    });
}

const formatDate = (date = null, format = 'YYYY-MM-DD hh:mm A') => moment(moment.utc(date).toDate()).local().format(format);

function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e);
    }
};

// For Cropper.js
function dataURLtoMimeType(dataURL) {
    var BASE64_MARKER = ';base64,';
    var data;

    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        data = decodeURIComponent(parts[1]);
    } else {
        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;

        data = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            data[i] = raw.charCodeAt(i);
        }
    }

    var arr = data.subarray(0, 4);
    var header = "";
    for (var i = 0; i < arr.length; i++) {
        header += arr[i].toString(16);
    }
    switch (header) {
        case "89504e47":
            mimeType = "image/png";
            break;
        case "47494638":
            mimeType = "image/gif";
            break;
        case "ffd8ffe0":
        case "ffd8ffe1":
        case "ffd8ffe2":
            mimeType = "image/jpeg";
            break;
        default:
            mimeType = ""; // Or you can use the blob.type as fallback
            break;
    }

    return mimeType;
}

function initSelect2(target = '.select2-class') {
    $(target).select2({
        width: '100%',
        dir: CURRENT_LANG == 'ar' ? 'rtl' : 'ltr',
    });
}

function initSelect2Custom(target = '.select2-class2') {
    $(target).select2({
        width: '100%',
        allowClear: true,
        dir: CURRENT_LANG == 'ar' ? 'rtl' : 'ltr',
    });
}

// Display .. after n words
String.prototype.trimToLength = function (n) {
    return (this.length > n)
        ? jQuery.trim(this).substring(0, n).split(" ").slice(0, -1).join(" ") + ".."
        : this;
};

$(document).ready(function (e) {
    if ($('.select2-class').length > 0) {
        initSelect2();
    }
    if ($('.select2-class2').length > 0) {
        initSelect2Custom();
    }

    if ($('.multiple-select').length > 0) {
        $('.multiple-select').multipleSelect({
            width: '100%',
        });
    }

    if ($('.date-picker').length > 0) {
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });
    }

    if ($('.dob-picker').length > 0) {
        $('.dob-picker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            endDate: new Date()
        });
    }

    if ($('.timepicker-class').length > 0) { $('.timepicker-class').timepicker(); }

    if ($('.alpha-num-text').length > 0) {
        $('.alpha-num-text').keyup(function () {
            var yourInput = $(this).val();
            re = /[`~!@#$%^&*()_|+\-=?;'"<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(yourInput);
            if (isSplChar) {
                var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;'"<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
    }

    $(".custom-positive-integer").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(document).on('click', '.select2-check-all', function (e) {
        const select_box = $(this).data('select_box');

        if ($(this).is(':checked')) {
            $(`${select_box} > option`).prop("selected", "selected");
        } else {
            $(`${select_box} > option`).removeAttr("selected");
        }
        $(select_box).trigger("change");
    });
});
