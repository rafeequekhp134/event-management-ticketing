<template>
  <div id="bookings">
    <template v-if="!isAdd">
        <h2 class="ma-4">Bookings</h2>
        <v-flex class="xs12 mb-5">
            <v-btn fab absolute right color="primary" @click="addBooking" class="add-btn-fab">
                <v-icon>add</v-icon>
            </v-btn>
        </v-flex>
        <v-data-table
            :headers="headers"
            :items="Bookings"
            no-data-text="No bookings found"
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
                <v-flex class="list-actions">
                    <v-btn icon class="ma-0" @click="cancelBooking(props.item)" title="Cancel">
                        <v-icon size="22" color="grey darken-2">delete_outline</v-icon>
                    </v-btn>
                </v-flex>
                </td>
            </tr>
            </template>
        </v-data-table>
    </template>
    <template v-else>
        <v-layout class="add-booking">
            <h2 class="ma-4">Add Booking</h2>
            <v-form ref="formAdd">
                <v-layout class="add-booking-form flex-column">
                    <v-flex class="xs12">
                        <v-text-field class="ml-5 mr-5" label="Customer Name" v-model.trim="newBooking.customer_name" :rules="rules.required"></v-text-field>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-select :items="Events" :item-text="'name'" :item-value="'id'" v-model="newBooking.event_id" class="ml-5 mr-5" label="Event" :rules="rules.required" @change="fetchCapacities">
                            <template slot="item" slot-scope="data">
                                {{ data.item.name }} &nbsp; <small> ( {{ `${formatDate(data.item.start_date, 'Do MMMM YYYY')} to ${formatDate(data.item.end_date, 'Do MMMM YYYY')}` }} )</small>
                            </template>
                        </v-select>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-select :items="eventCapacities" v-model="newBooking.type" :item-text="'type'" :item-value="'id'" class="ml-5 mr-5" label="Booking Type" :rules="rules.required" @change="setTotalAmount(true)">
                            <template slot="item" slot-scope="data">
                                {{ data.item.type }} &nbsp; <small> ( {{ `${data.item.capacity} seats left, ${data.item.price}/head` }} )</small>
                            </template>
                        </v-select>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-text-field class="ml-5 mr-5" label="Seat Count" v-model.trim="newBooking.count" :rules="[...rules.required, ...rules.integer, ...rules.maxCount]" @change="setTotalAmount"></v-text-field>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-text-field class="ml-5 mr-5" label="Total Amount" v-model.trim="newBooking.amount" disabled :rules="rules.required"></v-text-field>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-select :items="['Active', 'Inactive']" v-model="newBooking.status" class="ml-5 mr-5" label="Booking Status" :rules="rules.required"></v-select>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs12 class="button-container pr-4 text-xs-right">
                        <v-btn @click="cancelAddBooking">
                            <v-icon size="16" class="pr-1">cancel</v-icon>
                            <div>Cancel</div>
                        </v-btn>
                        <v-btn @click="saveBooking" color="primary">
                            <v-icon size="16" class="pr-1">check_circle</v-icon>
                            <div>Save</div>
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-form>
        </v-layout>
    </template>
  </div>
</template>

