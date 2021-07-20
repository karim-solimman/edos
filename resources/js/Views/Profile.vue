<template>
   <v-container>
       <Loading :loading="loading" />
       <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
       <v-row v-if="!loading">
           <v-col>
                <h1 class="text-h2">{{ user.name }}</h1>
                <v-chip x-small color="primary" class="mr-2" v-for="role in roles" :key="role.id">{{role.name}}</v-chip>
                <h2 class="text-overline">{{ user.email }}</h2>
            </v-col>
       </v-row>
       <v-divider></v-divider>
       <v-row v-if="!loading && invs.length === 0 && roles && roles.length > 0">
           <v-col>
               <v-alert
                border="left"
                color="orange"
                type="info"
                elevation="2"
                >
                Sorry, You didn't select any invs!
                </v-alert>
           </v-col>
       </v-row>
       <v-row v-if="!loading && roles && roles.length === 0">
           <v-col>
               <v-alert border="left" color="orange" text type="error" elevation="2">
                   <strong>Sorry</strong>, Admin didn't assign your role in the system, contact admin for this issue
               </v-alert>
           </v-col>
       </v-row>
       <v-row v-if="!loading && invs.length > 0">
           <v-col lg=4 md=4 cols=12 v-for="inv in invs" :key="inv.id" >
               <v-card rounded="xl" elevation="3">
                   <v-card-title>
                        <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat}}</h1>
                        </v-card-title>
                    <v-card-subtitle>Time: {{inv.date_time | TimeFormat}}</v-card-subtitle>
                    <v-card-text>
                        <v-chip small color="success">
                            <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.users_limit}}
                        </v-chip>
                        <v-chip small color="info" outlined>
                            <v-icon small left>mdi-clock-outline</v-icon>{{inv.pivot.created_at | ago}}
                        </v-chip>
                    </v-card-text>
                   <v-divider></v-divider>
                   <v-card-actions>
                       <v-btn rounded @click="deleteInv(inv.id)" block text color="error"><v-icon left>mdi-close</v-icon>Remove</v-btn>
                   </v-card-actions>
               </v-card>
           </v-col>
       </v-row>
       <v-row>
           <v-col>
               <v-sheet rounded="xl" height="100" width="100">
               </v-sheet>
           </v-col>
       </v-row>
   </v-container>
</template>

<script>
import Loading from '../components/Loading.vue'
import Alert from '../components/Alert.vue'
    export default {
        components:{
            Loading, Alert
        },
        data(){
            return {
                loading: true,
                token: null,
                invs: Array,
                roles: Array,
                user: JSON.parse(localStorage.getItem('user')),
                alertType: null,
                alertMessage: null,
                alert: false
            }
        },
        mounted() {
            this.token = localStorage.getItem('token')
            let formData = new FormData()
            formData.append('user_id', this.user.id)
            axios({
                method: 'post',
                url: '/api/profile',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.$store.dispatch('updateInvs', response?.data?.invs)
                this.$store.dispatch('updateRoles', response?.data?.roles)
                this.invs = response.data.invs
                this.roles = response.data.roles
                this.$emit('logged-in', true)
                this.checkRoles(this.roles)
                this.loading = false
            })
            .catch((error) => {
                this.$router.push('/')
                this.$emit('logged-out', false)
                localStorage.clear()
            })
        },
        methods:{
            deleteInv(invId)
            {
              let formData = new FormData()
              formData.append('inv_id', invId)
              formData.append('user_id', this.user.id)
                axios({
                    method: 'post',
                    url: '/api/invs/remove',
                    data: formData,
                    headers:{
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.invs = response.data.invs
                    this.alertMessage = response.data.message
                    this.alertType = 'success'
                    this.alert = true
                })
                .catch((error) => {
                    console.log(error.response)
                })
            },
            checkRoles(roles)
            {
                $.each(roles, (index, value)=>{
                    if (roles[index]['name'] === 'admin')
                    {
                        this.$emit('role', 'admin')
                        this.$router.push({name: 'dashboard'})
                        return false
                    }
                    else if (roles[index]['name'] === 'user')
                    {
                        this.$emit('role', 'user')
                        return false
                    }
                    else 
                    {
                        this.$emit('role', 'other')
                    }
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
