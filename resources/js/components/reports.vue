<template>
  <div id="reports">
    <h2 class="ma-4">Reports</h2>
    <v-layout wrap row class="mb-4">
        <v-flex sm3 pa-2>
            <v-select
                class="ml-3"
                v-model="currentEvent" 
                label="Filter by Event"
                :items="[...[{ name: 'All', id: 'All' }], ...Events]"
                :item-text="'name'"
                :item-value="'id'"
                @change="filterByEvent"
                >
            </v-select>
        </v-flex>
        <v-flex sm3 pa-2>
            <v-select
                class="ml-3"
                v-model="currentType" 
                label="Filter by Seat Type"
                :items="[...['All'], ...seatTypes]"
                @change="filterByType"
                >
            </v-select>
        </v-flex>
    </v-layout>
    <v-data-table
        :headers="headers"
        :items="reportData"
        no-data-text="No data found"
        :pagination.sync="pagination"
    >
        <template v-slot:items="props">
            <tr>
                <td>{{ props.item.customer_name }}</td>
                <td>{{ props.item.event }}</td>
                <td>{{ props.item.capacity_type }}</td>
                <td>{{ props.item.count }}</td>
                <td>{{ props.item.amount }}</td>
                <td>{{ props.item.status }}</td>
                <td>{{ formatDate(props.item.created_at) }}</td>
                <td align="right">
                    <!-- <v-flex class="list-actions">
                        <v-btn icon class="ma-0" @click="cancelBooking(props.item)" title="Cancel">
                            <v-icon size="22" color="grey darken-2">delete_outline</v-icon>
                        </v-btn>
                    </v-flex> -->
                </td>
            </tr>
        </template>
    </v-data-table>
  </div>
</template>

<script>
/* global store, router */
import moment from 'moment';
import swal from 'sweetalert2';
export default {
    data: () => ({
        currentEvent: 'All',
        currentType: 'All',
        reportData: [],
        headers: [
            { text: 'Customer Name', value: 'customer_name', align: 'left' },
            { text: 'Event', value: 'event' },
            { text: 'Type', value: 'capacity_type' },
            { text: 'Count', value: 'count' },
            { text: 'Amount', value: 'amount' },
            { text: 'Booking Status', value: 'status' },
            { text: 'Booked On', value: 'created_at' },
            { text: '', sortable: false },
        ],
        seatTypes: ['Silver', 'Gold', 'Platinum'],
        pagination: {
            sortBy: 'created_at',
            descending: true
        },
        fromDateMenu: false,
        toDateMenu: false,
        rules: {
            required: [v => !!v || 'This field is required'],
            integer: [v => !isNaN(v) || 'Value should be a number'],
            maxCount: []
        }
    }),
    components: {
    },
    computed: {
        Events: {
            set (value) {
                store.commit('setEvents', value);
            },
            get () {
                return store.state.Events;
            }
        },
        Reports: {
            set (value) {
                store.commit('setReports', value);
            },
            get () {
                return store.state.Reports;
            }
        }
    },
    created () {
        this.fetchEvents();
        this.fetchReport();
    },
    methods: {
        fetchEvents () {
            let self = this;
            window.axios.get('/events', {
            headers: {
                'Authorization': `Bearer ${store.state.session.access_token}` 
            }})
            .then(function (response) {
                // handle success
                if (response.status === 200) {
                    self.Events = response.data.data;
                } else {
                    alert('Failed to fetch events');
                }
            })
            .catch(function (error) {
                // handle error
                alert('Error: ' + error);
                console.log(error);
            });
        },
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
                    self.reportData = JSON.parse(JSON.stringify(self.Reports));
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
        filterByEvent () {
            if (this.currentEvent) {
                this.reportData = this.Reports.filter(x => (x.event_id && x.event_id === this.currentEvent) || this.currentEvent === 'All');
            }
        },
        filterByType () {
            if (this.currentType) {
                this.reportData = this.Reports.filter(x => (x.capacity_type && x.capacity_type === this.currentType) || this.currentType === 'All');
            }
        },
        saveBooking () {
            if (this.$refs.formAdd.validate()) {

                let self = this;
                window.axios.post('/bookings', this.newBooking, {
                headers: {
                    'Authorization': `Bearer ${store.state.session.access_token}` 
                }})
                .then(function (response) {
                    // handle success
                    if (response.status === 200) {
                        self.Bookings.push(response.data.data);
                        swal.fire({
                            type: 'success',
                            title: 'Booking Success',
                            text: 'The event booking was submitted successfully',
                            showConfirmButton: true,
                        });
                        self.cancelAddBooking();
                        self.fetchBookings();
                    } else {
                        swal.fire({
                            type: 'warning',
                            title: 'Error',
                            text: 'Failed to create the booking, please try again',
                            showConfirmButton: true,
                            onClose: () => {}
                        });
                    }
                })
                .catch(function (error) {
                    // handle error
                    alert('Error: ' + error);
                    console.log(error);
                });
            }
        },
        formatDate (date, format = 'dddd, MMMM Do YYYY') {
            return date ? moment(date).format(format) : '';
        }
    }
}
</script>

<style scoped>

.flex-column {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.add-booking {
    width: 50%;
    display: flex;
    flex-direction: column;
}

</style>