require('./bootstrap');
import Vue from "vue";
import VueRouter from "vue-router";
import router from './routes';
import Vuex from 'vuex';
import dataStore from './store/index'
import vuetify from "./vuetify";
import moment from "moment";
import excel from "vue-excel-export";


window.Vue = require('vue').default;
window.Vue = Vue;
window.moment = moment;

Vue.use(VueRouter);
Vue.use(Vuex)
Vue.use(excel)

const store = new Vuex.Store(
    dataStore
)

Vue.component('nav-bar', require('./components/Navbar').default)
Vue.component('navigation-drawer', require('./components/NavigationDrawer').default)
Vue.component('app-footer', require('./components/Footer').default)
Vue.component('app-vue', require('./Views/App').default)
const app = new Vue({
    el: '#app',
    router, store, vuetify, moment
});
