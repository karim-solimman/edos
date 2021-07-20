<template>
    <v-container>
       <Loading :loading="loading" />
        <v-row v-if="!loading">
            <v-col>
                <h5 class="text-h4 font-weight-light">
                    {{user.name}}
                </h5>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" md=4 lg=4>
                <v-card>
                    <v-card-title class="text-h5 font-weight-light">Update Information</v-card-title>
                    <v-card-text>
                        <v-text-field label="Name" :value="user.name"></v-text-field>
                        <v-text-field label="Email" :value="user.email"></v-text-field>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn text color="primary" block><v-icon left>mdi-pencil</v-icon>update</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" md=4 lg=4>
                <v-card>
                    <v-card-title class="text-h5 font-weight-light">Password Reset</v-card-title>
                    <v-card-text>
                        <p><strong color="error">Warning</strong>, by reseting the user password, user won't be able to login again until new registeration is done
                        <strong>, user data and invs won't be removed</strong></p>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn text color="error" block><v-icon left>mdi-lock-reset</v-icon>reset</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" md=4 lg=4>
            <v-card>
                <v-card-title class="text-h5 font-weight-light">Remove all users invs</v-card-title>
                    <v-card-text>
                        <p><strong color="error">Warning</strong>, This action can't be undo.
                        <strong>, User name, department, email and password will still the same!</strong></p>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn text color="error" block><v-icon left>mdi-delete-forever</v-icon>remove all jobs</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
export default {
    components: {
        Loading
    },
    data(){
        return{
            loading: true,
            user: Object
        }
    },
    mounted(){
        let userId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/users/${userId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response)=>{
            this.user = response.data.user
            this.loading = false
        })
    }
}
</script>