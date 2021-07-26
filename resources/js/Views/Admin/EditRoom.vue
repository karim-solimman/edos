<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading && room">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{ room.number }} <v-chip x-small>edit</v-chip></h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room">
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title><h1 class="text-h5 font-weight-light">Change room information</h1></v-card-title>
                    <v-card-text>
                         <v-text-field
                            label="Room number"
                            v-model="room.number"
                            :rules="room_numberRules"
                            ></v-text-field>
                            <v-text-field
                            label="Users limit"
                            v-model="room.users_limit"
                            :rules="users_limitRules"
                            ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block text color="info"><v-icon left>mdi-pencil</v-icon>Update room number</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title><h1 class="text-h5 font-weight-light">Undetach all invs</h1></v-card-title>
                    <v-card-text><strong>Warning</strong>, by Undetach invs from {{room.number}}, all invs will be available but not linked to a room. This action can't be undo.</v-card-text>
                    <v-card-actions><v-btn block color="error" dark><v-icon left>mdi-close</v-icon>undetach all invs</v-btn></v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room.invs && room.invs.length > 0">
            <v-col>
                <v-data-table :headers="headers" :items="room.invs">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        {{item.date_time | TimeFormat }}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration: none" :to="{name: 'invProfile', params:{id: item.id}}" icon x-small><v-icon>mdi-pencil</v-icon></v-btn>
                        <v-btn style="text-decoration: none" icon x-small color="error"><v-icon>mdi-close</v-icon></v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room.invs && room.invs.length === 0">
            <v-col>
                <v-alert outlined color="warning">Sorry no invs to be displayed</v-alert>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components: {
        Loading, Alert
    },
    data(){
        return{
            room: Object,
            loading: true,
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'users count', value: 'users.length'},
                {text: 'course', value: 'course.code'},
                {text: 'department', value: 'course.department.name'},
                {text: 'actions', value: 'actions'}
            ],
            room_numberRules: [
                v => !!v || 'Room number is required',
                v => /[a-zA-Z][0-9][0-9][0-9]/.test(v) || 'Room number should contains E followed by 3 digits',
                v => (v && v.length === 4) || 'Room number should contains E followed by 3 digits',
            ],
            users_limitRules: [
                v => !!v || 'Users limit is required',
                v => /^\d+$/.test(v) || 'Users limit must be digits ',
            ],
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    mounted(){
        let roomId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/rooms/${roomId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.room = response.data.room
            this.loading = false
        })
        .catch((error) => {
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