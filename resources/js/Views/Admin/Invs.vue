<template>
        <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading" />
        <v-row v-if="!loading">
           <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
       </v-row>
       <v-row v-if="!loading">
           <v-col>
               <v-data-table :headers="headers" :items="invs" :search="search">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template> 
                    <template v-slot:[`item.date`]="{item}">
                        {{item.date_time | DateFormat}}
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        {{item.date_time | TimeFormat}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration: none" icon small :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small >mdi-information</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class = "ml-2" icon small :to="{name: 'edit-inv', params:{id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class = "ml-2" icon small><v-icon small>mdi-delete</v-icon></v-btn>
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
    name: 'invs',
    components: {
        Loading, Alert
    },
    data()
    {
        return {
            loading: true,
            invs: [],
            user_invs: [],
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date', sortable: true},
                {text: 'time', value: 'time'},
                {text: 'dur', value: 'duration'},
                {text: 'course', value: 'course.code'},
                {text: 'room', value: 'room.number'},
                {text: 'department', value:'course.department.name'},
                {text: 'users count', value: 'users_count'},
                {text: 'users limit', value: 'room.users_limit'},
                {text: 'actions', value:'actions', sortable: false}
            ],
            search: '',
            alert: false,
            alertType: null,
            alertMessage: null,
        }
    },
    methods:{
        isExists(invId){
            let status = false
            this.user_invs.forEach((item) => {
                if (item.id === invId)
                    status = true
            })
            return status
        },
        updateInvs(){
            axios.get('/api/invs')
                .then((response) => {
                    this.invs = response.data
                })
                .catch((error) => {
                    console.log(error.response.message)
                })
        },
        addInv(invId){
            let formData = new FormData()
            formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
            formData.append('inv_id', invId)
            console.log(formData.get('user_id'), formData.get('inv_id'))
            axios.get('/sanctum/csrf-cookie')
            .then(response => {
                axios({
                    method: 'post',
                    url: '/api/users/addinv',
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.alertType = "success"
                    this.alertMessage = response.data.message
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.user_invs = this.$store.getters.getInvs
                    this.alert = true
                    this.updateInvs()
                })
                .catch((error) => {
                    this.alertType = "error"
                    this.alert = true
                    this.alertMessage = error.response.data.message
                })
            })
        },
        removeInv(invId) {
            let formData = new FormData()
            formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
            formData.append('inv_id', invId)
            axios.get('/sanctum/csrf-cookie')
            .then(response => {
                axios({
                    method: 'post',
                    url: '/api/users/removeinv',
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.alertType = "success"
                    this.alertMessage = response.data.message
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.user_invs = this.$store.getters.getInvs
                    this.alert = true
                    this.updateInvs()
                })
                .catch((error) => {
                    this.alertType = "error"
                    this.alert = true
                    this.alertMessage = error.response.data.message
                })
            })

        }
    },
    mounted() {
        this.user_invs = this.$store.getters.getInvs
        axios.get('/api/invs')
        .then((response) => {
            this.invs = response.data
            this.loading = false
        })
        .catch((error) => {
            this.alertType = "error"
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
