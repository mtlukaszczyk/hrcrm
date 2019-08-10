function urlencode(url) {
    return url;
}

$(document).ready(
        function () {
            $("#login_form").on("submit", function (e) {
                alert('lfsubmit');
                e.preventDefault();
                var param_0 = $("input[name=param_0]").val();
                var param_1 = $("input[name=param_1]").val();
                window.location.assign(base_url + 'app/account/log_in/' + encodeURIComponent(param_0) + '/' + encodeURIComponent(param_1) + '/');
            });
        }
);

function userLogin() {

    let user = $('#userLogin').val();
    let password = $('#userPassword').val();

    $.ajax({
        url: base_url + 'app/account/log_in/',
        method: "POST",
        dataType: 'json',
        data: {
            user: user,
            password: password
        }
    }).done(function (result) {
        window.location.href = result.link;

    }).fail(function () {

    });
}

var notify_template = '<div data-notify="container" class="alert alert-{0}" style="padding-right: 40px;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="margin: 4px">×</button>' +
        '<span data-notify="icon"></span> ' +
        '<span data-notify="title">{1}</span> ' +
        '<span data-notify="message">{2}</span>' +
        '</div>' +
        '</div>';

/**
 * Show error message
 * @param header
 * @param content
 * @returns void
 */
function showError(header, content) {
    $.notify({
        message: content,
        title: header
    }, {
        delay: 1113000,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        offset: {
            x: 20,
            y: 60
        },
        template: notify_template,
        type: 'danger',
        newest_on_top: true

    });
}

/**
 * Show saved icon in input
 * @param elem element html element
 */
function saveIcon(elem) {
    $(elem).after('<span class="glyphicon glyphicon-ok form-control-feedback saved"></span>');
    $(elem).addClass("saved");

    setTimeout(function () {
        $(elem).removeClass("saved");
        $(elem).next("span").remove();
    }, 500);
}