<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation @dialog-closed="dialog = false" :confirmationText="dialogText" :dialog="dialog" :dialogData="dialogData" :onDeleteFunction="dialogFunction" />
        <v-row v-if="!loading && course">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{course.code}} - {{course.name}}</h1>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-calendar</v-icon>{{course.invs[0].date_time | DateFormat}}</v-chip>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-alarm</v-icon>{{course.invs[0].date_time | TimeFormat}}</v-chip>
                <v-chip class="text-overline" outlined v-if="course && course.invs.length > 0 "><v-icon left>mdi-clock</v-icon>{{course.invs[0].duration}} Hours</v-chip>
                <v-chip class="text-overline" outlined v-else>No invs</v-chip>
                <v-chip class="text-overline my-1" outlined><v-icon left>mdi-folder</v-icon>{{course.department? course.department.name: 'No department'}} / {{course.credit_hours}} credit hours</v-chip>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table :headers="headers" :items="course.invs">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.users_count`]="{item}" >
                        {{item.users.length}}
                    </template>
                    <template v-slot:[`item.users`]="{item}" >
                        <v-chip-group column>
                            <v-chip small v-for="user in item.users" :key="user.id">{{user.name}}</v-chip>
                        </v-chip-group>
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn small style="text-decoration: none" color="info" :to="{name: 'invProfile', params:{id: item.id}}" icon><v-icon small>mdi-table-eye</v-icon></v-btn>
                        <v-btn small style="text-decoration: none" :to="{name: 'edit-inv', params:{id: item.id}}" icon><v-icon small>mdi-pencil</v-icon></v-btn>
                        <v-btn small class="ml-1" style="text-decoration: none" icon color="error" @click="confirmDeleteInv(item)"><v-icon small>mdi-delete</v-icon></v-btn>
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
    components:{
        Alert, Loading, Confirmation
    },
    data(){
        return{
            loading: true,
            course: Object,
            
            alert: false,
            alertType: null,
            alertMessage: null,

            headers:[
                {text: '#', value: 'index'},
                {text: 'room', value: 'room.number'},
                {text: 'users', value: 'users'},
                {text: 'users limit', value: 'room.users_limit'},
                {text: 'actions', value: 'actions'}
            ],

            dialog: false,
            dialogText: null,
            dialogData: null,
            dialogFunction: null,
        }
    },
    mounted(){
        this.getCourseInfo()
    },
    methods:{
            getCourseInfo(){
                let courseId = this.$route.params.id
                axios({
                    method: 'get',
                    url: `/api/courses/${courseId}`,
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.course = response.data.course
                    this.loading = false
                })
                .catch((error) => {
                    this.alert = true
                    this.alertType = 'error'
                    this.alertMessage = error.response.data.message
                    this.loading = false
                })
        },
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
                this.getCourseInfo()
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