<template>
  <div id="login">
    <v-layout class="loginWrap">
      <v-flex class="loginContainer sm4">
        <h2>Register</h2>
        <form>
          <v-flex>
            <v-text-field
              v-model="user.name"
              :error-messages="nameErrors"
              label="Name"
              required
            ></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field
              v-model="user.email"
              :error-messages="emailErrors"
              label="Email"
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
          <v-btn @click="submit" color="primary">register</v-btn>
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
      name: '',
      email: '',
      password: ''
    },
    nameErrors: '',
    emailErrors: '',
    passwordErrors: '',
    select: null,
    checkbox: false
    }),
    methods: {
      submit () {
        axios.post('/register', this.user)
          .then(function (response) {
            // handle success
            if (response.status === 200) {
              router.push({ name: 'login' });
            } else {
              alert('Failed to register');
            }
            console.log(response);
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