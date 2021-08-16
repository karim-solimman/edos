<template>
    <v-container>
       <Loading :loading="loading" />
       <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
       <Confirmation
       @dialog-closed="dialog = false"
       :confirmationText="dialogText"
       :dialog="dialog"
       :dialogData="dialogData"
       :onDeleteFunction="dialogFunction" 
       />
        <v-row v-if="!loading">
            <v-col>
                <h5 class="text-h4 font-weight-light">
                    {{user.name}} <v-chip class="mr-2" x-small color="primary" v-for="role in user.roles" :key="role.id">{{role.name}}</v-chip>
                </h5>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" md=6 lg=6>
                <v-card color="grey lighten-5">
                    <v-card-title class="text-h5 font-weight-light">Update Information</v-card-title>
                    <v-form ref="info-form" @submit.prevent="updateInfo">
                    <v-card-text>
                        <v-text-field v-model="user.name" :error-messages="errorMessages['name']" label="Name" :value="user.name"></v-text-field>
                        <v-text-field v-model="user.email" :error-messages="errorMessages['email']" label="Email" :value="user.email"></v-text-field>
                    </v-card-text>
                     <v-card-actions>
                        <v-btn type="submit" color="primary" :loading="infoBtnLoading" block text><v-icon left>mdi-pencil</v-icon>update</v-btn>
                    </v-card-actions>
                     </v-form>
                    <v-divider></v-divider>
                    <v-card-title class="text-h5 font-weight-light">Password Reset</v-card-title>
                    <v-card-text>
                        <p class="text-body-1"><strong color="error">Warning</strong>, by reseting the user password, user won't be able to login again until new registeration is done
                        <strong>, user data and invs won't be removed</strong></p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="confirmResetPassword" color="error" block text><v-icon left>mdi-lock-reset</v-icon>reset</v-btn>
                    </v-card-actions>
                    <v-divider></v-divider>
                    <v-card-title class="text-h5 font-weight-light">Remove all users invs</v-card-title>
                    <v-card-text>
                        <p class="text-body-1"><strong color="error">Warning</strong>, This action can't be undo.
                        <strong>, User name, department, email and password will still the same!</strong></p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="confirmRemoveAllInvs" color="error" block text><v-icon left>mdi-delete-forever</v-icon>remove all invs</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card class="mb-5" color="grey lighten-5"> 
                    <v-card-title><h1 class="text-h5 font-weight-light">Change department</h1></v-card-title>
                    <v-card-text>
                        <v-select
                        :error-messages="errorMessages['department']"
                        label="Department"
                        :items="departments"
                        item-text="name"
                        item-value="id"
                        :value="user.department.id"
                        v-model="user.department.id"
                        >
                        </v-select>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn :loading="depBtnLoading" @click="updateDepartment" color="primary" block text><v-icon left>mdi-pencil</v-icon>change</v-btn>
                    </v-card-actions>
                </v-card>
                <v-card class="mb-5" color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Delete User</h1>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            <strong>Warning</strong>, deleting user will detach all invs from this user before deleting.
                            Deleting user will remove all related data to the user and <strong>this action can't be undo.</strong>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="confirmDeleteUser" block color="error"><v-icon left>mdi-delete-outline</v-icon>Delete user</v-btn>
                    </v-card-actions>
                </v-card>
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">User Invs</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-data-table v-if="user.invs && user.invs.length > 0" :headers="headers" :items="user.invs">
                            <template v-slot:[`item.index`]="{index}">
                                {{index+1}}
                            </template>
                            <template v-slot:[`item.date`]="{item}">
                                {{item.date_time | DateFormat}}
                            </template>
                            <template v-slot:[`item.time`]="{item}">
                                {{item.date_time | TimeFormat}}
                            </template>
                            <template v-slot:[`item.actions`]="{item}">
                                <v-btn style="text-decoration: none" color="info" small icon :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small>mdi-calendar-outline</v-icon></v-btn>
                                <v-btn style="text-decoration: none" color="error" small icon><v-icon small @click="confirmRemoveInv(item)">mdi-delete</v-icon></v-btn>
                            </template>
                        </v-data-table>
                        <p v-else class="text-body-1">No invs to be displayed!</p>
                    </v-card-text>
                </v-card>
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
            user: Object,
            departments: [],
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'room', value: 'room.number'},
                {text: 'actions', value: 'actions'}
            ],

            alert: false,
            alertMessage: null,
            alertType: null,

            dialog: false,
            dialogData: null,
            dialogText: null,
            dialogFunction: null,

            infoBtnLoading: false,
            depBtnLoading: false,
            errorMessages: [
                {name: ''},
                {email: ''},
                {department: ''}
            ],
            countDown: 2
        }
    },
    mounted(){
        let userId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/users/${userId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response)=>{
            this.user = response.data.user
            if(this.user.department == null) {
                this.user.department = {id: 1}
            }
            this.departments = response.data.departments
            this.loading = false
        })
    },
    methods:{
        updateInfo(){
            this.infoBtnLoading = true
            let formData = new FormData()
            formData.append('user_id', this.user.id)
            formData.append('user_name', this.user.name)
            formData.append('user_email', this.user.email)
            
            axios({
                method: 'post',
                url: '/api/users/updateinformation',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => { 
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.infoBtnLoading = false
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.errorMessages['name'] = error.response.data.errors['user_name']
                this.errorMessages['email'] = error.response.data.errors['user_email']
                this.infoBtnLoading = false
            })
            
        },
        updateDepartment(){
            this.depBtnLoading = true
            let formData = new FormData()
            formData.append('user_id', this.user.id)
            formData.append('department_id', this.user.department.id)
            axios({
                method: 'post',
                url: '/api/users/updatedepartment',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.depBtnLoading = false
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.depBtnLoading = false
            })
        },
        confirmRemoveInv(inv){
            this.dialog = true
            this.dialogData = inv.id
            this.dialogFunction = this.removeInv
            this.dialogText = `Are you sure you want to delete inv on\n${this.$options.filters.DateFormat(inv.date_time)} at ${this.$options.filters.TimeFormat(inv.date_time)}`
        },
        removeInv(invId){
            let formData = new FormData()
                formData.append('user_id', this.user.id)
                formData.append('inv_id', invId)
                axios({
                    method: 'post',
                    url: '/api/users/removeinv',
                    data: formData,
                    headers:{
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.alert = true
                    this.alertMessage = response.data.message
                    this.alertType = "success"
                    this.user.invs = response.data.invs
                })
                .catch((error)=>{
                    this.alert = true
                    this.alertMessage = error.response.data.message
                    this.alertType = "error"
                })
        },
        confirmRemoveAllInvs()
        {
            this.dialog = true
            this.dialogText = `Are you sure you want to delete all invs for ${this.user.name}?`
            this.dialogData = this.user.id
            this.dialogFunction = this.removeAllInvs
        },
        removeAllInvs(){
            axios({
                method: 'get',
                url: `/api/users/deletealluserinvs/${this.user.id}`,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertMessage = response.data.message
                this.alertType = 'success'
                this.user.invs = []
            })
            .catch((error) => {
                this.alert = true
                this.alertMessage = error.response.data.message
                this.alertType = 'error'
            })
        },
        confirmResetPassword(){
            this.dialog = true
            this.dialogText = `Are you sure you want to reset password for ${this.user.name}?`
            this.dialogData = this.user.id
            this.dialogFunction = this.resetPassword
        },
        resetPassword(){
            axios({
                method: 'get',
                url: `/api/users/resetpassword/${this.user.id}`,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response)=>{
                this.alert = true
                this.alertMessage = response.data.message
                this.alertType = 'success'
            })
            .catch((error)=>{
                this.alert = true
                this.alertMessage = error.response.data.message
                this.alertType = 'error'
            })
        },
        confirmDeleteUser(){
            this.dialog = true
            this.dialogText = 'Are you sure you want to delete ' + this.user.name + ' ?'
            this.dialogData = this.user
            this.dialogFunction = this.deleteUser
        },
        deleteUser(user){
            let formData = new FormData()
            formData.append('user_id', user.id)
            axios({
                method: 'post',
                url: '/api/users/deleteuser',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message + " - Page will be redirected in " + this.countDown + " seconds."
                setTimeout(() => {
                    this.$router.push({name: 'users'})
                }, this.countDown * 1000 )
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
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