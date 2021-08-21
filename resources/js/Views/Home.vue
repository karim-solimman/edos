<template>
    <v-container>
        <Alert
            @alert-closed="alert = false"
            :alert="alert"
            :alertMessage="alertMessage"
            :alertType="alertType"
        />
        <v-row style="margin-top: 10%" align="center" justify="space-around">
            <v-col cols="12" lg="5">
                <v-stepper v-model="e1">
                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <h1 class="text-h5 font-weight-light">
                                Registration
                            </h1>
                            <v-form
                                v-model="form1Valid"
                                @submit.prevent="verifyEmail"
                            >
                                <v-text-field
                                    label="Email"
                                    type="email"
                                    :rules="emailRules"
                                    v-model="email"
                                    prepend-inner-icon="mdi-email"
                                    required
                                >
                                </v-text-field>
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    :disabled="!form1Valid"
                                    :loading="btn1Loading"
                                >
                                    <v-icon left>mdi-login-variant</v-icon>
                                    Continue
                                </v-btn>
                            </v-form>
                        </v-stepper-content>
                        <v-stepper-content step="2">
                            <h1 class="text-h5 font-weight-light">
                                Set password
                            </h1>
                            <v-form
                                v-model="form2Valid"
                                @submit.prevent="setPasswordLogin"
                            >
                                <v-text-field
                                    label="Password"
                                    type="password"
                                    autoco
                                    v-model="password"
                                    :rules="passwordRules"
                                    required
                                    prepend-inner-icon="mdi-lock"
                                ></v-text-field>
                                <v-text-field
                                    label="Confirm Password"
                                    type="password"
                                    v-model="password_confirm"
                                    :rules="password_confirmRules"
                                    prepend-inner-icon="mdi-lock-check"
                                    required
                                ></v-text-field>
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    :disabled="!form2Valid"
                                    :loading="btn2Loading"
                                >
                                    <v-icon left>mdi-login-variant</v-icon>
                                    Continue
                                </v-btn>
                                <v-btn @click="nextStep(0)" text>
                                    Cancel
                                </v-btn>
                            </v-form>
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Alert from "../components/Alert.vue";
export default {
    components: {
        Alert
    },
    data() {
        return {
            valid: false,
            form1Valid: false,
            form2Valid: false,
            alert: false,
            alertType: null,
            alertMessage: null,
            btn1Loading: false,
            btn2Loading: false,

            name: "",
            nameRules: [v => !!v || "Name is required"],
            email: "",
            emailRules: [
                v => !!v || "E-mail is required",
                v => /.+@.+\..+/.test(v) || "E-mail must be valid"
            ],
            password: "",
            passwordRules: [v => !!v || "Password is required"],
            password_confirm: "",
            password_confirmRules: [
                v => !!v || "Confirmation password is required",
                value =>
                    this.password === this.password_confirm ||
                    "Password confirmation not match"
            ],
            user: Object,
            e1: 1
        };
    },
    watch: {
        steps(val) {
            if (this.e1 > val) {
                this.e1 = val;
            }
        }
    },
    methods: {
        register() {
            let bodyFormData = new FormData();
            bodyFormData.append("name", this.name);
            bodyFormData.append("email", this.email);
            bodyFormData.append("password", this.password);
            bodyFormData.append("password_confirmation", this.password_confirm);
            axios({
                method: "post",
                url: "/api/registration",
                data: bodyFormData
            })
                .then(response => {
                    localStorage.setItem("token", response.data.token);
                    localStorage.setItem(
                        "user",
                        JSON.stringify(response.data.user)
                    );
                    this.$store.dispatch(
                        "updateUser",
                        JSON.parse(localStorage.getItem("user"))
                    );
                    this.$store.dispatch("updateInvs", response.data.invs);
                    this.$emit("logged-in", true);
                    this.$router.push("/profile").catch(error => {});
                })
                .catch(error => {
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage =
                        error.response.data.message +
                        " " +
                        error.response.data.errors["email"];
                });
        },
        verifyEmail() {
            this.btn1Loading = true;
            let formData = new FormData();
            formData.append("email", this.email);
            axios({
                method: "post",
                url: "/api/users/checkemail",
                data: formData
            })
                .then(response => {
                    this.user = response.data.user;
                    this.alert = true;
                    this.alertType = "success";
                    this.alertMessage = "Welcome " + this.user.name;
                    this.nextStep(1);
                    this.btn1Loading = false;
                })
                .catch(error => {
                    this.btn1Loading = false;
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                });
        },
        setPasswordLogin() {
            this.btn2Loading = true;
            let formData = new FormData();
            formData.append("id", this.user.id);
            formData.append("password", this.password);
            formData.append("password_confirmation", this.password_confirm);
            axios({
                method: "post",
                url: "/api/users/passwordset",
                data: formData
            })
                .then(response => {})
                .catch(error => {
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                    this.btn2Loading = false;
                });
            let roles = [];
            formData.append("email", this.email);
            axios.get("/sanctum/csrf-cookie").then(response => {
                axios
                    .post("/api/login", formData)
                    .then(response => {
                        localStorage.setItem("token", response.data.token);
                        localStorage.setItem(
                            "user",
                            JSON.stringify(response.data.user)
                        );
                        this.$store.dispatch("updateUser", response.data.user);
                        this.$store.dispatch(
                            "updateInvs",
                            response?.data?.invs
                        );
                        $.each(response.data.roles, (index, value) => {
                            roles.push(response.data.roles[index]["slug"]);
                        });
                        this.$store.dispatch("updateRoles", roles);
                        this.$emit("logged-in", true);
                    })
                    .catch(error => {
                        this.alert = true;
                        this.alertType = "error";
                        this.alertMessage = error.response.data.message;
                        this.btn2Loading = false;
                    });
            });
        },
        nextStep(n) {
            if (n === this.steps) {
                this.e1 = 1;
            } else {
                this.e1 = n + 1;
            }
        }
    }
};
</script>
