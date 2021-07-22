<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around" v-if="!loading">
            <v-col cols=12 lg=5 md=5>
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h4 class="text-h4 font-weight-light">Add new user</h4>
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                            label="Name"
                            :rules="nameRules"
                            required
                            v-model="name"
                            >
                            </v-text-field>
                            <v-text-field
                            label="Email"
                            :rules="emailRules"
                            required
                            v-model="email"
                            >
                            </v-text-field>
                            <v-select
                            v-model="department"
                            :items="departments"
                            item-text="name"
                            item-value="id"
                            :rules="[v => !!v || 'Department is required']"
                            label="Department"
                            >
                            </v-select>
                            <v-btn block color="success"><v-icon left>mdi-plus</v-icon> Add new user</v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components: {
        Loading, Alert
    },
    data(){
        return{
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null,
            name: '',
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length >= 10) || 'Name must be at least 10 characters',
            ],
            email: '',
            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid', 
            ],
            department: null,
            departments: []
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: '/api/departments',
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.departments = response.data.departments
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    }
    
}
</script>