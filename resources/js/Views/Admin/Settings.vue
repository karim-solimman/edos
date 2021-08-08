<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading">
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title>
                        <h2 class="text-h5 font-weight-light">Random distribution</h2>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            With random distribution, EDOS will automatically distribute users over all the invs <br/>
                            <strong>Be Attention</strong>, that users count must be greater than the greatest count of users required per inv period. <br/>
                            <strong>Be Attention</strong>, if the users count is less than the greatest count of users required per any inv period the system can't distribute.
                        </p>
                        <v-row>
                            <v-col>
                                <v-alert v-if="users < max_slot.users_count" border="top" colored-border elevation="2" type="error">
                                    can't run automatic distribution due to max number of <strong>users is {{users}}</strong> less than of max <strong>required {{max_slot.users_count}}</strong> <br/>
                                    <v-icon left small>mdi-table-eye</v-icon> {{max_slot.date_time | DateFormat}} <br/>
                                    <v-icon left small>mdi-clock</v-icon> {{max_slot.date_time | TimeFormat}}
                                </v-alert>
                                <v-alert v-else border="top" colored-border elevation="2" type="success">
                                    Automatic users distribution can be run
                                </v-alert>
                            </v-col>
                        </v-row>
                        <v-row v-if="users >= max_slot.users_count">
                            <v-col>
                                <p class="text-h6"> {{slots}}<span class="text-caption">slots</span> / {{users}}<span class="text-caption">users</span> = {{(slots/users).toFixed(2)}} <span class="text-caption">each user will have {{Math.floor((slots/users).toFixed(2))}} or {{Math.ceil((slots/users).toFixed(2))}} invs.</span></p>
                            </v-col>
                        </v-row>                        
                    </v-card-text>
                    <v-card-actions v-if="users >= max_slot.users_count">
                        <v-btn block color="success"><v-icon left>mdi-shuffle</v-icon> execute random distribution</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components:{
        Loading, Alert
    },
    data(){
        return{
            loading: true,
            max_slot: null,
            slots: null,
            users: null,

            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: 'api/invs/statistics',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.loading = false
            this.users = response.data.users
            this.slots = response.data.sum
            this.max_slot = response.data.max_slot
        })
        .catch((error) => {
            this.loading = false
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    },
    filters:{
            DateFormat(value)
            {
                return moment(value).format("ddd, MMM DD, YYYY") 
            },
            TimeFormat(value)
            {
                return moment(value).format("hh : mm A")
            },
            ago(value)
            {
                return moment(value).fromNow()
            }
        }
    
}
</script>