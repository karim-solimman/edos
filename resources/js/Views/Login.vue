<template>
        <v-container>
            <v-row>
                <v-col>
                    <v-alert
                        type="error"
                        dismissible
                        elevation="6"
                        border="top"
                        v-if="isError"
                    >
                        Sorry, {{errors}}
                    </v-alert>
                </v-col>
            </v-row>
            <v-row style="margin-top: 10%" align="center" justify="space-around">
                <v-col lg="5">
                    <v-card>
                        <v-card-title>LOGIN</v-card-title>
                        <v-card-text>
                            <v-form @submit.prevent="login" ref="form" v-model="valid">
                                <v-text-field
                                    type="email"
                                    v-model="email"
                                    :rules="emailRules"
                                    prepend-inner-icon="mdi-email"
                                    label="Email"
                                    validate-on-blur
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
   export default {
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
               isError: false,
               errors: '',
               loading: false
           }
       },
       methods: {
           login(){
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
                           this.$emit('logged-in',true)
                           this.$store.dispatch('updateUser', response.data.user)
                           this.$store.dispatch('updateInvs', response?.data?.invs)
                           this.$store.dispatch('updateRoles', response?.data?.roles)
                           this.checkRoles(this.$store.getters.getRoles)
                       }))
                       .catch((error) => {
                           this.isError = true
                           this.loading = false
                           this.errors = error.response.data.message
                       })
                   })
           },
           checkRoles(roles)
            {
                $.each(roles, (index, value)=>{
                    if (roles[index]['name'] === 'admin')
                    {
                        this.$emit('role', 'admin')
                        this.$router.push({name: 'dashboard'}).catch((error)=>{})
                        return false
                    }
                    else if (roles[index]['name'] === 'user')
                    {
                        this.$emit('role', 'user')
                        this.$router.push({name: 'profile'}).catch((error)=>{})
                        return false
                    }
                    else 
                    {
                        this.$emit('role', 'other')
                        this.$router.push({name: 'profile'}).catch((error)=>{})
                    }
                })
                this.$router.push({name: 'profile'}).catch((error)=>{})
            }
       }
   }
</script>
