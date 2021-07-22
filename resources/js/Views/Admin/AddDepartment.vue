<template>
    <v-container>
        <Loading :loading="loading"/>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around">
            <v-col cols="12" lg="5" md="5">
                <v-card color="grey lighten-5">
                    <v-card-title>
                        <h4 class="text-h4 font-weight-light">Add new department</h4>
                    </v-card-title>
                    <v-card-text>
                        <v-form  @submit.prevent="addDepartment" ref="form" v-model="formValid"> 
                            <v-text-field
                            label="Name"
                            v-model="department_name"
                            :rules="nameRules"
                            required
                            >
                            </v-text-field>
                            <v-btn block :disabled="!formValid" :loading="btnLoading" color="success" type="submit"><v-icon left>mdi-plus</v-icon> Add new department</v-btn>
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
            department_name: '',
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length >= 10) || 'Name must be at least 10 characters',
            ],
            formValid: false,
            btnLoading: false,
            loading: false,
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    methods:{
        addDepartment(){
            this.btnLoading = true
            let formData = new FormData()
            formData.append('name', this.department_name)
            axios({
                method: 'post',
                url: '/api/departments/create',
                data: formData,
                headers: {
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