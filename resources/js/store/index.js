import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    session: {},
    user: {},
    Events: [],
    Bookings: [],
    Reports: [],
  },
  mutations: {
    setUser (state, user) {
      state.user = user
    },
    setSession (state, session) {
      state.session = session
    },
    setEvents (state, Events) {
      state.Events = Events
    },
    setBookings (state, Bookings) {
      state.Bookings = Bookings
    },
    setReports (state, Reports) {
      state.Reports = Reports
    },
  }
})

export default store;