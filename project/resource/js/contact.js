var Contact = {

    saveDetail: function (type, value, element) {

        $.ajax({
            url: base_url + 'app/contact/edit_detail/',
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

var Comments = '';

$(document).ready(function () {
    $('.data-grab').on('blur', function () {
        if ($(this).attr('old_value') !== $(this).val()) {
            Contact.saveDetail(
                    $(this).attr('id'),
                    $(this).val(),
                    $(this)
                    );
        }
    });

    var oTable = '';

    $('#groups').on('change', function () {

        var groupsSelected = $(this).val();

        $.ajax({
            url: base_url + 'app/contact/save_groups/',
            method: "POST",
            dataType: 'json',
            data: {
                groupsSelected: groupsSelected
            }
        }).done(function (result) {
        }).fail(function () {
            showError("Błąd!", "Dane nie zapisane.");
        });
    });

    if ($("#table_contacts").is(":visible")) {

        var oTable = "";
        var created = false;

        var language = {
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        };

        var lengthMenu = [[10, 25, 50, -1], [10, 25, 50, "All"]];
        var exportOptions = {
            columns: ':not(:last-child):not(.state-changer)'
        };

        var buttons = [{
                extend: 'copy',
                header: false,
                exportOptions: exportOptions
            },
            {
                extend: 'csv',
                header: false,
                exportOptions: exportOptions
            },
            {
                extend: 'excel',
                header: false,
                exportOptions: exportOptions
            },
            {
                extend: 'pdfHtml5',
                header: false,
                exportOptions: exportOptions
            },
            {
                extend: 'print',
                header: false,
                exportOptions: exportOptions
            }
        ];

        let columns = $("#table_contacts").length;

        oTable = $("#table_contacts").dataTable({
            lengthMenu: lengthMenu,
            stateSave: true,
            columnDefs: [{
                    targets: [columns - 2],
                    orderable: false,
                    searchable: false
                }],
            order: [[0, "desc"]],
            dom: 'Blfrtip',
            buttons: buttons,
            language: language
        });

        $(".dataTables_length select").addClass("form-control");
        $(".dataTables_filter input").addClass("form-control");


        $('.dt-button').each(function () {
            var cl = $(this).attr('class');
            $(this).attr('class', 'btn btn-warning ' + cl);
        });
    }

    if ($("#comments").is(":visible")) {

        Comments = new Vue({
            el: '#comments',
            data: {
                comments: [],
                current: {id: '', created_at: '', message: '', type: 'mail', act_dt: '', important: 'n'}
            },
            methods: {
                init: function () {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: base_url + 'app/comments/get/',
                        success: (response) => {
                            this.comments = response.comments;
                        },
                        error: (response) => {
                        }
                    });
                },
                importantSwitch: function (id) {

                    $.ajax({
                        type: 'POST',
                        url: base_url + 'app/comments/important/',
                        data: {
                            id: id
                        },
                        success: (response) => {
                            for (let i = 0; i < this.comments.length; i++) {
                                if (this.comments[i]['id'] == id) {
                                    if (this.comments[i]['important'] == 'y') {
                                        this.comments[i]['important'] = 'n';
                                    } else {
                                        this.comments[i]['important'] = 'y';
                                    }
                                }
                            }
                        },
                        error: (response) => {
                        }
                    }), 'json';


                },
                edit: function (id) {
                    for (let i = 0; i < this.comments.length; i++) {
                        if (this.comments[i]['id'] == id) {
                            for (let key in this.current) {
                                this.current[key] = this.comments[i][key];
                            }
                        }
                    }
                },
                save: function () {
                    let dataToSave = {};
                    for (var key in this.current) {
                        dataToSave[key] = this.current[key];
                    }

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: dataToSave,
                        url: base_url + 'app/comments/save/',
                        success: (response) => {
                            if (dataToSave['id'] == '') { // add new
                                dataToSave['id'] = response.data;
                                dataToSave['created_at'] = response.created_at.replace('T', ' ').split('.')[0];
                                this.comments.push(dataToSave);
                            } else { // edit existing
                                let index = this.comments.findIndex((obj => obj['id'] == this.current['id']));

                                for (let key in this.current) {
                                    if (key !== 'id') {
                                        this.comments[index][key] = this.current[key];
                                    }
                                }
                            }

                            this.cancel();
                        },
                        error: (response) => {
                        }
                    }, 'json');
                },
                cancel: function () {
                    for (let key in this.current) {
                        this.current[key] = '';
                    }

                    this.current['type'] = 'mail';
                },
                del: function (id) {
                    // DELETE
                    $.ajax({
                        type: 'POST',
                        url: base_url + 'app/comments/del/',
                        data: {
                            id: id
                        },
                        success: (response) => {
                            this.comments = this.comments.filter(function (element) {
                                return element['id'] != this;
                            }, id);
                        },
                        error: (response) => {
                        }
                    }), 'json';
                }
            },
            computed: {

            },
            created: function () {
                this.init();
            }

        });


    }
});