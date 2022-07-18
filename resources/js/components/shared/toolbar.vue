<template>
  <v-toolbar dark color="warning">
    <v-toolbar-side-icon></v-toolbar-side-icon>

    <v-toolbar-title class="white--text">Event Stack</v-toolbar-title>

    <v-spacer></v-spacer>

    <v-btn v-if="!isOnline" flat :to="{ name: 'home' }">Home</v-btn>

    <v-btn v-if="!isOnline" flat :to="{ name: 'register' }">
      Register
    </v-btn>
    <v-btn flat v-if="!isOnline" :to="{ name: 'login' }">
      Login
    </v-btn>

    <v-btn icon @click="logout" v-if="isOnline">
      <v-icon>power_settings_new</v-icon>
    </v-btn>

  </v-toolbar>
</template>

<script>
/* global store, router */
export default {
  name: 'toolbar',
  data: () => ({
    store: store
  }),
  computed: {
    isOnline () {
      return store.state.session.access_token;
    }
  },
  methods: {
    logout () {
      if (store.state.session) {
        
        /* Clear the local storage user */
        global.localStorage.setItem('EVENT_STACK_USER', "");

        axios.post('/auth/logout', {}, {
          headers: {
            'Authorization': `Bearer ${store.state.session.access_token}` 
          }})
          .then(function (response) {
            // handle success
            if (response.status === 200) {
              
              store.commit('setSession', {});
              store.commit('setUser', {});
              store.commit('setEvents', []);
              store.commit('setBookings', []);
              
              router.push({name: 'login'});
              
            }
            console.log(response);
          })
          .catch(function (error) {
            // handle error
            store.commit('setSession', {});
            store.commit('setUser', {});
            store.commit('setDocuments', []);
            router.push({name: 'login'});
            console.log(error);
          })
          .then(function () {
            // always executed
          });
      }
    }
  }
};
</script>

<style scoped>

</style>