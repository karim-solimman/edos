<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around" v-if="!loading">
            <v-col cols="12" lg="12" md="12">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">Add new inv/s</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="addInvs" ref="form">
                            <v-row>
                                <v-col cols="12" lg="6" md="6">
                                    <v-autocomplete 
                                    item-value="id" 
                                    v-model="courseId"
                                    ref="course"
                                    :rules="courseIdRules"
                                    :error-messages="errorMessages['course']" 
                                    :items="courses"
                                    :item-text="item => item.code + ' - ' + item.name"
                                    label="Course">
                                    </v-autocomplete>
                                    <v-autocomplete
                                    chips
                                    clearable
                                    deletable-chips
                                    ref="rooms"
                                    multiple 
                                    item-value="id" 
                                    v-model="selectedRooms"
                                    :rules="selectedRoomsRules"
                                    :items="rooms"
                                    hide-selected
                                    no-data-text="No other rooms to be displayed"
                                    :error-messages="errorMessages['rooms']"
                                    item-text="number"
                                    label="Rooms">
                                    </v-autocomplete>
                                    <v-text-field
                                    label="Duration"
                                    v-model="duration"
                                    :rules="durationRules"
                                    :error-messages="errorMessages['duration']"
                                    type="number"
                                    >
                                    </v-text-field>
                                    <v-time-picker
                                    class="my-3"
                                    v-model="time"
                                    ampm-in-title
                                    format="ampm"
                                    full-width
                                    :landscape="!$vuetify.breakpoint.mobile"
                                    :allowed-minutes="allowedMinutes"
                                    scrollable
                                    required
                                    ></v-time-picker>
                                </v-col>
                                <v-col cols="12" lg="6" md="6">
                                    <v-date-picker
                                    class="my-3"
                                    v-model="date"
                                    full-width
                                    required
                                    ></v-date-picker>
                                    <v-btn type="submit" block color="success"><v-icon left>mdi-plus</v-icon>Add new invs</v-btn>
                                </v-col>
                            </v-row>
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
            btnLoading: false,
            alert: false,
            alertType: null,
            alertMessage: null,
            courses: [],
            rooms: [],
            courseId: null,
            courseIdRules: [
                v => !!v || 'Course is required',
            ],
            selectedRooms: [],
            selectedRoomsRules: [
                v => this.selectedRooms.length > 0 || 'At lease one room is required',
            ],
            duration: '',
            durationRules: [
                v => /^\d+$/.test(v) || 'Duration must be digits ',
                v => (v > 0 && v <= 5) || 'Duration period is not acceptable'
            ],
            date: null,
            time: null,
            allowedMinutes: [0, 30],
            errorMessages: [
                {course: ''},
                {rooms: ''},
                {duration: ''}
            ],
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
        addInvs() {
            this.btnLoading = true
            let formData = new FormData()
            formData.append('course_id', this.courseId)
            formData.append('rooms', Array(this.selectedRooms))
            formData.append('date', this.date)
            formData.append('time', this.time)
            formData.append('duration', this.duration)
            axios({
                method: 'post',
                url: '/api/invs/create',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.btnLoading = false
                //reset form
                this.$refs.form.reset()
                this.date = null
                this.time = null
                
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.errorMessages['course'] = error.response.data.errors['course_id']
                this.errorMessages['rooms'] = error.response.data.errors['rooms']
                this.errorMessages['duration'] = error.response.data.errors['duration']
                this.btnLoading = false
            })
        }
    }
    
}
</script>