<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation
         @dialog-closed="dialog = false"
         :confirmationText="dialogText"
         :onDeleteFunction="deleteCourse" 
         :dialogData="dialogData" 
         :dialog="dialog" 
         /> 
         <v-row v-if="!loading && courses">
           <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
       </v-row>
        <v-row v-if="!loading && courses">
            <v-col>
                <v-data-table :headers="headers" :items="courses" :search="search">
                    <template v-slot:[`item.index`]="{index}">
                       {{index+1}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn color="info" style="text-decoration: none" icon small :to="{name: 'courseProfile', params:{id: item.id}}"><v-icon small>mdi-book-account</v-icon></v-btn>
                        <v-btn style="text-decoration: none" icon small :to="{name: 'editCourse', params: {id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
                        <v-btn style="text-decoration: none" color="error" icon small @click="confirm(item)"><v-icon small>mdi-delete</v-icon></v-btn>
                    </template>   
                </v-data-table>
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
        Loading, Alert, Confirmation
    },
    data(){
        return{
            courses: [],
            loading: true,
            alert: false,
            alertMessage: null,
            alertType: null,
            headers:[
                {text: '#', value: 'index'},
                {text: 'code', value: 'code'},
                {text: 'name', value: 'name'},
                {text: 'credit hours', value: 'credit_hours'},
                {text: 'invs count', value: 'invs_count'},
                {text: 'department', value: 'department.name'},
                {text: 'actions', value: 'actions'}
            ],
            search: '',
            dialog: false,
            dialogData: null,
            dialogText: null,

        }
    },
    mounted(){
        this.getCourses()
    },
    methods:{
        confirm(course){
            this.dialog = true
            this.dialogText = 'Are you sure you want to delete ' + course.code + ' - ' + course.name + " ?"
            this.dialogData = course.id
        },
        deleteCourse(courseId){
            axios({
                method: 'get',
                url: `/api/courses/${courseId}/remove`,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.getCourses()
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        getCourses() {
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
        }
    }
    
}
</script>