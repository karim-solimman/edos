<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around" v-if="!loading">
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">Add new inv/s</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="addInvs" ref="form">
                            <v-autocomplete 
                            item-value="id" 
                            v-model="courseId"
                            prepend-icon="mdi-book"
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
                            prepend-icon="mdi-door"
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
                            hint="Optional"
                            persistent-hint
                            prepend-icon="mdi-timer"
                            v-model="duration"
                            :rules="durationRules"
                            :error-messages="errorMessages['duration']"
                            type="number"
                            >
                            </v-text-field>

                            <v-menu
                            ref="time"
                            v-model="timeMenu"
                            :close-on-content-click="false"
                            :return-value.sync="time"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                            >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="time"
                                    label="Time"
                                    prepend-icon="mdi-clock"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                            v-model="time"
                            ampm-in-title
                            format="ampm"
                            landscape
                            :allowed-minutes="allowedMinutes"
                            scrollable
                            >
                            <v-spacer></v-spacer>
                            <v-btn
                                text
                                color="primary"
                                @click="timeMenu = false"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                text
                                color="primary"
                                @click="$refs.time.save(time)"
                            >
                                OK
                            </v-btn>
                            </v-time-picker>
                            </v-menu>
                            <v-menu
                            v-model="dateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="date"
                                        label="Date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                v-model="date"
                                @input="dateMenu = false"
                                ></v-date-picker>
                            </v-menu>
                            <v-btn type="submit" block color="success"><v-icon left>mdi-plus</v-icon>Add new invs</v-btn>
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
            duration: null,
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

            dateMenu: false,
            timeMenu: false,
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
            formData.append('duration', this.duration ? this.duration : 0 )
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