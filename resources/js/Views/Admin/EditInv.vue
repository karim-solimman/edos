<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation
         @dialog-closed="dialog = false"
         :confirmationText="dialogText"
         :onDeleteFunction="dialogFunction" 
         :dialogData="dialogData" 
         :dialog="dialog"
         />
        <v-row v-if="!loading && inv">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat }}</h1>
                <h2 class="text-h5 font-weight-light">{{inv.date_time | TimeFormat}}</h2>
                <h3 class="text-caption">{{inv.room.number}} / {{inv.course.code}} / {{inv.course.name}} / {{inv.course.department.name}}</h3>
            </v-col>
        </v-row>
        <v-row v-if="!loading && inv">
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-4">
                    <v-card-title><h1 class="text-h5 font-weight-light">Edit information</h1></v-card-title>
                    <v-card-text>
                        <v-autocomplete 
                            item-value="id" 
                            v-model="courseId"
                            :items="courses"
                            :item-text="item => item.code + ' - ' + item.name"
                            prepend-inner-icon="mdi-book"
                            label="Course">
                            </v-autocomplete>
                            <v-autocomplete
                            chips
                            clearable
                            item-value="id" 
                            prepend-inner-icon="mdi-door"
                            v-model="room" 
                            :items="rooms"
                            item-text="number"
                            label="Room">
                            </v-autocomplete>
                            <v-text-field
                            label="users limit"
                            prepend-inner-icon="mdi-account-off"
                            v-model="users_limit"
                            type="number"
                            >
                            </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block color="primary"><v-icon left>mdi-pencil</v-icon>edit information</v-btn>
                    </v-card-actions>
                </v-card>
                 <v-card class="mt-6" color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Delete inv</h1>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1"><strong>Warning</strong>, by deleting the inv all users attached to this inv will be cleared from inv resgitration, this action can't be undo</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="confirmInv(inv.id)" block color="error"><v-icon left>mdi-delete</v-icon>delete inv</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Change date</h1>
                    </v-card-title>
                    <v-card-text>
                         <v-date-picker
                            class="my-3"
                            v-model="date"
                            full-width
                            :show-current="false"
                            ></v-date-picker>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block color="primary"><v-icon left>mdi-pencil</v-icon>Change date</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" lg="6" md="6">
              <v-card color="grey lighten-5">
                  <v-card-title><h1 class="text-h5 font-weight-light">Users</h1></v-card-title>
                  <v-card-text>
                      <v-simple-table v-if="!loading && inv.users && inv.users.length > 0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Dept</th>
                                    <th>Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, i) in inv.users" :key="i">
                                    <td>{{i+1}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.department.name}}</td>
                                    <td>
                                        <v-btn color="success" style="text-decoration: none" small icon :to="{name: 'userProfile', params:{id: user.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                                        <v-btn color="error" style="text-decoration: none" small icon @click="confirmUser(user.id)"><v-icon>mdi-close</v-icon></v-btn>
                                    </td>  
                                </tr>
                            </tbody>
                    </v-simple-table>
                    <p class="text-body2" v-else>Sorry, no users to be displayed</p>
                  </v-card-text>
              </v-card>
            </v-col>
            <v-col style="margin-top: -4%;" cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Change time</h1>
                    </v-card-title>
                    <v-card-text>
                         <v-time-picker
                            class="my-3"
                            v-model="time"
                            ampm-in-title
                            format="ampm"
                            landscape
                            :allowed-minutes="allowedMinutes"
                            scrollable
                            ></v-time-picker>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block color="primary"><v-icon left>mdi-pencil</v-icon>Change time</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
import Confirmation from '../../components/Confirmation.vue'
export default {
    components:{
        Alert, Loading, Confirmation
    },
    data(){
        return{
            loading: true,
            dialogText: '',
            dialogFunction: null,
            dialog: false,
            dialogData: null,
            alert: false,
            alertType: null,
            alertMessage: null,
            inv: {},
            courseId: null,
            date: null,
            time: null,
            room: null,
            users_limit: null,
            allowedMinutes: [0, 30],
            courses: [],
            rooms: [],
        }
    },
    mounted(){
        let invId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/invs/${invId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.inv = response.data
            this.courseId = this.inv.course.id
            this.date = this.inv.date_time
            this.users_limit = this.inv.users_limit
            this.room = this.inv.room.id
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
            this.loading = false
        })
        axios({
            method: 'get',
            url: '/api/rooms',
            headers: {
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
            this.loading = false
        })
        axios({
            method: 'get',
            url: '/api/courses',
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.courses = response.data.courses
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
            this.loading = false
        })

    },
    methods:{
        confirmInv(invId){
            this.dialog = true
            this.dialogData = invId
            this.dialogText = 'Are you sure you want to delete the inv?'
            this.dialogFunction = this.removeInv
        },
        confirmUser(userId){
            this.dialog = true
            this.dialogData = userId
            this.dialogText = 'Are you sure you want to detach the user?'
            this.dialogFunction = this.removeUser
        },
        removeUser(userId){
            let formData = new FormData()
            formData.append('user_id', userId)
            formData.append('inv_id', this.inv.id)
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
                setTimeout(() => {
                    this.$router.push({name: 'admin-invs'})
                }, 2000)
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