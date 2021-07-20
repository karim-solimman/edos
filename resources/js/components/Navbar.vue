<template>
    <v-app-bar app dense>
        <v-app-bar-nav-icon v-if="status" @click.stop="$emit('drawer-toggle')" ></v-app-bar-nav-icon>
        <v-spacer></v-spacer>
        <v-toolbar-items>
            <v-btn to="/" v-if="!status" text style="text-decoration: none"><v-icon left>mdi-home</v-icon> Home</v-btn>
            <v-btn to="/profile" text v-if="status" style="text-decoration: none"><v-icon left>mdi-account</v-icon>{{ $store.getters.getUser.name }}</v-btn>
            <v-btn @click="logout" text v-if="status" style="text-decoration: none"><v-icon left>mdi-logout</v-icon>Logout</v-btn>
        </v-toolbar-items>
    </v-app-bar>
</template>

<script>
    export default {
        name: 'nav-bar',
        props: {
            status: {
                type: Boolean,
            }
       },
       methods: {
           logout(){
                axios({
                    method: 'post',
                    url: '/api/logout',
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    window.localStorage.clear()
                    this.$emit('logged-out', false)
                    this.$router.push('/')
                })
                .catch((error) => {
                    window.localStorage.clear()
                    this.$emit('logged-out', false)
                    this.$router.push('/')
                })
            }
        },
    }
</script>
