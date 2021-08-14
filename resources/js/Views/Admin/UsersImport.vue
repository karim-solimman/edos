<template>
    <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Import Users</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <v-form @submit.prevent="uploadTest" ref="form">
                    <v-file-input
                    label="Select File"
                    show-size
                    chips
                    clearable
                    :error-messages="errorMsg"
                    counter="1"
                    accept=".xls,.xlsx,application/msexcel"
                    v-model="file"
                    >
                    </v-file-input>
                    <v-btn :loading="btnLoading" type="submit"><v-icon left>mdi-upload</v-icon>upload</v-btn>
                </v-form>
                <v-divider class="mt-6"></v-divider>
                <h1 class="text-h4 font-weight-light">Important notes</h1>
                <p class="text-body-1">
                    <strong>Make sure</strong> that your excel file extension is either <strong>.xls</strong> or <strong>.xlsx</strong> otherwise your file will be ignored and importing process won't complete.
                </p>
                <p class="text-body-1">
                    <strong>Heading Rows</strong>, your excel file must contain heading row as shown in the figure. <strong>[#] [name] [email] [department]</strong> all lower case without any white spaces.
                </p>
                <p class="text-body-1">
                    <strong>Department</strong>, for department column you may write full / part of the department name stored in EDOS, either lower or upper case it will work, if EDOS failed to find the department the user won't be attached to any department until the admin update it.
                </p>
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-img src="/img/excel_users.png"></v-img>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Alert from '../../components/Alert.vue'
export default {
    components:{
        Alert
    },
    data(){
        return{
            file: null,
            btnLoading: false,
            errorMsg: null,

            alert: false,
            alertType: null,
            alertMessage: null,
        }
    },
    methods:{
        uploadTest(){
            this.btnLoading = true
            let formData = new FormData()
            formData.append('file', this.file)
            axios({
                method: 'post',
                url: '/api/users/import',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`,
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message + ' - ' + error.response.data.errors
                this.errorMsg = error.response.data.errors['file']
            })
            this.btnLoading = false
            this.$refs.form.reset()
        }
    }
}
</script>