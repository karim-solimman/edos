<template>
    <v-container>
        <Loading :loading="loading"/>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
         <v-row>
           <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
       </v-row>
        <v-row v-if="!loading && rooms">
            <v-col>
            <v-data-table :headers="headers" :items="rooms" :search="search">
                <template v-slot:[`item.index`]="{index}">
                    {{index+1}}
                </template>
                <template v-slot:[`item.actions`]="{item}">
                    <v-btn style="text-decoration: none" icon small :to="{name: 'roomProfile', params:{id: item.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                    <v-btn style="text-decoration: none" icon small :to="{name: 'editRoom', params:{id: item.id}}"><v-icon small>mdi-pencil</v-icon></v-btn>
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
            rooms: Array,
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null,
            headers:[
                {text: '#', value: 'index'},
                {text: 'number', value:'number'},
                {text: 'invs count', value: 'invs_count'},
                {text: 'users limit', value: 'users_limit'},
                {text: 'actions', value: 'actions'}
            ],
            search: ''
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: '/api/rooms',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.rooms = response.data.rooms
            this.loading = false
        })
        .catch((error) => {

        })
    }
    
}
</script>