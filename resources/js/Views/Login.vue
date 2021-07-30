<template>
<v-container>
    <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
    <v-row style="margin-top: 10%" align="center" justify="space-around">
        <v-col lg="5">
            <v-card>
                <v-card-title>
                    <h1 class="text-h5 font-weight-light">LOGIN</h1>
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="login" ref="form" v-model="valid">
                        <v-text-field
                            type="email"
                            v-model="email"
                            :rules="emailRules"
                            prepend-inner-icon="mdi-email"
                            label="Email"
                            required
                        ></v-text-field>
                        <v-text-field
                            v-model="password"
                            label="Password"
                            type="password"
                            :rules="passwordRules"
                            prepend-inner-icon="mdi-lock"
                            required
                        ></v-text-field>
                        <v-btn :disabled="!valid" color="success" :loading="loading" block success type="submit">
                            <v-icon left>mdi-login</v-icon>
                            Login
                        </v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
            <h5 class="body-1 text-center">Don't have an account? <router-link id='router-link' to="register">Register now</router-link></h5>
        </v-col>
    </v-row>
</v-container>
</template>

<style scoped>
    #router-link{
        text-decoration: none;
        color: #1E90FF;
    }    
    #router-link:hover{
        color: #008B8B;
        /* font-weight: bold; */
    }
</style>

<script>
import Alert from '../components/Alert.vue'
import Loading from '../components/Loading.vue'
   export default {
       components: {
           Alert, Loading
       },
       data(){
           return{
               valid: false,
               email: '',
               emailRules: [
                   v => !!v || 'E-mail is required',
                   v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
               ],
               password: '',
               passwordRules: [v => !!v || 'Password is required'],
               alert: false,
               alertType: null,
               alertMessage: null,
               loading: false
           }
       },
       methods: {
           login(){
               let roles = []
               if(!this.$refs.form.validate())
                   return
               let formData = new FormData()
               this.loading = true
               formData.append('email', this.email)
               formData.append('password', this.password)
               axios.get('/sanctum/csrf-cookie')
                   .then(response => {
                       axios.post('/api/login', formData)
                       .then((response => {
                           localStorage.setItem('token', response.data.token)
                           localStorage.setItem('user', JSON.stringify(response.data.user))
                           this.$store.dispatch('updateUser', response.data.user)
                           this.$store.dispatch('updateInvs', response?.data?.invs)
                           $.each(response.data.roles, (index, value) => {
                               roles.push(response.data.roles[index]['slug'])
                           })
                           this.$store.dispatch('updateRoles', roles)
                           this.$emit('logged-in',true)
                       }))
                       .catch((error) => {
                           this.alert = true
                           this.alertType = 'error'
                           this.alertMessage = error.response.data.message
                           this.loading = false
                       })
                   })
           },
       }
   }
</script>
