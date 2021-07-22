<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading">
            <v-col>
                <h2 class="text-h4 font-weight-light">{{department.name}}</h2>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col>
                <v-data-table :headers="headers" :items="department.courses">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        <v-chip v-if="item.invs.length ===0" small color="error">No invs</v-chip>
                        <span v-else>{{item.invs[0].date_time | DateFormat}}</span>
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        <v-chip v-if="item.invs.length ===0" small color="error">No invs</v-chip>
                        <span v-else>{{item.invs[0].date_time | TimeFormat}}</span>
                    </template>
                    <template v-slot:[`item.invs`]="{item}">
                        <v-chip-group v-if="item.invs.length">
                            <v-chip style="text-decoration: none" :to="{name: 'roomProfile', params:{id: inv.room.id}}" small v-for="inv in item.invs" :key="inv.id">{{inv.room.number}}</v-chip>   
                        </v-chip-group>
                        <v-chip v-else small color="error">No invs</v-chip>
                    </template>
                </v-data-table>
                {{courses}}
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
            loading: true,
            department_id: this.$route.params.id,
            department: Object,
            courses: [],
            alert: false,
            alertType: '',
            alertMessage: '',
            headers:[
                {text: '#', value: 'index'},
                {text: 'code', value: 'code'},
                {text: 'name', value: 'name'},
                {text: 'credit hours', value: 'credit_hours'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'rooms', value: 'invs'}
            ]
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: `/api/departments/${this.department_id}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.department = response.data.department
            this.courses = response.data.courses_invs
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