<script>
/* global store, router */
import moment from 'moment';
import swal from 'sweetalert2';
export default {
    data: () => ({
        isAdd: false,
        newBooking: {
            customer_name: '',
            event_id: '',
            type: '',
            count: 1,
            amount: 0,
            status: 'Active'
        },
        eventCapacities: [],
        eventBookings: [],
        headers: [
            { text: 'Customer Name', value: 'customer_name', align: 'left' },
            { text: 'Event', value: 'event' },
            { text: 'Type', value: 'capacity_type' },
            { text: 'Count', value: 'count' },
            { text: 'Amount', value: 'amount' },
            { text: 'Status', value: 'status' },
            { text: 'Created', value: 'created_at' },
            { text: '', sortable: false },
        ],
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
        Bookings: {
            set (value) {
                store.commit('setBookings', value);
            },
            get () {
                return store.state.Bookings;
            }
        },
        Events: {
            set (value) {
                store.commit('setEvents', value);
            },
            get () {
                return store.state.Events;
            }
        }
    },
    created () {
        this.fetchBookings();
        this.fetchEvents();
    },
    methods: {
        fetchBookings () {
            let self = this;
            window.axios.get('/bookings', {
            headers: {
                'Authorization': `Bearer ${store.state.session.access_token}` 
            }})
            .then(function (response) {
                // handle success
                if (response.status === 200) {
                    self.Bookings = response.data.data;
                } else {
                    alert('Failed to fetch event bookings');
                }
            })
            .catch(function (error) {
                // handle error
                alert('Error: ' + error);
                console.log(error);
            });
        },
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
        fetchCapacities () {
            if (this.newBooking.event_id) {

                let self = this;
                window.axios.get(`/capacities/${self.newBooking.event_id}`, {
                headers: {
                    'Authorization': `Bearer ${store.state.session.access_token}` 
                }})
                .then(function (response) {
                    // handle success
                    if (response.status === 200) {
                        self.eventCapacities = response.data.data;

                        /* fetch the current bookings */
                        self.fetchEventBookings();
                    } else {
                        alert('Failed to fetch event capacity data');
                    }
                })
                .catch(function (error) {
                    // handle error
                    alert('Error: ' + error);
                    console.log(error);
                });
            }
        },
        fetchEventBookings () {
            if (this.newBooking.event_id) {

                let self = this;
                window.axios.get(`/bookings/${self.newBooking.event_id}`, {
                headers: {
                    'Authorization': `Bearer ${store.state.session.access_token}` 
                }})
                .then(function (response) {
                    // handle success
                    if (response.status === 200) {
                        self.eventBookings = response.data.data;

                        /* Calculate the seats left */
                        self.setSeatsLeft();
                    } else {
                        alert('Failed to fetch event capacity data');
                    }
                })
                .catch(function (error) {
                    // handle error
                    alert('Error: ' + error);
                    console.log(error);
                });
            }
        },
        setSeatsLeft () {
            let self = this;
            if (this.eventCapacities.length && this.eventBookings.length) {
                this.eventCapacities = this.eventCapacities.map(x => {
                    let capacityBookings = self.eventBookings.filter(y => y.type === x.id);
                    if (capacityBookings.length) {
                        /* get the seats left */
                        let totalBookedCount = capacityBookings.reduce((a, b) => {
                            return a + b.count;
                        }, 0);
                        x.capacity = parseInt(x.capacity) - parseInt(totalBookedCount)
                    }
                    return x;
                })
                
                /* Filter out if capacity is less then 1 */
                this.eventCapacities = this.eventCapacities.filter(x => parseInt(x.capacity) && parseInt(x.capacity) >= 1);
            }
        },
        setTotalAmount (isTypeSelected = false) {
            
            if (this.newBooking.type) {
                let selectedCapacity = this.eventCapacities.find(x => x.id === this.newBooking.type);
                /* Set the max count rule if isTypeSelected */
                if (isTypeSelected && selectedCapacity) {
                    this.rules.maxCount = [v => parseInt(v) <= selectedCapacity.capacity || `Only ${selectedCapacity.capacity} seats left`];
                }
                /* Set the total amount */
                if (this.newBooking.count && selectedCapacity && selectedCapacity.price) {
                    this.newBooking.amount = parseInt(this.newBooking.count) * parseInt(selectedCapacity.price);
                }
            }
        },
        addBooking () {
            this.isAdd = true;
            this.newBooking = {
                customer_name: '',
                event_id: '',
                type: '',
                count: 1,
                amount: '',
                status: 'Active'
            };
        },
        cancelAddBooking () {
            this.isAdd = false;
            this.newBooking = {
                customer_name: '',
                event_id: '',
                type: '',
                count: 1,
                amount: '',
                status: 'Active'
            };
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
        },
        cancelBooking (item) {
            let self = this;
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.value) {
                    window.axios.delete(`/bookings/${item.id}`, {
                    headers: {
                        'Authorization': `Bearer ${store.state.session.access_token}` 
                    }})
                    .then(function (response) {
                        // handle success
                        if (response.status === 200) {
                            let itemIndex = self.Bookings.indexOf(item);
                            self.Bookings.splice(itemIndex, 1);
                            swal.fire({
                                type: 'success',
                                title: 'Cancelled',
                                text: 'The booking was cancelled successfully',
                                showConfirmButton: true,
                            });
                        } else {
                            swal.fire({
                                type: 'warning',
                                title: 'Error',
                                text: 'Failed to cancel the booking, please try again',
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
            });
        },
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
.add-btn-fab {
    top: 20px;
}
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