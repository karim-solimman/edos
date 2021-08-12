<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">Random distribution</h1>
            </v-col>
        </v-row>
        <v-row justify="center" v-if="!loading && users && users.length > 0">
            <v-col cols="6" lg="3" md="3">
                <v-card elevation="3" color="green lighten-4">
                    <v-card-title class="justify-center">
                        <h1 class="font-weight-light"><v-icon left>mdi-chevron-triple-down</v-icon> Min count</h1>
                    </v-card-title>
                    <v-card-text>
                        <h1 class="text-h2 text-center">{{minUser().invs_count}}</h1>
                    </v-card-text>
                </v-card> 
            </v-col>
            <v-col cols="6" lg="3" md="">
                <v-card elevation="3" dark color="red darken-4">
                    <v-card-title class="justify-center">
                        <h1 class="font-weight-light"><v-icon left>mdi-chevron-triple-up</v-icon> Max count</h1>
                    </v-card-title>
                    <v-card-text>
                        <h1 style="color: white" class="text-h2 text-center">{{maxUser().invs_count}}</h1>
                    </v-card-text>
                </v-card> 
            </v-col>
            <v-col cols="6" lg="3" md="">
                <v-card elevation="3" dark color="blue darken-1">
                    <v-card-title class="justify-center">
                        <h1 class="font-weight-light"><v-icon x-large>mdi-math-integral</v-icon> Average</h1>
                    </v-card-title>
                    <v-card-text>
                        <h1 style="color: white" class="text-h2 text-center">{{getAvg().toFixed(2)}}</h1>
                    </v-card-text>
                </v-card> 
            </v-col>
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