<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation @dialog-closed="dialog = false" :confirmationText="dialogText" :dialog="dialog" :dialogData="dialogData" :onDeleteFunction="dialogFunction" />
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{course.code}} - {{course.name}}</h1>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-calendar</v-icon>{{course.invs[0].date_time | DateFormat}}</v-chip>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-alarm</v-icon>{{course.invs[0].date_time | TimeFormat}}</v-chip>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-clock</v-icon>{{course.invs[0].duration}} Hours</v-chip>
                <v-chip class="text-overline" outlined v-else>No invs</v-chip>
                <v-chip class="text-overline my-1" outlined><v-icon left>mdi-folder</v-icon>{{course.department? course.department.name: 'No department'}} / {{course.credit_hours}} credit hours</v-chip>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" lg="4" md="4">
                <v-card color="grey lighten-5">
                    <v-card-title><h1 class="text-h5 font-weight-light">Edit information</h1></v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-text-field :rules="courseCodeRules" label="Code" v-model="courseCode"></v-text-field>
                            <v-text-field :rules="courseNameRules" label="name" v-model="courseName"></v-text-field>
                            <v-text-field :rules="courseCreditHoursRules" label="credit hours" v-model="courseCreditHours" type="number"></v-text-field>
                            <v-select
                            label="Department"
                            :rules="departmentIdRules"
                            :items="departments"
                            item-value="id"
                            v-model="courseDepartmentId"
                            item-text="name"
                            :value="course.department? course.department.id: 1"
                            ></v-select>
                            <v-btn @click="updateInfo" :loading="btnLoading1" color="primary" block text><v-icon left>mdi-plus</v-icon>Update</v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-card class="mt-4" color="grey lighten-5" v-if="!loading && course.invs.length > 0">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Change date and time</h1>
                    </v-card-title>
                    <v-card-text>
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
                    </v-card-text>
                    <v-card-actions>
                        <v-btn :loading="btnLoading2" @click="updateDateAndTime" block color="primary" text><v-icon left>mdi-pencil</v-icon>Change date and time</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" lg="8" md="8">
                <v-card v-if="!loading && course.invs.length > 0"  color="grey lighten-5">
                    <v-card-title><h1 class="text-h5 font-weight-light">Course invs</h1></v-card-title>
                    <v-card-text>
                        <v-data-table :headers="headers" :items="course.invs">
                            <template v-slot:[`item.index`]="{index}">
                                {{index+1}}
                            </template>
                            <template v-slot:[`item.users`]="{item}">
                                <v-chip style="text-decoration: none" class="mr-2" small v-for="user in item.users" :key="user.id" :to="{name: 'userProfile', params:{id: user.id}}">{{user.name}}</v-chip>
                            </template>
                            <template v-slot:[`item.actions`]="{item}">
                                <v-btn color="info" style="text-decoration: none" :to="{name: 'roomProfile', params:{id: item.room.id}}" icon small><v-icon small>mdi-calendar</v-icon></v-btn>
                                <v-btn small style="text-decoration: none" :to="{name: 'edit-inv', params:{id: item.id}}" icon><v-icon small>mdi-pencil</v-icon></v-btn>
                                <v-btn @click="confirmDeleteInv(item)" color="error" icon small><v-icon small>mdi-delete</v-icon></v-btn>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
                <v-card v-if="!loading && course.invs.length > 0" class="mt-4" color="grey lighten-5">
                    <v-card-title><h1 class="text-h5 font-weight-light">Remove all course invs</h1></v-card-title>
                    <v-card-text><strong>Warning</strong>, by removing invs from {{course.code}}, all invs will be permentantly removed and all users will unlinked to these invs This action can't be undo.</v-card-text>
                    <v-card-actions>
                        <v-btn @click="confirmDeletAllInvs" block color="error" dark><v-icon left>mdi-close</v-icon>remove all invs</v-btn>
                    </v-card-actions>
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
        Alert, Loading, Confirmation
    },
    data(){
        return{
            loading: true,
            course: Object,
            departments:[],
            headers:[
                {text: '#', value: 'index'},
                {text: 'room', value: 'room.number'},
                {text: 'users', value: 'users'},
                {text: 'actions', value: 'actions'}
            ],

            alert: false,
            alertType: null,
            alertMessage: null,

            dialog: false,
            dialogText: null,
            dialogData: null,
            dialogFunction: null,

            courseCode: null,
            courseCodeRules: [
                v => !!v || 'Course code is required',
                v => (v && v.length >= 4 && v.length <= 10) || 'Course code must be between 4 and 10 characters',
            ],
            courseName: null,
            courseNameRules: [
                v => !!v || 'Course name is required',
                v => (v && v.length >= 10) || 'Course name must be at least 10 characters ',
            ],
            courseCreditHours: null,
            courseCreditHoursRules: [
                v => !!v || 'Credit hours is required',
                v => /^\d+$/.test(v) || 'Credit hours must be at digits ',
            ],
            courseDepartmentId: null,
            departmentIdRules: [
                v => !!v || 'Department is required',
            ],

            btnLoading1: false,
            btnLoading2: false,
            
            allowedMinutes: [0, 30],
            date: null,
            time: null,
            dateMenu: false,
            timeMenu: false
        }
    },
    mounted(){
        let id = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/courses/${id}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.course = response.data.course
            this.departments = response.data.departments
            this.loading = false
            this.courseCode = this.course.code
            this.courseName = this.course.name
            this.courseCreditHours = this.course.credit_hours
            this.courseDepartmentId = this.course.department.id
            if (this.course.invs.length > 0)
            {
                this.date = this.$options.filters.DateOnly(this.course.invs[0].date_time)
                this.time = this.$options.filters.TimeOnly(this.course.invs[0].date_time)
            }
        })
        .catch((error) => {
            this.alert= true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    },
    methods:{
        confirmDeleteInv(inv){
            this.dialog = true
            this.dialogText = 'Are you sure you want to delete inv on ' + this.$options.filters.DateFormat(inv.date_time) + " ?"
            this.dialogData = inv.id
            this.dialogFunction = this.deleteInv
        },
        deleteInv(invId)
        {
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
                this.course.invs = this.course.invs.filter((item) => {
                    return item.id != invId
                })
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        updateInfo(){
            this.btnLoading1 = true
            this.courseCode = this.courseCode.toUpperCase()
             this.courseName = this.courseName.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase()
            })
            let formData = new FormData()
            formData.append('course_id', this.course.id)
            formData.append('course_code', this.courseCode)
            formData.append('course_name', this.courseName)
            formData.append('course_credit_hours', this.courseCreditHours)
            formData.append('department_id', parseInt(this.courseDepartmentId))
            
            axios({
                method: 'post',
                url: '/api/courses/updateinfo',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.btnLoading1 = false
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.course.code = this.courseCode
                this.course.name = this.courseName
                this.course.credit_hours = this.courseCreditHours
                this.course.department = this.departments.filter((item) => {return item.id == parseInt(this.courseDepartmentId)})[0]
            })
            .catch((error) => {
                this.btnLoading1 = false
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })

            
        },
        confirmDeletAllInvs(){
            this.dialog = true
            this.dialogText = 'Are you sure you want to delete all invs ?'
            this.dialogData = this.course.id
            this.dialogFunction = this.deleteAllInvs
        },
        deleteAllInvs(courseId){
            let formData = new FormData()
            formData.append('course_id', courseId)
            axios({
                method: 'post',
                url: '/api/courses/removeallinvs',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.course.invs = []
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        updateDateAndTime(){
            this.btnLoading2 = true
            let formData = new FormData()
            formData.append('course_id', this.course.id)
            formData.append('time', this.time)
            formData.append('date', this.date)
            axios({
                method: 'post',
                url: '/api/courses/updatedatetime',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((respone) => {
                this.btnLoading2 = false
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = respone.data.message
                this.course.invs[0].date_time = `${this.date} ${this.time}`
            })
            .catch((error) => {
                this.btnLoading2 = false
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
            },
            DateOnly(value)
            {
                return moment(value).format("YYYY-MM-DD")
            },
            TimeOnly(value)
            {
                return moment(value).format("hh:mm")
            }
        },
}
</script>