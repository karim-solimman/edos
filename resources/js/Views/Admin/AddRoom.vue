<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row>
            <v-col cols="12" md="5" lg="5">
                <h1 class="text-h4 font-weight-light">Add new Room</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="5" lg="5">
                <v-form @submit.prevent="addRoom" ref="form" v-model="formValid">
                    <v-text-field
                    label="Room number"
                    v-model="room_number"
                    :rules="room_numberRules"
                    ></v-text-field>
                    <v-btn :disabled="!formValid" :loading="btnLoading" type="submit" block color="success"><v-icon left>mdi-plus</v-icon>Add new</v-btn>
                </v-form>
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