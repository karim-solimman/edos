<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row justify="space-around">
            <v-col cols="12" lg="5" md="5">
                <v-card color="grey lighten-5">
                    <v-card-title><h1 class="text-h4 font-weight-light">Add new Room</h1></v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="addRoom" ref="form" v-model="formValid">
                            <v-text-field
                            label="Room number"
                            v-model="room_number"
                            :rules="room_numberRules"
                            ></v-text-field>
                            <v-text-field
                            label="Users limit"
                            v-model="users_limit"
                            :rules="users_limitRules"
                            type="number"
                            >
                            </v-text-field>
                            <v-btn :disabled="!formValid" :loading="btnLoading" type="submit" block color="success"><v-icon left>mdi-plus</v-icon>Add new</v-btn>
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
    components:{
        Alert, Loading
    },
    data(){
        return{
            loading: false,
            formValid: false,
            btnLoading: false,
            room_number: null,
            room_numberRules: [
                v => !!v || 'Room number is required',
                v => /[a-zA-Z][0-9][0-9][0-9]/.test(v) || 'Room number should contains E followed by 3 digits',
                v => (v && v.length === 4) || 'Room number should contains E followed by 3 digits',
            ],
            users_limit: null,
            users_limitRules: [
                v => !!v || 'Users limit is required',
                v => /^\d+$/.test(v) || 'Users limit must be digits ',
            ],
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    methods:{
        addRoom(){
            this.btnLoading = true
            let formData = new FormData()
            formData.append('room_number', this.room_number.toUpperCase())
            formData.append('users_limit', this.users_limit)
            axios({
                method: 'post',
                url: '/api/rooms/create',
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
                this.$refs.form.reset()
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message + ' ' + error.response.data.errors['room_number']
                this.btnLoading = false
            })
        }
    }
    
}
</script>