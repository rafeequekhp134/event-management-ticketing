<template>
  <div id="login">
    <v-layout class="loginWrap">
      <v-flex class="loginContainer sm4">
        <h2>Login</h2>
        <form>
          <v-flex>
            <v-text-field
              v-model="user.email"
              :error-messages="emailErrors"
              label="Username"
              required
            ></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field
              v-model="user.password"
              type="password"
              :error-messages="passwordErrors"
              label="Password"
              required
            ></v-text-field>
          </v-flex>
          <v-btn @click="submit" color="primary">submit</v-btn>
          <v-btn @click="clear">clear</v-btn>
        </form>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
/* global store, router */
const axios = require('axios');
export default {
  data: () => ({
    user: {
      email: 'admin',
      password: 'admin'
    },
    emailErrors: '',
    passwordErrors: '',
    select: null,
    items: [
      'Item 1',
      'Item 2',
      'Item 3',
      'Item 4'
    ],
    checkbox: false
    }),
    methods: {
      toB (str) {
          return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
              function toSolidBytes(match, p1) {
                  return String.fromCharCode('0x' + p1)
              }))
      },
      fromB (str) {
          return decodeURIComponent(atob(str).split('').map((c) => {
              return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
          }).join(''))
      },
      submit () {
        let self = this;
        axios.post('/auth/login', this.user)
          .then(function (response) {
            // handle success
            console.log(response);
            if (response.error) {
              alert('Error: ' + response.error);
            } else {
              store.commit('setSession', response.data);
              global.localStorage.setItem('EVENT_STACK_USER', self.toB(JSON.stringify(response.data)));
              router.push({ name: 'dashboard' });
            }
          })
          .catch(function (error) {
            // handle error
            alert('Error: ' + error);
            console.log(error);
          });
      },
      clear () {
        this.user = {
          password: '',
          password: '',
        };
      }
    }
}
</script>

<style scoped>

  .loginWrap {
    justify-content: center;
  }
  .loginContainer {
    padding: 15px;
    border: 1px solid #ccc;
    margin-top: 50px;
  }

</style>