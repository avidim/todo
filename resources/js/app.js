require('./bootstrap');
window.Vue = require('vue');

import Index from "./Index";

const app = new Vue({
    el: '#app',
    render: h => h(Index),
});
