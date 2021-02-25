require('./bootstrap');
window.Vue = require('vue');

Vue.component('Navbar', require("./components/Navbar").default);
import Index from "./Index";

const app = new Vue({
    el: '#app',
    render: h => h(Index),
});
