<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around" v-if="!loading && departments">
            <v-col cols=12 md=5 lg=5>
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">Add new course</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="addCourse" ref="form" v-model="formValid">
                            <v-text-field
                            v-model="courseCode"
                            :error-messages="errorMessages['course_code']"
                            :rules="courseCodeRules"
                            @keydown="errorMessages = []"
                            label="Code"
                            ></v-text-field>
                            <v-text-field
                            v-model="courseName"
                            :rules="courseNameRules"
                            label="Name"
                            ></v-text-field>
                            <v-text-field
                            v-model="courseCreditHours"
                            :rules="courseCreditHoursRules"
                            label="Credit Hours"
                            type="number"
                            ></v-text-field>
                            <v-select
                            label="Department"
                            :items="departments"
                            :rules="departmentIdRules"
                            item-text="name"
                            item-value="id"
                            v-model="departmentId"
                            ></v-select>
                            <v-btn :loading="btnLoading" :disabled="!formValid" block type="submit" color="success"><v-icon left>mdi-plus</v-icon>Add new</v-btn>
                            <a v-if="alertType == 'error'" class="text-center" @click="reset()">Add another course?</a>
                        </v-form>
                    </v-card-text>
                 </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Alert from '../../components/Alert.vue'
import Loading from '../../components/Loading.vue'
export default {
    components:{
        Alert, Loading
    },
    data(){
        return{
            loading: true,
            btnLoading: false,
            formValid: false,
            departments: [],
            alert: false,
            alertType: null,
            alertMessage: null,

            courseCode: '',
            courseCodeRules: [
                v => !!v || 'Course code is required',
                v => (v && v.length >= 4) || 'Course code must be at least 4 characters ',
            ],
            courseName: '',
            courseNameRules: [
                v => !!v || 'Course name is required',
                v => (v && v.length >= 10) || 'Course name must be at least 10 characters ',
            ],
            courseCreditHours: '',
            courseCreditHoursRules: [
                v => !!v || 'Credit hours is required',
                v => /^\d+$/.test(v) || 'Credit hours must be at digits ',
            ],
            departmentId: '',
            departmentIdRules: [
                v => !!v || 'Department is required',
            ],
            errorMessages: []
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: '/api/departments',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.departments = response.data.departments
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    },
    methods:{
        addCourse(){
            this.btnLoading = true
            this.courseCode = this.courseCode.toUpperCase()
             this.courseName = this.courseName.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase()
            })
            let formData = new FormData()
            formData.append('course_code', this.courseCode)
            formData.append('course_name', this.courseName)
            formData.append('course_credit_hours', this.courseCreditHours)
            formData.append('department_id', parseInt(this.departmentId))
            axios({
                method: 'post',
                url: '/api/courses/create',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.btnLoading = false
                this.$refs.form.reset()
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.errorMessages = error.response.data.errors
                this.btnLoading = false
            })
        },
        reset(){
            this.errorMessages = []
            this.$refs.form.reset()
        }
    }
    
}
</script>