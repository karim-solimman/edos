<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
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
                            label="Course">
                            </v-autocomplete>
                            <v-autocomplete
                            chips
                            clearable
                            item-value="id" 
                            v-model="room" 
                            :items="rooms"
                            item-text="number"
                            label="Rooms">
                            </v-autocomplete>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block color="primary"><v-icon left>mdi-pencil</v-icon>edit information</v-btn>
                    </v-card-actions>
                </v-card>
                 <v-card class="mt-12" color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Delete inv</h1>
                    </v-card-title>
                    <v-card-text>
                        <p><strong>Warning</strong>, by deleting the inv all users attached to this inv will be cleared from inv resgitration, this action can't be undo</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block color="error"><v-icon left>mdi-close</v-icon>delete inv</v-btn>
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
               
            </v-col>
            <v-col cols="12" lg="6" md="6">
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
export default {
    components:{
        Alert, Loading
    },
    data(){
        return{
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null,
            inv: Object,
            date: null,
            time: null,
            room: null,
            allowedMinutes: [0],
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