<template>
    <v-container>
        <Loading :loading="loading" />
        <v-row v-if="!loading">
            <v-col>
                <h4 class="text-h4 font-weight-light">Add new user</h4>
            </v-col> 
        </v-row>
        <v-row v-if="!loading">
            <v-col cols=12 lg=5 md=5>
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
            console.log(error.response.data.message)
        })
    }
    
}
</script>