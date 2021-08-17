<template>
    <v-container>
       <Loading :loading="loading" />
       <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="roles.length == 0 && !loading"> 
            <v-col>
                <v-alert
                outlined
                type="warning"
                prominent
                border="left"
                >
                Sorry, No Roles or Permissions been added to be displayed!
                </v-alert>
            </v-col>
        </v-row>
        <v-row v-for="role in roles" :key="role.id">
            <v-col>
                <v-card>
                    <v-card-title>
                        <v-badge overlap color="green" :content="role.users_count"> <h1 class="text-h5 font-weight-light">{{role.name}}</h1> </v-badge>
                    </v-card-title>
                    <v-card-text>
                        <v-simple-table dense>
                            <template v-slot:default>
                            <thead>
                                <tr>
                                <th class="text-left">
                                    #
                                </th>
                                <th class="text-left">
                                    Name
                                </th>
                                <th class="text-left">
                                    Email
                                </th>
                                <th class="text-left">
                                    Verified
                                </th>
                                <th class="text-left">
                                    actions
                                </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, i) in role.users" :key="i">
                                    <td>{{i+1}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.email}}</td>
                                    <td>
                                        <v-icon small v-if="user.email_verified_at" color="success">mdi-check-circle</v-icon>
                                        <v-icon small v-else color="warning">mdi-alert-circle</v-icon>
                                    </td>
                                    <td>
                                        <v-btn style="text-decoration: none" color="info" icon small :to="{name: 'userProfile', params:{id: user.id}}"><v-icon small>mdi-account</v-icon></v-btn>
                                        <v-btn style="text-decoration: none" :to="{name: 'editUser', params:{id: user.id}}" class="ml-2" icon small><v-icon small>mdi-pencil</v-icon></v-btn>
                                    </td>
                                </tr>
                            </tbody>
                            </template>
                        </v-simple-table>
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
    components:{
        Loading, Alert
    },
   data(){
       return{
           roles: [],
           loading: true,
           alert: false,
           alertType: null,
           alertMessage: null
       }
   },

   mounted(){
       axios({
           method: 'get',
           url: '/api/roles',
           headers: {
               Authorization: `Bearer ${window.localStorage.getItem('token')}`
           }
       })
       .then((response) => {
           this.loading = false
           this.roles = response.data
       })
       .catch((error)=>{
           this.alert = true
           this.alertType = 'error'
           this.alertMessage = error.response.data.message
           this.loading = false
       })
   }
}
</script>