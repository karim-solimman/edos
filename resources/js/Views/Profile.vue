<template>
   <v-container>
       <Loading :loading="loading" />
       <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
       <v-row v-if="!loading">
           <v-col cols="12" lg="6" md="6">
                <h1 class="text-h2">{{ user.name }}</h1>
                <h2 class="text-overline">{{ user.email }}</h2>
                <v-chip small color="primary" class="text-overline mr-2" v-for="role in roles" :key="role.id"><v-icon small left>mdi-account</v-icon>{{role}}</v-chip>
                <v-chip small outlined class="text-overline"><v-icon small left>mdi-folder</v-icon>{{user.department.name}}</v-chip>
            </v-col>
            <v-col>
                <h1 class="text-h1 font-weight-light">{{ invs.length }}<span class="text-overline">invs</span></h1>
            </v-col>
       </v-row>
       <v-divider></v-divider>
       <v-row v-if="!loading && invs.length === 0 && roles && roles.length > 0">
           <v-col>
               <v-alert
                border="top"
                color="red darken-2"
                type="info"
                elevation="3"
                dismissible
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
           <v-col lg=4 md=4 cols=12 v-for="inv in invs" :key="inv.id">
               <v-hover v-slot="{hover}">
                    <v-card color="grey lighten-5" hover @click="deleteInv(inv.id)">
                        <v-card-title>
                                <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat}}</h1>
                                </v-card-title>
                            <v-card-subtitle>Time: {{inv.date_time | TimeFormat}}</v-card-subtitle>
                            <v-card-text>
                                <v-chip dark small :color="inv.users_count < inv.room.users_limit? 'green darken-2' : 'red darken-2' ">
                                    <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.room.users_limit}}
                                </v-chip>
                                <v-chip small color="info" outlined>
                                    <v-icon small left>mdi-clock-outline</v-icon>{{inv.pivot.created_at | ago}}
                                </v-chip>
                            </v-card-text>
                            <v-expand-transition>
                                <div
                                    v-if="hover"
                                    class="d-flex transition-fast-in-fast-out blue-grey darken-4 v-card--reveal text-h5 font-weight-light white--text"
                                    style="height: 100%;"
                                >
                                <span class="text-h4 font-weight-thin text-center"><v-icon dark x-large left color="error">mdi-table-large-remove</v-icon><br/>REMOVE</span>
                                </div>
                            </v-expand-transition>
                    </v-card>
               </v-hover>
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

<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  position: absolute;
  opacity: 0.9;
  width: 100%;
}
</style>

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
                invs: [],
                roles: [],
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
                this.invs = response?.data?.invs
                this.$store.dispatch('updateInvs', this.invs)
                $.each(response.data.roles, (index, value) => {
                    this.roles.push(response.data.roles[index]['slug'])
                })
                this.$store.dispatch('updateRoles', this.roles)
                this.$emit('logged-in', true)
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
                    url: '/api/users/removeinv',
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
