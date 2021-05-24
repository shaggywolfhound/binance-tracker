//
// import Vue from 'vue'
require('jquery');
require('axios');
var JSAlert = require('js-alert');
"use strict";

//IIFE
let currenciesPage = function () {

    //Get quote asset elements
    //enable tracked column on Quote currency selection
    //bugger sort on axios load!!
    let triggeredChange = function () {
        let quote = $('.quote');
        quote.find('select').on('change', function () {
            if ($(this).val() !== '') {
                //make checkbox enabled
                let checkbox = $(this).closest('td').siblings('.tracked').find('input:checkbox');
                checkbox.attr('disabled', false).addClass('border-green-600').css({
                    'cursor': 'pointer'
                });
                checkbox.parent('label').siblings('span.tooltiptext').hide();
            }
        });
    }
    triggeredChange();

    //submit data using axios
    let submit = $('button:submit').on('click', function () {
        //get data ready to send to database for save
        let tracked = $('table td.tracked').find(':checkbox');
        let row = tracked.closest('tr');
        let data = {};

        //get the data from the table
        row.each(function (index) {
            let id          = $(this).data('id');
            let asset       = $(this).find('td.asset').text();
            let quote       = $(this).find('td.quote').find('select').val();
            let checked     = $(this).find('td.tracked').find('input:checkbox').prop('checked');

            data[index] = {
                'currency_id'       : id,
                'quote_currency_id' : quote,
                'is_tracked'        : checked
            };
        });

        //post to backend
        window.axios.defaults.headers.common = {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type':'application/json',
            'Accept':'application/json'
        };

        axios.post('/currencies/patch', data).then(function(response) {
            JSAlert.alert("<code>Currencies updated</code>", null, JSAlert.Icons.Success);

            //replace table with returned axios data
            let newhtml =  $(response.data).find('table.currencies');
            let screen = $('table.currencies');
            screen.replaceWith(newhtml);
            //re-add watchers
            triggeredChange();

        }, function(error) {
            JSAlert.alert("<code>Something went wrong, please try again later.</code>", null, JSAlert.Icons.Failed);
        });
    })


}();
