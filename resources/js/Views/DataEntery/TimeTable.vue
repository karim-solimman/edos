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
          <v-row v-if="!loading && !isDataEntery">
           <v-col>
               <v-alert border="left" color="orange" text type="error" elevation="2">
                   <strong>Sorry</strong>, Data Entery Role is turned off for you.
               </v-alert>
           </v-col>
       </v-row>
        <v-row align="center" justify="center" v-if="!loading && isDataEntery">
           <v-col cols="12" lg="5" md="5">
               <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
           </v-col>
           <v-col cols="12" lg="5" md="5">
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
                       <vue-excel-xlsx :filename="`${$store.getters.getUser.department.name} TimeTable`" :data="export_data" :columns="export_fields">
                            <v-btn v-on="on" v-bind="attrs" class="mt-6 ml-3" color="success" icon><v-icon>mdi-microsoft-excel</v-icon></v-btn>
                       </vue-excel-xlsx>
                   </template>
                   <span>Excel Download</span>
               </v-tooltip>
           </v-col>
       </v-row>
       <v-row v-if="!loading && isDataEntery">
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
                    <template v-slot:[`item.actions`]="{item}">
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
export default {
    name: 'invs',
    components: {
        Loading, Alert, Confirmation,
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
                
            ]
            ,
            export_data:[],

            isDataEntery: false
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
                this.invs = this.invs.filter((item) => {
                    return item.id != invId
                })
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
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
                        'department': value.course.department.name
                    })
                })
        },
    },
    beforeMount() {
        let formData = new FormData()
        let roles = []
            formData.append('user_id', this.$store.getters.getUser.id)
            axios({
                method: 'post',
                url: '/api/profile',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                $.each(response.data.roles, (index, value) => {
                    roles.push(value.slug)
                })
                this.$store.dispatch('updateRoles', roles)
                this.$store.dispatch('updateSettings', response.data.settings)
                if (roles.includes('de'))
                {
                    this.isDataEntery = true
                }
                this.loading = false
            })
            .catch((error) => {
                this.$router.push('/')
                this.$emit('logged-out', false)
                localStorage.clear()
            })
    },
    mounted(){
        let formData = new FormData()
        formData.append('user_id', this.$store.getters.getUser.id)
        axios({
            method: 'post',
            url: '/api/invs/getbydepartment',
            data: formData,
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
