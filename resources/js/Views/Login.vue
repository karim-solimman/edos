<template>
<v-container fluid>
    <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
    <v-row v-if="!loading">
        <v-col>
            <v-parallax height="450" src="/img/peak.jpg">
                <v-row
                    align="end"
                    justify="center"
                    >
                    <v-col
                        class="text-center"
                        cols="12"
                    >
                        <h1 class="text-lg-h1 font-weight-thin mb-4">
                        EDOS
                        </h1>
                        <h4 class="text-overline">
                            Distribute, Swap, Monitor, and edit, With one click!
                        </h4>
                    </v-col>
                </v-row>
            </v-parallax>
        </v-col>
    </v-row>
    <v-row style="margin-top: 5%;" justify="space-around" align="center">
        <v-col cols="12" lg="3" md="3">
            <div class="text-center">
                <h1 class="text-h3 font-weight-light">{{date_time | getDay}} {{date_time | getMonth}}</h1>
                <h5 class="text-h5 font-weight-light">{{date_time | getDayName}}</h5>
                <p class="text-overline">{{date_time | getYear}}</p>
            </div>
            <v-hover v-slot="{hover}">
                <v-card hover color="grey lighten-5">
                    <v-card-title class="d-flex justify-space-between">
                        <h1 class="text-h4 font-weight-light">{{date_time | TimeFormat}}</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-chip small dark color="green darken-2"><v-icon left>mdi-account-group</v-icon>2 / 3</v-chip>
                        <v-chip small outlined><v-icon left>mdi-clock-outline</v-icon>{{date_time | ago}}</v-chip>
                    </v-card-text>
                    <v-expand-transition>
                        <div
                            v-if="hover"
                            class="d-flex transition-fast-in-fast-out blue-grey darken-4 v-card--reveal text-h5 font-weight-light white--text"
                            style="height: 100%;"
                        >
                        <span class="text-h4 font-weight-thin text-center"><v-icon dark x-large left color="success">mdi-table-large-plus</v-icon><br/>ADD</span>
                        </div>
                    </v-expand-transition>
                </v-card>
            </v-hover>
        </v-col>
        <v-col cols="12" lg="4" md="4">
            <h1 class="text-center font-weight-thin text-h3">
                Choose your invs
            </h1>
            <p class="text-body-1 text-center">
                With EDOS, users are freely to choose the date / time slot to be attached, without knowing the full details of inv. <br/>
                The Admin has the ability to make all the details visible / hidden.
            </p>
        </v-col>
    </v-row>
    <v-row justify="space-around" align="center">
         <v-col cols="12" lg="4" md="4">
            <h1 class="text-center text-h3 font-weight-thin">
                Remove with one click
            </h1>
            <p class="text-body-1 text-center">
                Users are free to remove the registered invs or swap it with your colleagues, no extra paper work, no extra overhead and time waste. <br/>
                It's all with a simple one click.
            </p>
        </v-col>
        <v-col cols="12" lg="3" md="3">
            <div class="text-center">
                <h1 class="text-h3 font-weight-light">{{date_time | getDay}} {{date_time | getMonth}}</h1>
                <h5 class="text-h5 font-weight-light">{{date_time | getDayName}}</h5>
                <p class="text-overline">{{date_time | getYear}}</p>
            </div>
            <v-hover v-slot="{hover}">
                <v-card  hover color="green lighten-5">
                    <v-card-title class="d-flex justify-space-between">
                        <h1 class="text-h4 font-weight-light">{{date_time | TimeFormat}}</h1>
                        <v-icon color="green">mdi-account</v-icon>
                    </v-card-title>
                    <v-card-text>
                        <v-chip small dark color="red darken-2"><v-icon left>mdi-account-group</v-icon>3 / 3</v-chip>
                        <v-chip small outlined><v-icon left>mdi-clock-outline</v-icon>{{date_time | ago}}</v-chip>
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
    <v-row style="margin-top: 3%" justify="center">
        <v-col cols="10" lg="5" md="5" class="d-flex justify-center align-center" v-if="view == 'grid'">
            <v-sheet class="d-flex justify-center align-center" color="green" elevation="5" height="50" rounded width="183" dark>
                <span class="text-overline ml-2">Item 1</span>
            </v-sheet>
            <v-sheet class="ml-4 d-flex align-center justify-center" color="red" elevation="5" height="50" rounded width="183" dark>
                <span class="text-overline ml-2">Item 2</span>
            </v-sheet>
            <v-sheet class="ml-4 d-flex justify-center align-center" color="info" elevation="5" height="50" rounded width="183" dark>
                <span class="ml-2 text-overline">Item 3</span>
            </v-sheet>
        </v-col>
         <v-col cols="10" lg="5" md="5" class="d-flex justify-center align-center" v-if="view == 'table'">
            <v-data-table :headers="headers" :items="data">
            </v-data-table>
        </v-col>
        <v-col cols="2" lg="1" md="1">
            <v-switch class="mt-11" @click="switchView" :prepend-icon="switchIcon" inset v-model="switchToggle" :persistent-hint="true" :hint="switchHint"></v-switch>
        </v-col>
        <v-col cols="12" lg="6" md="6">
            <h1 class="text-center text-h3 font-weight-thin">
                With favourite layout
            </h1>
            <p class="text-body-1 text-center">
                Using EDOS make it easier to swap and switch between Grid mode or Table mode which you cans switch with a single click and choose your favourite layout of the information.
            </p>
        </v-col>
    </v-row>
    <v-row style="margin-top: 5%" align="center" justify="space-around">
         <v-col cols="12" lg="4" md="4">
            <h1 class="text-center text-h3 font-weight-thin">
                Login to EDOS
            </h1>
            <p class="text-body-1 text-center">
                To use EDOS you are welcomed to register, but the admin should first add you as a new user so you can register in the system and set your password for the first time. EDOS not for everyone.
            </p>
        </v-col>
        <v-col cols="12" lg="4" md="4">
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
                            <v-icon left>mdi-login-variant</v-icon>
                            Login
                        </v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
            <h5 class="body-1 text-center mt-2">Don't have an account? <router-link id='router-link' to="register">Register now</router-link></h5>
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
               loading: true,
               date_time: '2021-07-29 09:00:00',

               switchToggle: false,
               switchHint: 'Table view',
               switchIcon: 'mdi-table-eye',
               view: 'grid',

               headers:[
                   {text: '#', value: 'index'},
                   {text: 'date', value: 'date'},
                   {text: 'time', value: 'time'},
                   {text: 'room', value: 'room'}
               ],
               data:[
                   {
                       index: 1,
                       date: this.$options.filters.DateFormat('2021-9-14'),
                       time: this.$options.filters.TimeFormat('2021-9-14 09:00'),
                       room: 'E318' 
                   },
                    {
                       index: 2,
                       date: this.$options.filters.DateFormat('2021-9-16'),
                       time: this.$options.filters.TimeFormat('2021-9-16 12:00'),
                       room: 'E018' 
                   },
                    {
                       index: 3,
                       date: this.$options.filters.DateFormat('2021-9-17'),
                       time: this.$options.filters.TimeFormat('2021-9-14 15:00'),
                       room: 'E127' 
                   },
                   
               ]
           }
       },
       mounted(){
           this.loading = false
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
                           this.$store.dispatch('updateSettings', response?.data?.settings)
                           $.each(response.data.roles, (index, value) => {
                               roles.push(value['slug'])
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
           switchView(){
               if(!this.switchToggle)
               {
                   this.switchHint = "Table View"
                   this.switchIcon = "mdi-table-eye"
                   this.view = 'grid'
               }
               else
               {
                   this.switchHint = "Grid View"
                   this.switchIcon = "mdi-view-grid"
                   this.view = 'table'
               }
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
            },
            getDayName(value)
            {
                return moment(value).format("dddd")
            },
            getDay(value)
            {
                return moment(value).format("DD")
            },
            getMonth(value)
            {
                return moment(value).format("MMMM")
            },
            getYear(value)
            {
                return moment(value).format("YYYY")
            }
        }
   }
</script>
