<template>
    <v-container>
        <Loading :loading="loading"/>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation
        @dialog-closed="dialog=false"
        :dialog="dialog"
        :dialogData="dialogData"
        :confirmationText="dialogText"
        :onDeleteFunction="dialogFunction"
        >
        </Confirmation>

         <v-row>
           <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
       </v-row>
        <v-row v-if="!loading && rooms">
            <v-col>
            <v-data-table :headers="headers" :items="rooms" :search="search">
                <template v-slot:[`item.index`]="{index}">
                    {{index+1}}
                </template>
                <template v-slot:[`item.actions`]="{item}">
                    <v-btn color="info" style="text-decoration: none" icon small :to="{name: 'roomProfile', params:{id: item.id}}"><v-icon small>mdi-door</v-icon></v-btn>
                    <v-btn style="text-decoration: none" icon small :to="{name: 'editRoom', params:{id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
                    <v-btn @click="confirmDelete(item)" color="error" style="text-decoration: none" icon small><v-icon small>mdi-delete</v-icon></v-btn>
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
    components:{
        Loading, Alert, Confirmation
    },
    data(){
        return{
            rooms: Array,
            loading: true,

            alert: false,
            alertType: null,
            alertMessage: null,

            headers:[
                {text: '#', value: 'index'},
                {text: 'number', value:'number'},
                {text: 'invs count', value: 'invs_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'actions', value: 'actions'}
            ],
            search: '',

            dialog: false,
            dialogData: null,
            dialogText: null,
            dialogFunction: null,
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: '/api/rooms',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.rooms = response.data.rooms
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    },
    methods:{
        confirmDelete(room){
            this.dialog = true
            this.dialogData = room.id
            this.dialogText = `Are you sure you want to delete room ${room.number} ?`
            this.dialogFunction = this.deleteRoom
        },
        deleteRoom(roomId){
            let formData = new FormData()
            formData.append('room_id', roomId)
            axios({
                method: 'post',
                url: '/api/rooms/delete',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }   
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.rooms = this.rooms.filter((item) => {
                    return item.id != roomId
                })
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        }
    },
    
}
</script>