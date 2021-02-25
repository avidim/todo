require('./bootstrap');
window.Vue = require('vue');

Vue.component('Navbar', require("./components/Navbar").default);
Vue.component('Todos', require("./components/Todos").default);
import Index from "./Index";

const app = new Vue({
    el: '#app',
    render: h => h(Index),
});
