<template>
    <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading"/>
        <Confirmation
        @dialog-closed="dialog = false"
        :confirmationText="'Are you sure you want to detach user?'"
        :onDeleteFunction="removeUser" 
        :dialogData="dialogData" 
        :dialog="dialog" 
        />        
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat }}</h1>
                <h2 class="text-h5 font-weight-light">{{inv.date_time | TimeFormat}} <v-chip outlined small><v-icon left>mdi-alarm</v-icon>{{inv.duration}} Hours</v-chip> </h2>
                <v-chip-group column>
                    <v-chip style="text-decoration: none" outlined :to="{name: 'roomProfile', params:{id: inv.room.id}}"><v-icon left>mdi-door</v-icon>{{inv.room.number}}</v-chip>
                    <v-chip style="text-decoration: none" outlined :to="{name: 'courseProfile', params:{id: inv.course.id}}"><v-icon left>mdi-book-open-page-variant-outline</v-icon>{{inv.course.code}} - {{inv.course.name}}</v-chip>
                    <v-chip style="text-decoration: none" outlined :to="{name: 'departmentProfile', params:{id: inv.course.department.id}}"><v-icon left>mdi-folder</v-icon>{{inv.course.department.name}}</v-chip>
                </v-chip-group>
            </v-col>
        </v-row>
        <v-divider></v-divider>
        <v-row v-if="inv.users && inv.users.length === 0">
            <v-col>
              <v-alert
                    outlined
                    dense
                    type="warning"
                    border="left"
                >
                    Sorry, no users to be displayed at this inv
                </v-alert>
            </v-col>
        </v-row>
        <v-row v-if="!loading && inv.users && inv.users.length > 0">
            <v-col>
                <v-simple-table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>name</th>
                        <th>email</th>
                        <th>date</th>
                        <th>actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(user, i) in inv.users" :key="i">
                        <td>{{i+1}}</td>
                        <td>{{user.name}}</td>
                        <td>{{user.email}}</td>
                        <td>{{user.pivot.created_at | ago}}</td>
                        <td>
                            <v-btn style="text-decoration: none" small icon :to="{name: 'userProfile', params:{id: user.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                            <v-btn color="error" style="text-decoration: none" small icon @click="confirm(user.id)"><v-icon>mdi-close</v-icon></v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Alert from '../../components/Alert.vue'
import Loading from '../../components/Loading.vue'
import Confirmation from '../../components/Confirmation.vue'
    export default {
        components: {
            Loading, Alert, Confirmation
        },
        data(){
            return {
                invId : null,
                inv: {},
                loading: true,
                dialog: false,
                dialogData: null,
                alert: false,
                alertType: '',
                alertMessage: ''
            }
        },
        mounted() {
            this.invId = this.$route.params.id
            axios({
                method: 'get',
                url: `/api/invs/${this.invId}`,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.inv = response.data
                this.loading = false
            })
            .catch((error) => {
                this.alert = true
                this.alertType = "error"
                this.alertMessage = error.response.data.message
            })
        },
        methods:{
            confirm(userId){
                this.dialogData = userId
                this.dialog = true
            },
            removeUser(userId){
                let formData = new FormData()
                formData.append('user_id', userId)
                formData.append('inv_id', this.invId)
                axios({
                    method: 'post',
                    url: '/api/users/removeinv',
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.alert = true
                    this.alertType = "success"
                    this.alertMessage = response.data.message
                    this.inv.users = response.data.inv.users
                })
                .catch((error) => {
                    this.alert = true
                    this.alertType = "error"
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
