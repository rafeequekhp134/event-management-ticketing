/* global */
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store'

Vue.use(VueRouter)

// import App from './components/App'
import Welcome from './../components/Welcome'
import Login from './../components/login'
import Register from './../components/register'
import Dashboard from './../components/dashboard'
import Events from './../components/events'
import Bookings from './../components/bookings'
import Reports from './../components/reports'

const router = new VueRouter({
    mode: 'hash',
    base: '/',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Welcome,
            meta: { title: 'Home' }
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: Dashboard
        },
        {
            path: '/events',
            name: 'events',
            component: Events
        },
        {
            path: '/bookings',
            name: 'bookings',
            component: Bookings
        },
        {
            path: '/reports',
            name: 'reports',
            component: Reports
        },
    ],
})

router.beforeEach(async (to, from, next) => {

    /* Check if user exists in state */
    if (['home', 'register', 'login'].indexOf(to.name) > -1) {
        next();
    } else if (!store.state.session.access_token) {
        let localData = global.localStorage.getItem('EVENT_STACK_USER');
        if (localData) {
            localData = JSON.parse(fromB(localData))
            store.commit('setSession', localData);
            router.push({ path: to.path });
        } else {
            router.push({ name: 'login' });
        }
    } else if (store.state.session.access_token) {
        next();
    } else {
        router.push({ name: 'login' });
    }

})

const fromB = function (str) {
    return decodeURIComponent(atob(str).split('').map((c) => {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    }).join(''))
}

export default router;