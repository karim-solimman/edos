<template>
    <v-app>
        <navigation-drawer @drawer-toggle-fun="drawerToggle = !drawerToggle" :drawerToggle="this.drawerToggle" :isAdmin="isAdmin" :isUser="isUser" :status="this.status" ></navigation-drawer>
        <nav-bar @drawer-toggle="drawerToggle = !drawerToggle" @logged-out="loggedOut" :status="this.status"></nav-bar>
        <v-main>
        <v-container fluid>
            <router-view @click="drawerToggle = !drawerToggle" @logged-in="loggedIn" @role="role"/>
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
                this.$router.push({name: 'profile'})
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
                this.status = true
                this.drawerToggle = true
            },
            role(data){
                if (data === 'admin')
                {
                    this.isAdmin = true
                }
                else if (data === 'user')
                {
                    this.isUser = true
                }
            }
        }
    }
</script>
