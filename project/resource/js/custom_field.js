var custom_fields = '';

$(document).ready(function () {

    custom_fields = new Vue({

        el: '#custom_fields',
        data: {
            elements: [],
            current: {id: 0, name: '', state: 'on'},
            base_url: base_url,
            search_phrase: '',
        },
        methods: {
            init: function () {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: base_url + 'app/custom_fields/get/',
                    success: (response) => {
                        this.elements = response.data;
                    },
                    error: (response) => {
                    }
                });
            },
            edit: function (id) {

                for (let i = 0; i < this.elements.length; i++) {
                    if (this.elements[i]['id'] == id) {
                        for (let key in this.current) {
                            this.current[key] = this.elements[i][key];
                        }
                    }
                }
            },
            cancel: function () {
                for (let key in this.current) {
                    this.current[key] = '';
                }
            },
            toggle: function (id, act) {

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        act: act
                    },
                    url: base_url + 'app/custom_fields/toggle/',
                    success: (response) => {
                        if (response.res === 'ok') {
                            let index = this.elements.findIndex((obj => obj['id'] == id));
                            this.elements[index]['state'] = (act === 'hide' ? 'hidden' : 'on');
                        }
                    },
                    error: (response) => {
                    }
                }, 'json');


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
                    url: base_url + 'app/custom_fields/save/',
                    success: (response) => {
                        if (dataToSave['id'] == '') { // add new
                            dataToSave['id'] = response.data;
                            this.elements.push(dataToSave);
                            this.cancel();
                        } else { // edit existing
                            let index = this.elements.findIndex((obj => obj['id'] == this.current['id']));

                            for (let key in this.current) {
                                if (key !== 'id') {
                                    this.elements[index][key] = this.current[key];
                                }
                            }
                        }

                        this.cancel();
                    },
                    error: (response) => {
                    }
                }, 'json');

            },
            del: function (id) {
                // DELETE
                $.ajax({
                    type: 'POST',
                    url: base_url + 'app/custom_fields/delete/',
                    data: {
                        id: id
                    },
                    success: (response) => {
                        this.elements = this.elements.filter(function (element) {
                            return element['id'] != this;
                        }, id);

                        this.cancel();
                    },
                    error: (response) => {
                    }
                }), 'json';
            },
        },
        computed: {
            filterSearch: function () {
                return this.elements.filter(
                        (data) => {
                    return (data.name.toLowerCase().indexOf(this.search_phrase.toLowerCase()) >= 0);
                });
            }
        },
        created: function () {
            this.init();
        }
    });
});