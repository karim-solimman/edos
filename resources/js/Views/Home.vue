<template>
        <v-container>
            <v-row v-if="error">
                <v-col>
                    <v-alert
                        type="error"
                        dismissible
                        elevation="6"
                        border="top"
                    >
                        {{errorMsg}} - <strong>{{error}}</strong>
                    </v-alert>
                </v-col>
            </v-row>
            <v-row align="center" justify="space-around">
                <v-col lg=5>
                    <h1 class="text-h5 font-weight-light">Registration</h1>
                    <v-stepper v-model="e1">
                        <v-stepper-items>
                            <v-stepper-content step="1" >
                            <v-text-field
                                    label="Email"
                                    type="email"
                                    prepend-inner-icon="mdi-email"
                                    validate-on-blur
                                    required
                                >
                                </v-text-field>
                                 <v-btn
                                color="primary"
                                @click="nextStep(1)"
                            >
                                Continue
                            </v-btn>
                            </v-stepper-content>
                            <v-stepper-content step="2">
                                <v-text-field
                                    label="Password"
                                    type="password"
                                    prepend-inner-icon="mdi-lock"
                                ></v-text-field>
                                <v-text-field
                                    label="Confirm Password"
                                    type="password"
                                    required
                                ></v-text-field>

                            <v-btn
                                color="primary"
                            >
                                Continue
                            </v-btn>

                            <v-btn @click="nextStep(0)" text>
                                Cancel
                            </v-btn>
                            </v-stepper-content>
                        </v-stepper-items>
                    </v-stepper>
                </v-col>
            </v-row>
            <v-row align="center" justify="space-around">
                <v-col lg="5">
                    <v-alert type="error" dense dismissible v-if="data">{{ data }}</v-alert>
                    <v-card>
                        <v-card-title>Registration</v-card-title>
                        <v-card-text>
                            <v-form ref="form" @submit.prevent="register" v-model="valid">
                                <v-text-field
                                    label="Name"
                                    placeholder="Please insert your first and last name only"
                                    v-model="name"
                                    :rules="nameRules"
                                    name="name"
                                    type="text"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    label="Email"
                                    type="email"
                                    name="email"
                                    ref="email"
                                    prepend-inner-icon="mdi-email"
                                    validate-on-blur
                                    v-model="email"
                                    :rules="emailRules"
                                    required
                                >
                                </v-text-field>
                                <v-text-field
                                    label="Password"
                                    type="password"
                                    name="password"
                                    v-model="password"
                                    :rules="passwordRules"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    label="Confirm Password"
                                    type="password"
                                    name="password_confirmation"
                                    v-model="password_confirm"
                                    :rules="password_confirmRules"
                                    required
                                ></v-text-field>
                                <v-btn :disabled="!valid" block color="success" type="submit"><v-icon left>mdi-account-plus</v-icon>Registration</v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
</template>

<script>
    export default {
        data(){
            return {
                valid: false,
                data: null,
                name: '',
                nameRules: [v => !!v || 'Name is required'],
                email: '',
                emailRules: [v => !!v || 'E-mail is required',
                    v => /.+@.+\..+/.test(v) || 'E-mail must be valid'],
                password: '',
                passwordRules: [v => !!v || 'Password is required'],
                password_confirm: '',
                password_confirmRules: [v => !!v || 'Confirmation password is required',
                 value => this.password === this.password_confirm || 'Password confirmation not match' ],
                error: '',
                errorMsg: '',
                e1: 1
            }
        },
        watch: {
            steps (val) {
                if (this.e1 > val) {
                this.e1 = val
                }
            },
        },
        methods: {
            register(){
                let bodyFormData = new FormData()
                bodyFormData.append('name', this.name)
                bodyFormData.append('email', this.email)
                bodyFormData.append('password', this.password)
                bodyFormData.append('password_confirmation', this.password_confirm)
                axios({
                    method: "post",
                    url: "/api/registration",
                    data: bodyFormData,
                })
                .then((response) => {
                    localStorage.setItem('token', response.data.token)
                    localStorage.setItem('user', JSON.stringify(response.data.user))
                    this.$emit('logged-in',true)
                    this.$store.dispatch('updateUser', JSON.parse(localStorage.getItem('user')))
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.$router.push("/profile")
                })
                .catch((error) => {
                        this.errorMsg = error.response.data.message
                        this.error = error.response.data.errors.email[0]
                })
            },
            nextStep (n) {
                if (n === this.steps) {
                    this.e1 = 1
                     } else {
                        this.e1 = n + 1
                    }
            },
        }
    }
</script>
