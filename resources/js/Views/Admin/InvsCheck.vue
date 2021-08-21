<template>
    <v-container>
        <Alert
            @alert-closed="alert = false"
            :alert="alert"
            :alertMessage="alertMessage"
            :alertType="alertType"
        />
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Check Invs</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <p class="text-body-1">
                    <b>Check invs</b>, will check the following for you and
                    report back to you in case of any warnings you should take
                    care of.
                </p>
                <p class="text-body-1">
                    <b>Rooms Conflicts</b>
                    <span class="text-body-2">
                        Will check if there is any duplications with courses and
                        rooms, for example <b>E011</b>, has two different
                        courses at the same date and the same time.
                    </span>
                </p>
                <p class="text-body-1">
                    <b>Rooms overlap</b>
                    <span class="text-body-2">
                        If the exam duration is provided with the exam
                        information the system will check if there any ovelaps
                        with the same room within the duration provided.
                    </span>
                </p>
                <p class="text-body-1">
                    <b>Exams dates and times Conflicts</b>
                    <span class="text-body-2">
                        Will check the invs related to one single course,
                        compare date and time for if any is different the system
                        will generate a warning message.
                    </span>
                </p>
                <v-btn @click="checkConflicts" :loading="btnLoading"
                    ><v-icon left>mdi-hammer-wrench</v-icon> Check now</v-btn
                >
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-card dark :color="reportType" v-if="report">
                    <v-card-title>
                        <v-icon left>mdi-check-circle</v-icon>
                        <h5 class="text-h6 font-weight-light">
                            Invs check report
                        </h5>
                    </v-card-title>
                    <v-card-text>
                        <p
                            class="text-body-1 white--text"
                            v-for="(msg, i) in invs_conflicts"
                            :key="msg.id"
                        >
                            {{ ++i }} - {{ msg }}
                        </p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Alert from "../../components/Alert.vue";
export default {
    components: {
        Alert
    },
    data() {
        return {
            alert: false,
            alertMessage: null,
            alertType: null,

            btnLoading: false,
            invs_conflicts: [],
            report: false,
            reportType: ""
        };
    },
    methods: {
        checkConflicts() {
            this.btnLoading = true;
            axios({
                method: "post",
                url: "/api/invs/checkinvsconflicts",
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem(
                        "token"
                    )}`
                }
            })
                .then(response => {
                    this.btnLoading = false;
                    this.alert = true;
                    this.alertMessage = response.data.message;
                    this.alertType = response.data.type;

                    if (response.data?.invs_conflicts) {
                        this.invs_conflicts = response.data.invs_conflicts;
                        this.report = true;
                        this.reportType = response.data.type;
                    } else {
                        this.report = true;
                        this.reportType = response.data.type;
                        this.invs_conflicts.push("All invs looks good");
                    }
                })
                .catch(error => {
                    this.btnLoading = false;
                    this.alert = true;
                    this.alertMessage = error.response.data.message;
                    this.alertType = "error";
                });
        }
    }
};
</script>
