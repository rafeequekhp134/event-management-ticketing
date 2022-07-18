<template>
  <div id="events">
    <template v-if="!isAdd">
        <h2 class="ma-4">Events</h2>
        <v-flex class="xs12 mb-5">
            <v-btn fab absolute right color="primary" @click="addEvent" class="add-btn-fab">
                <v-icon>add</v-icon>
            </v-btn>
        </v-flex>
        <v-data-table
            :headers="headers"
            :items="Events"
            no-data-text="No events found"
            :pagination.sync="pagination"
        >
            <template v-slot:items="props">
            <tr>
                <td>{{ props.item.name }}</td>
                <td>{{ (new Date(props.item.end_date).getTime() <= new Date().getTime()) ? 'Expired' : props.item.status }}</td>
                <td>{{ formatDate(props.item.start_date) }}</td>
                <td>{{ formatDate(props.item.end_date) }}</td>
                <td>{{ formatDate(props.item.created_at) }}</td>
                <td align="right">
                <v-flex class="list-actions">
                    <v-btn icon class="ma-0" @click="deleteEvent(props.item)" title="Delete">
                        <v-icon size="22" color="grey darken-2">delete_outline</v-icon>
                    </v-btn>
                </v-flex>
                </td>
            </tr>
            </template>
        </v-data-table>
    </template>
    <template v-else>
        <v-layout class="add-event">
            <h2 class="ma-4">Add Event</h2>
            <v-form ref="formAdd">
                <v-layout class="add-event-form flex-column">
                    <v-flex class="xs12">
                        <v-text-field class="ml-5 mr-5" label="Event Name" v-model.trim="newEvent.name" :rules="rules.required"></v-text-field>
                    </v-flex>
                    <v-flex class="xs12">
                        <v-select :items="['Active', 'Inactive']" v-model="newEvent.status" class="ml-5 mr-5" label="Event Status" :rules="rules.required"></v-select>
                    </v-flex>

                    <!-- To choose the capacity -->
                    <v-layout wrap>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="1. Capacity (Silver)" v-model.trim="capacity.Silver.capacity" :rules="rules.integer"></v-text-field>
                        </v-flex>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="Price per Head (Silver)" v-model.trim="capacity.Silver.price" :rules="rules.integer"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="2. Capacity (Gold)" v-model.trim="capacity.Gold.capacity" :rules="rules.integer"></v-text-field>
                        </v-flex>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="Price per Head (Gold)" v-model.trim="capacity.Gold.price" :rules="rules.integer"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="3. Capacity (Platinum)" v-model.trim="capacity.Platinum.capacity" :rules="rules.integer"></v-text-field>
                        </v-flex>
                        <v-flex class="xs6">
                            <v-text-field class="ml-5 mr-5" label="Price per Head (Platinum)" v-model.trim="capacity.Platinum.price" :rules="rules.integer"></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-flex class="xs12 ml-5 mr-5">
                        <v-menu
                            v-model="fromDateMenu"
                            :close-on-content-click="false"
                            full-width
                            max-width="290"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    :value="computedDateFormattedMomentjs('start_date')"
                                    clearable
                                    label="Start Date"
                                    readonly
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker :min="new Date().toISOString().substr(0,10)" v-model="newEvent.start_date" @change="fromDateMenu = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                    <v-flex class="xs12 ml-5 mr-5">
                        <v-menu
                            v-model="toDateMenu"
                            :close-on-content-click="false"
                            full-width
                            max-width="290"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    :value="computedDateFormattedMomentjs('end_date')"
                                    clearable
                                    label="End Date"
                                    readonly
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker :min="newEvent.start_date ? new Date(newEvent.start_date).toISOString().substr(0,10) : new Date().toISOString().substr(0,10)" v-model="newEvent.end_date" @change="toDateMenu = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs12 class="button-container pr-4 text-xs-right">
                        <v-btn @click="cancelAddEvent">
                            <v-icon size="16" class="pr-1">cancel</v-icon>
                            <div>Cancel</div>
                        </v-btn>
                        <v-btn @click="saveEvent" color="primary">
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
        newEvent: {
            name: '',
            status: 'Active',
            start_date: '',
            end_date: ''
        },
        capacity: {
            Silver: { price: 0, capacity: 0 },
            Gold: { price: 0, capacity: 0 },
            Platinum: { price: 0, capacity: 0 }
        },
        headers: [
            { text: 'Event Name', value: 'name', align: 'left' },
            { text: 'Status', value: 'status' },
            { text: 'Start Date', value: 'start_date' },
            { text: 'End Date', value: 'end_date' },
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
            integer: [v => !isNaN(v) || 'Count should be a number']
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
    },
    created () {
        this.fetchEvents();
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
        addEvent () {
            this.isAdd = true;
            this.newEvent = {
                name: '',
                status: 'Open',
                start_date: '',
                end_date: ''
            };
        },
        cancelAddEvent () {
            this.isAdd = false;
            this.newEvent = {
                name: '',
                status: 'Open',
                start_date: '',
                end_date: ''
            };
        },
        saveEvent () {
            if (this.$refs.formAdd.validate()) {

                if (!this.newEvent.start_date || !this.newEvent.end_date) {
                    swal.fire({
                        type: 'warning',
                        title: 'Error',
                        text: 'Please choose both start date and end date',
                        showConfirmButton: true,
                        onClose: () => {}
                    });
                    return false;
                }

                let self = this;
                this.newEvent.capacity = self.capacity;

                window.axios.post('/events', this.newEvent, {
                headers: {
                    'Authorization': `Bearer ${store.state.session.access_token}` 
                }})
                .then(function (response) {
                    // handle success
                    if (response.status === 200) {
                        self.Events.push(response.data.data);
                        swal.fire({
                            type: 'success',
                            title: 'Event Created',
                            text: 'The new event was created successfully',
                            showConfirmButton: true,
                        });
                        self.cancelAddEvent();
                    } else {
                        swal.fire({
                            type: 'warning',
                            title: 'Error',
                            text: 'Failed to create the event, please try again',
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
        computedDateFormattedMomentjs (type) {
            return type && this.newEvent[type] ? moment(this.newEvent[type]).format('dddd, MMMM Do YYYY') : '';
        },
        formatDate (date, format = 'dddd, MMMM Do YYYY') {
            return date ? moment(date).format(format) : '';
        },
        deleteEvent (item) {
            let self = this;
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    window.axios.delete(`/events/${item.id}`, {
                    headers: {
                        'Authorization': `Bearer ${store.state.session.access_token}` 
                    }})
                    .then(function (response) {
                        // handle success
                        if (response.status === 200) {
                            let itemIndex = self.Events.indexOf(item);
                            self.Events.splice(itemIndex, 1);
                            swal.fire({
                                type: 'success',
                                title: 'Deleted',
                                text: 'The event was deleted successfully',
                                showConfirmButton: true,
                            });
                        } else {
                            swal.fire({
                                type: 'warning',
                                title: 'Error',
                                text: 'Failed to delete the event, please try again',
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
.add-event {
    width: 50%;
    display: flex;
    flex-direction: column;
}

</style>