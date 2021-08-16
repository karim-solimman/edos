<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation @dialog-closed="dialog = false" :confirmationText="dialogText" :dialog="dialog" :dialogData="dialogData" :onDeleteFunction="dialogFunction" />
        <v-row v-if="!loading && room">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{ room.number }}</h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room && room.invs">
            <v-col>
                <v-data-table :headers="headers" :items="room.invs">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.users_count`]="{item}">
                        {{item.users.length}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration: none" icon small color="info" :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small>mdi-door</v-icon></v-btn>
                        <v-btn style="text-decoration: none" icon small color="error" class="ml-2" @click="confirm(item)"><v-icon small>mdi-delete</v-icon></v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
import Confirmation from '../../components/Confirmation.vue'
export default {
    components: {
        Loading, Alert, Confirmation
    },
    data(){
        return{
            room: Object,
            loading: true,

            alert: false,
            alertType: null,
            alertMessage: null,

            headers: [
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'duration', value: 'duration'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'course', value: 'course.code'},
                {text: 'department', value: 'course.department.name'},
                {text: 'actions', value: 'actions'}
            ],

            dialog: false,
            dialogText: null,
            dialogFunction: null,
            dialogData: null
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
    methods:{
        confirm(item){
            this.dialog = true
            this.dialogData = item.id
            this.dialogText = "Are you sure you want to delete inv on " + this.$options.filters.DateFormat(item.date_time) + " ?"
            this.dialogFunction = this.removeInv
        },
        removeInv(invId){

        }
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