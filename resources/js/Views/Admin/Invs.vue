<template>
        <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading" />
        <Confirmation
         @dialog-closed="dialog = false"
         :confirmationText="dialogText"
         :onDeleteFunction="removeInv" 
         :dialogData="dialogData" 
         :dialog="dialog" 
         />
         <UserDialog :dialog="userDialog" :inv="inv" @user-dialog-closed="userDialog = false" />
        <v-row align="center" justify="center" v-if="!loading">
           <v-col lg="5" md="5">
               <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
           </v-col>
           <v-col lg="5" md="5">
               <v-text-field
                v-model="date"
                label="Date Filter"
                single-line
                hide-details
                type="date"
            ></v-text-field>
           </v-col>
           <v-col class="text-center" lg="2" md="2">
               <v-btn @click="date = ''; search = ''" class="mt-6" text>
                   <v-icon left>mdi-delete-outline</v-icon>clear search
               </v-btn>
           </v-col>
       </v-row>
       <v-row v-if="!loading">
           <v-col>
               <v-data-table :headers="headers" :items="filteredInvs" :search="search" sort-by="date_time">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template> 
                    <template v-slot:[`item.date`]="{ item }">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{ item }">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration: none" color="primary" icon small><v-icon small @click="showInvUsers(item)">mdi-account-group</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class="ml-1" color="info" icon small :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small >mdi-calendar-outline</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class = "ml-1" icon small :to="{name: 'edit-inv', params:{id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class = "ml-1" color="error" icon small @click="confirm(item)"><v-icon small>mdi-delete</v-icon></v-btn>
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
import UserDialog from '../../components/UsersDialog.vue'
export default {
    name: 'invs',
    components: {
        Loading, Alert, Confirmation, UserDialog
    },
    data()
    {
        return {
            loading: true,
            invs: [],
            user_invs: [],
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'dur', value: 'duration'},
                {text: 'course', value: 'course.code'},
                {text: 'room', value: 'room.number'},
                {text: 'department', value:'course.department.name'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'actions', value:'actions', sortable: false}
            ],
            search: '',
            date:'',
            
            dialog: false,
            dialogData: null,
            dialogText: null,

            userDialog: false,
            inv : {},

            alert: false,
            alertType: null,
            alertMessage: null,
        }
    },
    methods:{
        isExists(invId){
            let status = false
            this.user_invs.forEach((item) => {
                if (item.id === invId)
                    status = true
            })
            return status
        },
        updateInvs(){
            axios.get('/api/invs')
                .then((response) => {
                    this.invs = response.data
                })
                .catch((error) => {
                    console.log(error.response.message)
                })
        },
        addInv(invId){
            let formData = new FormData()
            formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
            formData.append('inv_id', invId)
            console.log(formData.get('user_id'), formData.get('inv_id'))
            axios.get('/sanctum/csrf-cookie')
            .then(response => {
                axios({
                    method: 'post',
                    url: '/api/users/addinv',
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.alertType = "success"
                    this.alertMessage = response.data.message
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.user_invs = this.$store.getters.getInvs
                    this.alert = true
                    this.updateInvs()
                })
                .catch((error) => {
                    this.alertType = "error"
                    this.alert = true
                    this.alertMessage = error.response.data.message
                })
            })
        },
        confirm(inv)
        {
            this.dialogData = inv.id
            this.dialog = true
            this.dialogText = `Are you sure you want to delete inv on\n${this.$options.filters.DateFormat(inv.date_time)} at ${this.$options.filters.TimeFormat(inv.date_time)}\n${inv.course.code} - ${inv.course.name}`
        },
        removeInv(invId) {
            this.dialog = false
            let formData = new FormData()
            formData.append('id', invId)
            axios({
                method: 'post',
                url: '/api/invs/delete',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.invs = response.data.invs
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        showInvUsers(inv){
            this.userDialog = true
            this.inv = inv
        }
    },
    mounted() {
        axios({
            method: 'get',
            url: '/api/invs',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.invs = response.data
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = "error"
            this.alertMessage = error.response.data.message
            this.loading = false
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
        },
    computed:{
        filteredInvs(){
             return this.invs.filter(inv => {
                    return inv.date_time.match(this.date)
                })
        }
    }
}
</script>
