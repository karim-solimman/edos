<template>
    <v-container>
        <v-row v-if="alert">
            <v-col>
                <v-alert
                    :type="alertType"
                    v-model="alert"
                    border="top"
                    elevation="6"
                >
                    {{alertMessage}}
                </v-alert>
            </v-col>
        </v-row>
        <Loading :loading="loading"/>
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat }}</h1>
                <h2 class="text-h5 font-weight-light">{{inv.date_time | TimeFormat}}</h2>
                <h3 class="text-caption">{{inv.room.number}} / {{inv.course.code}} / {{inv.course.name}} / {{inv.course.department.name}}</h3>
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
                            <v-btn color="error" style="text-decoration: none" small icon @click="removeUser(user.id)"><v-icon>mdi-close</v-icon></v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
    export default {
        components: {
            Loading
        },
        data(){
            return {
                invId : null,
                inv: {},
                loading: true,
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
                    this.inv.users = response.data.users
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
