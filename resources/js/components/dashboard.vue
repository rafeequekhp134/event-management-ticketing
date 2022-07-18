<template>
  <div id="dashboard">
    <v-layout class="flex-column">
      <p class="ma-4">Hey user, Welcome to your dashboard</p>
      <v-layout wrap class="ml-4 flex-column">
        <h2 class="xs12 mb-3">Sales Report</h2>
        <v-flex sm3>
          <v-select
            class="xs12 sm3 pt-0"
            v-model="reportDays" 
            label="Filter Reports"
            :items="reportDaysItems"
            item-key="value"
            @change="setSalesGraph()"
          >
          </v-select>
        </v-flex>
      </v-layout>
      <v-layout wrap>
        <v-flex class="xs12 sm8 ma-4" :class="{ 'sm12': reportDays > 7 }">
            <v-card
              class="mx-auto"
              color="grey lighten-4"
            >
              <v-card-title>
                <v-layout
                  column
                  align-start
                >
                  <div class="caption grey--text text-uppercase">
                    Total Sales
                  </div>
                  <div>
                    <span
                      class="display-2 font-weight-black"
                      v-text="formatPrice(totalSales) || '—'"
                    ></span>
                    <strong v-if="totalSales">Rs</strong>
                  </div>
                </v-layout>

                <v-spacer></v-spacer>

                <v-btn icon class="align-self-start" size="28">
                  <v-icon>mdi-arrow-right-thick</v-icon>
                </v-btn>
              </v-card-title>

              <v-sheet color="transparent">
                <v-sparkline
                  :key="String(totalSales)"
                  :labels="labels"
                  :value="values"
                  color="black"
                  line-width="2"
                  padding="14"
                  :show-labels="true"
                  :label-size="3"
                  :smooth="2"
                  :gradient="['#f72047', '#ffd200', '#1feaea']"
                  type="trend"
                  auto-draw
                  stroke-linecap="square"
                ></v-sparkline>
              </v-sheet>
            </v-card>
        </v-flex>
      </v-layout>
    </v-layout>
  </div>
</template>

<script>
/* global store router */
import moment from 'moment';
const exhale = ms => new Promise(resolve => setTimeout(resolve, ms));

export default {
  data: () => ({
    store: store,
    checking: false,
    heartbeats: [],
    labels: [],
    values: [],
    totalSales: 0,
    reportDays: 7,
    reportDaysItems:[
      { text: 'Last 7 days', value: 7 },
      { text: 'Last 15 days', value: 15 },
      { text: 'Last 30 days', value: 30 },
      { text: 'Last 3 months', value: 90 },
    ]
  }),
  components: {},
    created () {
      this.fetchReport();
    },
    computed: {
      Reports: {
          set (value) {
              store.commit('setReports', value);
          },
          get () {
              return store.state.Reports;
          }
      }
    },
    beforeRouteEnter (to, from, next) {
      if (typeof store.state.session.access_token === 'undefined') {
        router.push({ name: 'login' });
      } else {
        next();
      }
    },
    methods: {
      fetchReport () {
          let self = this;
          window.axios.get('/reports', {
          headers: {
              'Authorization': `Bearer ${store.state.session.access_token}` 
          }})
          .then(function (response) {
              // handle success
              if (response.status === 200) {
                  self.Reports = response.data.data;

                  /* Set the graph data */
                  self.setSalesGraph();
              } else {
                  alert('Failed to fetch event reports');
              }
          })
          .catch(function (error) {
              // handle error
              alert('Error: ' + error);
              console.log(error);
          });
      },
      setSalesGraph () {
        if (this.Reports.length) {
          this.labels = [];
          this.values = [];
          for (let i = this.reportDays - 1; i >= 0; i--) {
            let date = moment().add(-i, 'days').format('YYYY-MM-DD');
            this.labels.push(moment(date).format('Do MMMM'));

            /* Get the total amount for the date */
            let dateReport = this.Reports.filter(x => x.created_at.indexOf(date) > -1);
            if (dateReport.length) {

              let dateAmount = dateReport.reduce((a, b) => {
                return parseInt(a) + parseInt(b['amount']);
              }, 0);

              this.values.push(dateAmount);

            } else {
              this.values.push(0);
            }
          }

          /* Calculate the totalSales amount */
          console.log('this.values', this.values);
          this.totalSales = this.values.reduce((a, b) => {
            return parseInt(a) + parseInt(b);
          }, 0);

        }
      },
      formatPrice (value) {
        if (value) {
          let val = Number((value).toFixed(2)).toLocaleString();
          return val.indexOf('.') > -1 ? val : (val + '.00');
        }
      },
    }
}
</script>

<style>

.flex-column {
  display: flex;
  flex-direction: column;
}
#dashboard g {
  margin-top: 10px;
}
#dashboard g text {
  font-size: 5px !important;
  padding: 10px 0 !important
}

</style>