<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert=false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation @dialog-closed="dialog=false" :dialog="dialog" :dialogData="dialogData" :confirmationText="dialogText" :onDeleteFunction="dialogFunction" />
        <v-row v-if="!loading">
            <v-col>
                <h5 class="text-h5 font-weight-light">{{department.name}} <v-chip x-small>Edit</v-chip> </h5>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" lg="4" md="4">
                <v-card color="grey lighten-4" height="100%"> 
                    <v-card-title><h1 class="text-h5 font-weight-light">Update name</h1></v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="Name"
                        v-model="department.name"
                        ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="updateName" block text color="info"><v-icon left>mdi-pencil</v-icon>update name</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" lg="4" md="4">
                <v-card color="grey lighten-4" height="100%">
                   <v-card-title><h1 class="text-h5 font-weight-light">Flush all invs</h1></v-card-title>
                   <v-card-text>
                       <strong>Warning</strong>, By flushing all the invs you are detach all users from those invs and this action can't be undo.
                    </v-card-text>
                   <v-card-actions>
                       <v-btn @click="confirmFlushInvs(department)" color="error" block text><v-icon left>mdi-close</v-icon>Flsuh all invs</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card color="grey lighten-4" height="100%">
                    <v-card-title class="text-h5 font-weight-light">Delete department</v-card-title>
                    <v-card-text><strong>Warning</strong>, deleting / removing {{department.name}} department will remove all the invs of this department also will unlink all courses attached to the department and this can't be undo.</v-card-text>
                    <v-card-actions><v-btn @click="confirmDelete(department)" block color="error"><v-icon left>mdi-close</v-icon>Delete department</v-btn></v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col>
                <v-data-table :headers="headers" :items="department.courses">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        <span v-if="item.invs.length > 0">{{item.invs[0].date_time | DateFormat}}</span>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        <span v-if="item.invs.length > 0">{{item.invs[0].date_time | TimeFormat}}</span>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.rooms`]="{item}">
                        <v-chip-group v-if="item.invs.length > 0">
                            <v-chip small v-for="inv in item.invs" :key="inv.id">{{inv.room.number}}</v-chip>
                        </v-chip-group>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.actions`]="{}">
                        <v-btn icon small color="error"><v-icon>mdi-close</v-icon></v-btn>
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
            loading: true,
            department_id: this.$route.params.id,
            department: Object,
            headers:[
                {text: '#', value: 'index'},
                {text: 'code', value: 'code'},
                {text: 'name', value: 'name'},
                {text: 'credit hours', value: 'credit_hours'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'rooms', value: 'rooms'},
                {text: 'actions', value: 'actions'}
            ],

            alert: false,
            alertType: null,
            alertMessage: null,

            dialog: false,
            dialogData: null,
            dialogText: null,
            dialogFunction: null,

            countDown: 2,
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: `/api/departments/${this.department_id}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.department = response.data.department
            this.loading = false
        })
        .catch((error) => {
            console.log(error.response.data.message)
        })
    },
    methods:{
        confirmDelete(department)
        {
            this.dialog = true
            this.dialogData = department.id
            this.dialogText = `Are you sure you want to delete ${department.name} ?`
            this.dialogFunction = this.deleteDepartment
        },
        deleteDepartment(department_id)
        {
            let formData = new FormData()
            formData.append('department_id', department_id)
            axios({
                method: 'post',
                url: '/api/department/delete',
                data: formData,
                headers:{
                    Authorization: `Bearer  ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertMessage = response.data.message + " - Page will be redirected in " + this.countDown + " seconds."
                this.alertType = 'success'
                 setTimeout(() => {
                    this.$router.push({name: 'departments'})
                }, this.countDown * 1000 )
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        updateName()
        {
            let formData = new FormData()
            formData.append('department_id', this.department.id)
            formData.append('department_name', this.department.name)
            axios({
                method: 'post',
                url: '/api/department/updatename',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        confirmFlushInvs(department){
            this.dialog = true
            this.dialogData = department.id
            this.dialogFunction = this.flushInvs
            this.dialogText = `Are you sure you want to flush all invs from ${department.name}`
        },
        flushInvs(departmentId)
        {
            let formData = new FormData()
            formData.append('department_id', departmentId)
            axios({
                method: 'post',
                url: '/api/department/flushallinvs',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.department = response.data.department
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
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