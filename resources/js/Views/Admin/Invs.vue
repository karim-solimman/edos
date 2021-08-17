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
           <v-col cols="12" lg="4" md="4">
               <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
           </v-col>
           <v-col cols="12" lg="4" md="4">
               <v-text-field
                v-model="date"
                label="Date Filter"
                single-line
                hide-details
                type="date"
            ></v-text-field>
           </v-col>
           <v-col class="text-center d-flex flex-row justify-center" cols="6" lg="2" md="2">
               <v-tooltip bottom>
                   <template v-slot:activator="{ on, attrs }">
                       <v-btn v-bind="attrs" v-on="on" color="error" icon @click="date = ''; search = ''" class="mt-6" text>
                            <v-icon>mdi-delete-outline</v-icon>
                        </v-btn>
                   </template>
                   <span>Clear Search</span>
               </v-tooltip>
               
               <v-tooltip bottom>
                   <template v-slot:activator="{ on, attrs }">
                       <vue-excel-xlsx filename="Admin Invs" :data="export_data" :columns="export_fields">
                            <v-btn v-on="on" v-bind="attrs" class="mt-6 ml-3" color="success" icon><v-icon>mdi-microsoft-excel</v-icon></v-btn>
                       </vue-excel-xlsx>
                   </template>
                   <span>Excel Download</span>
               </v-tooltip>
                
           </v-col>
           <v-col class="my-auto mt-5" cols="6" lg="2" md="2">
               <v-switch prepend-icon="mdi-account-group" v-model="toggleUsers" inset hint="Show Users" persistent-hint>
               </v-switch>
           </v-col>
       </v-row>
       <v-row v-if="!loading && !toggleUsers">
           <v-col>
               <v-data-table @current-items="exportData" :headers="headers" :items="filteredInvs" :search="search" sort-by="date_time">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template> 
                    <template v-slot:[`item.date`]="{ item }">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{ item }">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.duration`]="{ item }">
                        <span v-if="item.duration">{{item.duration}}</span>
                        <span style="color: red" v-else> 0 </span>
                    </template>
                    <template v-slot:[`item.department`]="{ item }">
                        <span v-if="item.course.department">{{item.course.department.name}}</span>
                        <span style="color: red" v-else> No department </span>
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

       <v-row v-if="!loading && toggleUsers">
           <v-col>
               <v-data-table @current-items="exportData" :headers="headersUsers" :items="filteredInvs" :search="search" sort-by="date_time">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template> 
                    <template v-slot:[`item.date`]="{ item }">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{ item }">
                        {{item.date_time | TimeFormat}}
                    </template>
                     <template v-slot:[`item.department`]="{ item }">
                        <span v-if="item.course.department">{{item.course.department.name}}</span>
                        <span style="color: red" v-else> No department </span>
                    </template>
                     <template v-slot:[`item.users`]="{ item }">
                         <v-chip-group column>
                             <v-chip
                             v-for="user in item.users" :key="user.id"
                             :to="{name: 'userProfile', params:{id: user.id}}"
                             style="text-decoration: none"
                             small
                             >{{user.name}}</v-chip>
                         </v-chip-group>
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
                {text: 'department', value: 'course.department.name'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'actions', value:'actions', sortable: false}
            ],

            headersUsers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'course', value: 'course.code'},
                {text: 'room', value: 'room.number'},
                {text: 'department', value:'course.department.name'},
                {text: 'users', value:'users', sortable: false}
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

            toggleUsers: false,

            export_fields:
            [
                {
                    label: "#",
                    field: "index"
                },
                {
                    label: "date",
                    field: "date"
                },
                {
                    label: "time",
                    field: "time"
                },
                {
                    label: "duration",
                    field: "duration"
                },
                {
                    label: "code",
                    field: "code"
                },
                {
                    label: "course",
                    field: "course"
                },
                {
                    label: "room",
                    field: "roomnumber"
                },
                {
                    label: "department",
                    field: "department"
                },
                {
                    label: "users count",
                    field: "users_count"
                },
                {
                    label: "users limit",
                    field: "users_limit"
                },
                {
                    label: "inv 1",
                    field: "user_01"
                },
                {
                    label: "inv 2",
                    field: "user_02"
                },
                {
                    label: "inv 3",
                    field: "user_03"
                },
                {
                    label: "inv 4",
                    field: "user_04"
                },
                {
                    label: "inv 5",
                    field: "user_05"
                }
            ]
            ,
            export_data:[]
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
            this.dialogText = `Are you sure you want to delete inv on\n${this.$options.filters.DateFormat(inv.date_time)} at ${this.$options.filters.TimeFormat(inv.date_time)} for ${inv.course.code} - ${inv.course.name}`
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
        },
        exportData(data){
            this.export_data = []
            let users = []
            $.each(data, (index, value) => {
                users = []
                $.each(value.users,(index, value) => {
                    users.push(value.name)
                })
                    this.export_data.push({
                        'index': index+1,
                        'date': this.$options.filters.DateFormat(value.date_time),
                        'time': this.$options.filters.TimeFormat(value.date_time),
                        'duration': value.duration? value.duration : "No Duration",
                        'code': value.course.code,
                        'course': value.course.name,
                        'roomnumber': value.room.number,
                        'department': value.course.department? value.course.department.name: 'No department',
                        'users_count': value.users_count,
                        'users_limit': value.users_limit,
                        'user_01': users[0],
                        'user_02': users[1],
                        'user_03': users[2],
                        'user_04': users[3],
                        'user_05': users[4],
                    })
                })
        },
    },
    beforeMount() {
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
    },
}
</script>
