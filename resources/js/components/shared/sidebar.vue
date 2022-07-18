<template>

    <v-list class="sidebar-list">
        <v-list-tile @click="router.push({ name: 'dashboard' })">
            <v-list-tile-title>Dashboard</v-list-tile-title>
        </v-list-tile>
        <v-list-tile @click="router.push({ name: 'events' })">
            <v-list-tile-title>Events</v-list-tile-title>
        </v-list-tile>
        <v-list-tile @click="router.push({ name: 'bookings' })">
            <v-list-tile-title>Bookings</v-list-tile-title>
        </v-list-tile>
        <v-list-tile @click="router.push({ name: 'reports' })">
            <v-list-tile-title>Reports</v-list-tile-title>
        </v-list-tile>
    </v-list>

</template>

<script>
/* global store, router */
export default {
  name: 'sidebar',
  data: () => ({
    store: store,
    router: router,
  }),
  methods: {
    logout () {
      if (store.state.session) {
        axios.post('/auth/logout', {}, {
          headers: {
            'Authorization': `Bearer ${store.state.session.access_token}` 
          }})
          .then(function (response) {
            // handle success
            if (response.status === 200) {
              alert('Logged out');
              store.commit('setSession', {});
              store.commit('setUser', {});
              store.commit('setDocuments', []);
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

.sidebar-list {
    background: rgb(223, 223, 223);
    height: 100%;
}
.sidebar-list * {
    color: #575454;
    cursor: pointer;
}

</style>