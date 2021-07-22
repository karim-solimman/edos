<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around" v-if="!loading">
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">Add new inv</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-form>
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
                            deletable-chips
                            multiple 
                            item-value="id" 
                            v-model="selectedRooms" 
                            :items="rooms"
                            item-text="number"
                            label="Rooms">
                            </v-autocomplete>
                            <v-date-picker
                            class="my-3"
                            v-model="date"
                            full-width
                            ></v-date-picker>
                            <v-time-picker
                            class="my-3"
                            v-model="time"
                            ampm-in-title
                            format="ampm"
                            full-width
                            landscape
                            :allowed-minutes="allowedMinutes"
                            scrollable
                            ></v-time-picker>
                            <v-btn @click="test" block color="success"><v-icon left>mdi-plus</v-icon>Add new invs</v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components: {
        Alert, Loading
    },
    data(){
        return{
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null,
            courses: [],
            rooms: [],
            courseId: null,
            selectedRooms: [],
            date: null,
            time: null,
            allowedMinutes: [0],
        }
    },
    mounted(){
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
    },
    methods:{
        test() {
            console.log(this.courseId)
            console.log(this.date)
            console.log(this.time)
            console.log(this.selectedRooms)
        }
    }
    
}
</script>