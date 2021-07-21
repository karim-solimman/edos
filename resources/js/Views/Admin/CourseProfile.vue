<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading && course">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{course.code}} - {{course.name}}</h1>
                <h5 class="text-overline">{{course.department.name}} / {{course.credit_hours}} credit hours</h5>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table :headers="headers" :items="course.invs">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        {{item.date_time | DateFormat }}
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.users_count`]="{item}" >
                        {{item.users.length}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration:none" :to="{name: 'invProfile', params:{id: item.id}}" icon><v-icon small>mdi-table-eye</v-icon></v-btn>
                    </template>
                </v-data-table>
            </v-col>
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
            course: Object,
            alert: false,
            alertType: null,
            alertMessage: null,
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'room', value: 'room.number'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'actions', value: 'actions'}
            ]
        }
    },
    mounted(){
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