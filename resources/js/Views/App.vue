<template>
    <v-app>
        <navigation-drawer @drawer-toggle-fun="drawerToggle = !drawerToggle" :drawerToggle="this.drawerToggle" :isAdmin="isAdmin" :isUser="isUser" :status="this.status" ></navigation-drawer>
        <nav-bar @drawer-toggle="drawerToggle = !drawerToggle" @logged-out="loggedOut" :status="this.status"></nav-bar>
        <v-main>
        <v-container fluid>
            <router-view @logged-out="loggedOut" @click="drawerToggle = !drawerToggle" @logged-in="loggedIn"/>
        </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'app-vue',
        data(){
            return{
                token: localStorage.getItem('token'),
                status: false,
                drawerToggle: false,
                isAdmin: false,
                isUser: false
            }
        },
        mounted() {
            if (this.token)
            {
                this.$router.push({name: 'profile'}).catch((error)=>{})
            }
            else {
                localStorage.clear()
                this.loggedOut()
                this.$router.push('/').catch((error)=>{})
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
