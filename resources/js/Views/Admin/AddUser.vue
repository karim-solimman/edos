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
                        <v-form ref="form" v-model="formValid">
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
                            v-model="departmentId"
                            :items="departments"
                            item-text="name"
                            item-value="id"
                            hint="Optional"
                            persistent-hint
                            label="Department"
                            >
                            </v-select>
                            <v-btn @click="register" :loading="btnLoading" :disabled="!formValid" block color="success"><v-icon left>mdi-plus</v-icon> Add new user</v-btn>
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
            btnLoading: false,
            alert: false,
            alertType: null,
            alertMessage: null,
            formValid: false,
            name: '',
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length >= 7) || 'Name must be at least 7 characters',
                v => /^[aA-zZ]+\s[aA-zZ]+$/.test(v) || 'First and Last name are required',
            ],
            email: '',
            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid', 
            ],
            departmentId: null,
            departments: []
        }
    },
     mounted(){
        axios({
            method: 'get',
            url: '/api/departments',
            headers:{
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
    },
    methods:{
        register(){
            this.btnLoading = true
            this.name = this.name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase()
            })
            this.email = this.email.toLowerCase()
            let formData = new FormData()
            formData.append('name', this.name)
            formData.append('email', this.email)
            if(this.departmentId)
                formData.append('department_id', this.departmentId)
            axios({
                method: 'post',
                url: '/api/users/create',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.btnLoading = false
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.btnLoading = false
            })
        }
    }
    
}
</script>