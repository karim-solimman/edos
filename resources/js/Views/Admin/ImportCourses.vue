<template>
    <div>
        <Alert
            @alert-closed="alert = false"
            :alert="alert"
            :alertMessage="alertMessage"
            :alertType="alertType"
        />
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">
                    Import Courses
                </h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <v-form @submit.prevent="upload" ref="form">
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
                    <v-btn class="mt-2" :loading="btnLoading" type="submit"
                        ><v-icon left>mdi-upload</v-icon>upload file</v-btn
                    >
                </v-form>
                <v-divider class="mt-6"></v-divider>
                <h1 class="text-h4 font-weight-light">Important notes</h1>
                <p class="text-body-1">
                    <strong>Make sure</strong> that your excel file extension is
                    either <strong>.xls</strong> or
                    <strong>.xlsx</strong> otherwise your file will be ignored
                    and importing process won't complete.
                </p>
                <p class="text-body-1">
                    <strong>Heading Rows</strong>, your excel file must contain
                    heading row as shown in the figure.
                    <strong>[code] [name] [credit] [department]</strong>
                    all lower case without any white spaces.
                </p>
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-img class="mt-5" src="/img/courses.png"></v-img>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import Alert from "../../components/Alert.vue";
export default {
    components: {
        Alert
    },
    data() {
        return {
            file: null,
            btnLoading: false,
            errorMsg: null,

            alert: false,
            alertType: null,
            alertMessage: null
        };
    },
    methods: {
        upload() {
            this.btnLoading = true;
            let formData = new FormData();
            formData.append("file", this.file);
            axios({
                method: "post",
                url: "/api/courses/import",
                data: formData
            })
                .then(response => {
                    this.alert = true;
                    this.alertType = "success";
                    this.alertMessage = response.data.message;
                    this.btnLoading = false;
                })
                .catch(error => {
                    this.btnLoading = false;
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage =
                        error.response.data.message +
                        (error.response.data.errors
                            ? " - " + error.response.data.errors
                            : "");
                    this.errorMsg = error.response.data.errors["file"];
                });
            this.$refs.form.reset();
        }
    }
};
</script>
