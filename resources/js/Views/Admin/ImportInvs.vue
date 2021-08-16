<template>
    <v-container>
        <Alert @alert-closed="alert=false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Import invs time table</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <v-form @submit.prevent="uploadFile" ref="form">
                    <v-file-input
                    color="info"
                    label="Select File"
                    @focus="errorMsg = null"
                    show-size
                    chips
                    clearable
                    :error-messages="errorMsg"
                    counter="1"
                    accept=".xls,.xlsx,application/msexcel"
                    v-model="file"
                    >
                    </v-file-input>
                    <v-btn class="mt-2" :loading="btnLoading" type="submit"><v-icon left>mdi-upload</v-icon>upload file</v-btn>
                </v-form>
                <v-divider class="mt-6"></v-divider>
                <h1 class="text-h4 font-weight-light">Important notes</h1>
                <p class="text-body-1">
                    <strong>Make sure</strong> that your excel file extension is either <strong>.xls</strong> or <strong>.xlsx</strong> otherwise your file will be ignored and importing process won't complete.
                </p>
                <p class="text-body-1">
                    <strong>Heading Rows</strong>, your excel file must contain heading row as shown in the figure. <strong>[#] [date] [time] [duration] [course] [room]</strong> all lower case without any white spaces.
                </p>
                <p class="text-body-1">
                    <strong><v-icon>mdi-calendar</v-icon> Date Format</strong>, Please make sure that your dates following this format <strong> Month/Day/Year</strong>, if only one row not follow this format the upload process will fail.
                </p>
                <p class="text-body-1">
                    <strong><v-icon>mdi-clock</v-icon> Time Format</strong>, Your inv time must be in <strong> 24 Hours format</strong>, for example if inv at 9:00 am in the morning your time should be <strong>9:00</strong> if it's 3:00 PM afternoon your time should be <strong>15:00</strong>, and if it's 12:00 pm at noon the time should be <strong>12:00</strong>.
                </p>
                <p class="text-body-1">
                    <strong><v-icon>mdi-timer</v-icon> Duration</strong>, Duration is not required, if the cell is empty the application will set the value to 0. <strong> Month/Day/Year</strong>, if only one row not follow this format the upload process will fail.
                </p>
                <p class="text-body-1">
                    <strong><v-icon>mdi-book</v-icon> Course</strong>, The course code only required <strong>[Letter Case Insensitive]</strong> and must match one of the course codes stored in the database, without course code there is no inv.<strong> So, please make sure it's matching.</strong>
                </p>
                <p class="text-body-1">
                    <strong><v-icon>mdi-door</v-icon> Room</strong>, Room number is required and must match one of the rooms in the database. <strong> [Case Insensitive]</strong>, If there is no match to only one of the rooms the upload process will fail.
                </p>
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-card dark :color="reportType" v-if="report">
                    <v-card-title>
                        <h5 class="text-h6 font-weight-light">Uploading report</h5>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1 white--text" v-for="(msg, i) in reportMessages" :key="msg.id">{{++i}} - {{msg}}</p>
                    </v-card-text>
                </v-card>
                <v-img class="mt-5" src="/img/invs_excel.png"></v-img>
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

            report: false,
            reportType: null,
            reportMessages: []
        }
    },
    methods:{
        uploadFile(){
            this.btnLoading = true
            let formData = new FormData()
            formData.append('file', this.file)
            axios({
                method: 'post',
                url: '/api/invs/import',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`,
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                this.btnLoading = false
                this.alert = true
                this.alertType = response.data.type
                let invs_conflicts = ''
                console.log(response.data.invs_conflicts);
                if(response.data?.invs_conflicts)
                {
                    $.each(response.data.invs_conflicts, function (index, value) {
                        invs_conflicts += `${value}, `
                    });
                    this.report = true
                    this.reportType = response.data.type
                    this.reportMessages = response.data.invs_conflicts
                }
                this.alertMessage = response.data.message

               
            })
            .catch((error) => {
                this.btnLoading = false
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message + (error.response.data.errors? ' - ' + error.response.data.errors : '') 
                this.errorMsg = error.response.data.errors['file']
            })
            this.$refs.form.reset()
        },
    }
}
</script>