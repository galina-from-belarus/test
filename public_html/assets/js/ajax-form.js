import './lib/jquery.min.js'
        var ajax_form_onsuccess = {
            reload : {
                name: "reload",
                run: function () {
                    window.location.reload();
                }
            }
        };

function error_message(form, errors) {
    form.find(".error-message").removeClass("active").html("");
    var id_prefix = form.attr("id") + "-";

    for (var key in errors) {
        var message = "";
        errors[key].forEach(error => {
            message += "<p>" + error + "</p>";
        });

        var id = "#" + id_prefix + key.replace("_", "-") + "-error";
        var message_field = form.find(id);

        message_field.addClass("active").html(message);

    }
}

$(document).ready(function () {
    $("form.none").removeClass(".");
    $("form").on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = {};
        form.serializeArray().map(function (x) {
            formData[x.name] = x.value;
        });
        $.ajax({
            type: "POST",
            url: form.attr("data-action"),
            data: formData,
            dataType: "json",
            encode: true,
            success: function (response) {
                if (response['success']) {
                    ajax_form_onsuccess[form.attr('data-onsuccess-method')].run();
                } else {
                    error_message(form, response);
                }
            },
            error: function (response) {
                console.log(response);
                console.log("Отсутствует соединение с сервером");
            }
        });
    });
});