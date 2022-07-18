<template>
    <div>
        <toolbar class="app--toolbar"></toolbar>
        <v-app>
          <v-content>
            <v-layout wrap class="content-layout">
              <v-flex sm2 v-if="store.state.session.access_token">
              <!-- <v-flex sm2> -->
                <sidebar />
              </v-flex>
              <v-flex :class="{ 'sm10': store.state.session.access_token }">
                <router-view></router-view>
              </v-flex>
            </v-layout>
          </v-content>
        </v-app>
    </div>
</template>
<script>
/* global store */
import Toolbar from './shared/toolbar.vue'
import Sidebar from './shared/sidebar.vue'
  export default {
    data () {
      return {
        store: store
      }
    },
    components: { Toolbar, Sidebar }
  }
</script>

<style scoped>
  .content-layout {
    height: 100%;
  }
</style>