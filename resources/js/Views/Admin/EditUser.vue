<template>
    <v-container>
       <Loading :loading="loading" />
       <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading">
            <v-col>
                <h5 class="text-h4 font-weight-light">
                    {{user.name}} <v-chip class="mr-2" x-small color="primary" v-for="role in user.roles" :key="role.id">{{role.name}}</v-chip>
                </h5>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" md=5 lg=5>
                <v-card color="grey lighten-5">
                    <v-card-title class="text-h5 font-weight-light">Update Information</v-card-title>
                    <v-card-text>
                        <v-text-field label="Name" :value="user.name"></v-text-field>
                        <v-text-field label="Email" :value="user.email"></v-text-field>
                    </v-card-text>
                     <v-card-actions>
                        <v-btn color="primary" block><v-icon left>mdi-pencil</v-icon>update</v-btn>
                    </v-card-actions>
                    <v-divider></v-divider>
                    <v-card-title class="text-h5 font-weight-light">Password Reset</v-card-title>
                    <v-card-text>
                        <p><strong color="error">Warning</strong>, by reseting the user password, user won't be able to login again until new registeration is done
                        <strong>, user data and invs won't be removed</strong></p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="error" block><v-icon left>mdi-lock-reset</v-icon>reset</v-btn>
                    </v-card-actions>
                    <v-divider></v-divider>
                    <v-card-title class="text-h5 font-weight-light">Remove all users invs</v-card-title>
                    <v-card-text>
                        <p><strong color="error">Warning</strong>, This action can't be undo.
                        <strong>, User name, department, email and password will still the same!</strong></p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="error" block><v-icon left>mdi-delete-forever</v-icon>remove all invs</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">User Invs</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-data-table :headers="headers" :items="user.invs">
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
                                <v-btn style="text-decoration: none" small icon :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                                <v-btn style="text-decoration: none" color="error" small icon :to="{name: 'invProfile', params:{id: item.id}}"><v-icon small>mdi-close</v-icon></v-btn>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
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
            user: Object,
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'room', value: 'room.number'},
                {text: 'actions', value: 'actions'}
            ],
            alert: flase,
            alertMessage: null,
            alertType: null
        }
    },
    mounted(){
        let userId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/users/${userId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response)=>{
            this.user = response.data.user
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