var JSAlert = require('js-alert');
"use strict";

//IIFE
window.tickers = function (message) {
    JSAlert.alert("<code>"+message+"</code>", null, JSAlert.Icons.Failed);
};
