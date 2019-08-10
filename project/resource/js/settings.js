var Settings = {

    save: function (type, value, element) {

        $.ajax({
            url: base_url + 'app/settings/save/',
            method: "POST",
            dataType: 'json',
            data: {
                type: type,
                value: value
            }
        }).done(function (result) {

            if (typeof result.result !== 'undefined') {
                if (result.result === 'ok' && result.data === true) {
                    saveIcon(element);
                    $(element).attr('old_value', $(element).val());
                } else {
                    showError("Błąd!", "Dane nie zapisane.");
                }
            } else {
                showError("Błąd!", "Dane nie zapisane.");
            }
        }).fail(function () {
            showError("Błąd!", "Dane nie zapisane.");
        });
    }
};

$(document).ready(function () {
    $('.data-grab').on('blur', function () {
        if ($(this).attr('old_value') !== $(this).val()) {
            Settings.save(
                    $(this).attr('id'),
                    $(this).val(),
                    $(this)
                    );
        }
    });

    if ($('.list-group').is(':visible')) {

        $(".list-group").sortable({
            update: function () {
                contactsListTemplateSave();
            }
        });

        $('.list-group input').on('change', function () {
            contactsListTemplateSave();
        });

    }
});

function contactsListTemplateSave() {

    var newTemplate = [];

    $('.templateElement:checked').each(function () {
        newTemplate.push($(this).parent().data('key'));
    });

    $.ajax({
        url: base_url + 'app/settings/save_contacts_list_template/',
        method: "POST",
        dataType: 'json',
        data: {
            template: newTemplate
        }
    }).done(function (result) {


    }).fail(function () {
    });


}