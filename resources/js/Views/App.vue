<template>
    <v-app>
        <navigation-drawer @drawer-close="drawerToggle = false" :drawerToggle="this.drawerToggle" :isAdmin="isAdmin" :isUser="isUser" :status="this.status" ></navigation-drawer>
        <nav-bar @drawer-toggle="drawerToggle = !drawerToggle" @logged-out="loggedOut" :status="this.status"></nav-bar>
        <v-main>
            <v-container class="mb-12" fluid>
                <Loading :loading="loading" />
                <router-view v-if="!loading" @logged-out="loggedOut" @logged-in="loggedIn"/>
            </v-container>
            <app-footer></app-footer>
        </v-main>
    </v-app>
</template>

<script>
import Loading from '../components/Loading.vue'
    export default 
    {
        name: 'app-vue',
        components:{
            Loading
        },
        data(){
            return{
                token: localStorage.getItem('token'),
                status: false,
                drawerToggle: false,
                isAdmin: false,
                isUser: false,

                loading: true,
                invs: [],
                roles: [],
                user: JSON.parse(localStorage.getItem('user')),
            }
        },
        mounted() {
            if (this.token)
            {
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
                    this.loggedIn()
                    this.loading = false
                })
                .catch((error) => {
                    this.loggedOut()
                    this.$router.push('/')
                    window.localStorage.clear()
                    this.loading = false
                })
            }
            else {
                localStorage.clear()
                this.loggedOut()
                this.$router.push('/').catch((error)=>{})
                this.loading = false
            }
        },
        methods: {
            loggedOut(){
                this.status = false
                this.drawerToggle = false
                this.isAdmin = false
                this.isUser = false
            },
            loggedIn(){
                let roles = this.$store.getters.getRoles
                this.status = true
                this.drawerToggle = true
                if(roles.includes('admin')){
                    this.isAdmin = true
                    this.$router.push({name: 'dashboard'}).catch((error)=>{})
                }
                else if(roles.includes('user')){
                    this.isUser = true
                    this.$router.push({name: 'profile'}).catch((error)=>{})
                }
                else{
                    this.$router.push({name: 'profile'}).catch((error)=>{})
                }

            },
        }
    }
</script>
