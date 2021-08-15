<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h5 font-weight-thin text-center">Random distribution</h1>
            </v-col>
        </v-row>
        <v-row justify="center" v-if="!loading && users && users.length > 0">
            <v-sheet class="d-flex justify-center align-center mt-1" color="green" elevation="5" height="50" rounded width="183" dark>
                <span class="text-overline ml-2">{{minUser().invs_count}} Minimum</span>
            </v-sheet>
            <v-sheet class="ml-4 d-flex align-center justify-center mt-1" color="red" elevation="5" height="50" rounded width="183" dark>
                <span class="text-overline ml-2">{{maxUser().invs_count}} Maximum</span>
            </v-sheet>
            <v-sheet class="ml-4 d-flex justify-center align-center mt-1" color="info" elevation="5" height="50" rounded width="183" dark>
                <span class="ml-2 text-overline">{{getAvg().toFixed(2)}} Average</span>
            </v-sheet>
        </v-row>
        
        <v-row v-if="!loading && users" >
            <v-col>
                <v-data-table :headers="headers" :items="users">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn color="info" style="text-decoration: none" icon small :to="{name: 'userProfile', params:{id: item.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                        <v-btn style="text-decoration: none" class = "ml-2" icon small :to="{name: 'editUser', params:{id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
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
        Loading, Alert
    },
    data(){
        return{
            data: '',
            users: [],
            
            headers: [
                {text: '#', value: 'index'},
                {text: 'name', value: 'name'},
                {text: 'department', value: 'department.name'},
                {text: 'invs count', value: 'invs_count'},
                {text: 'actions', value: 'actions'}
            ],

            loading: true,

            alert: false,
            alertType: null,
            alertMessage: null,
        }
    },
    mounted(){
        axios({
            method: 'post',
            url: '/api/invs/randomdistribution',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.data = response.data
            this.users = response.data.users
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
        minUser(){
           let min = this.users[0];
           $.each(this.users, (index, user) => {
               min.invs_count < user.invs_count ? min = min : min = user
           })
           return min
        },
        maxUser(){
            let max = this.users[0];
            $.each(this.users, (index, user) => {
                max.invs_count > user.invs_count ? max = max : max = user
            })
            return max
        },
        getAvg(){
            let sum = 0 
            $.each(this.users, (index, user) => {
                sum += user.invs_count
            })
            let avg = sum / this.users.length
            return avg
        }
    }
    
}
</script>