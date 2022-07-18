/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

import App from './components/App';
import router from './router';
import store from './store';

global.store = store;
global.router = router;

Vue.use(router);
Vue.use(Vuetify, {
    options: {
        themeVariations: ['primary', 'secondary', 'accent'],
        extra: {
        mainToolbar: {
            color: 'primary',
        },
        sideToolbar: {
        },
        sideNav: 'primary',
        mainNav: 'grey lighten-1',
        bodyBg: '',
        }
    }
});

const app = new Vue({
    el: '#app',
    router,
    store,
    components: { App }
});

export default app;
