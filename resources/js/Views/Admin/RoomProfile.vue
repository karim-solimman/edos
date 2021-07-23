<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading && room">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{ room.number }}</h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room && room.invs">
            <v-col>
                <v-data-table :headers="headers" :items="room.invs">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.users_count`]="{item}">
                        {{item.users.length}}
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
    components: {
        Loading, Alert
    },
    data(){
        return{
            room: Object,
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null,
            headers: [
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'course', value: 'course.code'},
                {text: 'department', value: 'course.department.name'}
            ],
        }
    },
    mounted(){
        let roomId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/rooms/${roomId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.room = response.data.room
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
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