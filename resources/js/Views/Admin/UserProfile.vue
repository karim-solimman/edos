<template>
 <v-container>
     <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
     <Loading :loading="loading" />
     <Confirmation
     @dialog-closed="dialog = false"
     :confirmationText="dialogText"
     :dialog="dialog"
     :dialogData="dialogData"
     :onDeleteFunction="dialogFunction"
     />
    <v-row v-if="!loading && user">
        <v-col cols="5">
            <h1 class="text-h4 font-weight-light"> {{user.name}}</h1>
            <h2 class="text-overline">{{user.email}}</h2>
            <v-chip small color="primary" class="text-overline mr-2" v-for="role in user.roles" :key="role.id"><v-icon small left>mdi-account</v-icon>{{role.name}}</v-chip>
            <v-chip small outlined class="text-overline"><v-icon small left>mdi-folder</v-icon>
                    <span v-if="user.department">{{user.department.name}}</span>
                    <span v-else>No department</span>
                </v-chip>
        </v-col>
        <v-col>
            <h1 class="text-h1 font-weight-light">{{user.invs.length}}<span class="text-overline">invs</span></h1>
        </v-col>
    </v-row>
    <v-divider></v-divider>
    <v-row v-if="!loading && user.invs && user.invs.length === 0">
        <v-col>
            <v-alert border="top" colored-border type="warning" elevation="2">
                Sorry, No invs to be displayed attached for {{user.name}}!
            </v-alert>
        </v-col>
    </v-row>
    <v-row v-if="!loading && user.invs && user.invs.length > 0">
        <v-col>
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Inv Date</th>
                            <th>Inv Time</th>
                            <th>Room</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(inv, i) in user.invs" :key="i">
                            <td>{{i+1}}</td>
                            <td>{{inv.date_time | DateFormat}}</td>
                            <td>{{inv.date_time | TimeFormat}}</td>
                            <td>{{inv.room.number}}</td>
                            <td>{{inv.course.code}}</td>
                            <td>{{inv.course.department.name}}</td>
                            <td>{{inv.pivot.created_at | ago}}</td>
                            <td>
                                <v-btn style="text-decoration: none" icon color="info" small :to="{name: 'invProfile', params: {id:  inv.id}}" ><v-icon small>mdi-calendar</v-icon></v-btn>
                                <v-btn @click="confirm(inv)" color="error" icon><v-icon small>mdi-delete</v-icon></v-btn>
                            </td>
                        </tr>
                    </tbody>
                </template>
            </v-simple-table> 
        </v-col>
    </v-row>
</v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
import Confirmation from '../../components/Confirmation.vue'
    export default {
        name: 'user-profile',
        components: {
            Loading, Alert, Confirmation
        },
        data(){
            return{
                loading: true,
                user: Object,
                alert: false,
                alertMessage: null,
                alertType: null,
                dialog: false,
                dialogData: null,
                dialogFunction: null,
                dialogText: null
            }
        },
        mounted() {
            this.id = this.$route.params.id
            axios({
                method: 'get',
                url: `/api/users/${this.id}`,
                params: {id: this.id},
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.loading = false
                this.user = response.data.user
                $.each(this.user.roles)
            })
            .catch((error) => {
                this.loading = false
                this.errorMessage = error.response.data.message
            })
        },
        methods:{
            confirm(inv){
                this.dialog = true
                this.dialogData = inv.id
                this.dialogFunction = this.removInv
                this.dialogText = `Are you sure you want to remove inv on\n${this.$options.filters.DateFormat(inv.date_time)} at ${this.$options.filters.TimeFormat(inv.date_time)}`
            },
            removInv(invId){
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
