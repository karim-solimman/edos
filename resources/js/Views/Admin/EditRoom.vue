<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert
            @alert-closed="alert = false"
            :alert="alert"
            :alertMessage="alertMessage"
            :alertType="alertType"
        />
        <Confirmation
            @dialog-closed="dialog = false"
            :dialog="dialog"
            :dialogData="dialogData"
            :confirmationText="dialogText"
            :onDeleteFunction="dialogFunction"
        />
        <v-row v-if="!loading && room">
            <v-col>
                <h1 class="text-h4 font-weight-light">
                    {{ room.number }} <v-chip x-small>edit</v-chip>
                </h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room">
            <v-col>
                <v-card
                    color="grey lighten-5"
                    height="100%"
                    class="d-felx flex-column"
                >
                    <v-card-title
                        ><h1 class="text-h5 font-weight-light">
                            Change room information
                        </h1></v-card-title
                    >
                    <v-card-text>
                        <v-text-field
                            label="Room number"
                            v-model="room.number"
                            :rules="room_numberRules"
                            :error-messages="errorMessages['number']"
                        ></v-text-field>
                        <v-text-field
                            label="Users limit"
                            v-model="room.users_limit"
                            :rules="users_limitRules"
                            :error-messages="errorMessages['users_limit']"
                        ></v-text-field>
                    </v-card-text>
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <v-btn
                            @click="updateInfo"
                            :loading="btnLoading"
                            block
                            text
                            color="info"
                            ><v-icon left>mdi-pencil</v-icon>Update room
                            information</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card
                    color="grey lighten-5"
                    height="100%"
                    class="d-flex flex-column"
                >
                    <v-card-title
                        ><h1 class="text-h5 font-weight-light">
                            Remove all room invs
                        </h1></v-card-title
                    >
                    <v-card-text
                        ><strong>Warning</strong>, by removing invs from
                        {{ room.number }}, all invs will be permentantly removed
                        and all users will unlinked to these invs This action
                        can't be undo.</v-card-text
                    >
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <v-btn
                            @click="confirmDeletAllInvs"
                            block
                            color="error"
                            dark
                            ><v-icon left>mdi-close</v-icon>remove all
                            invs</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room.invs && room.invs.length > 0">
            <v-col>
                <v-data-table :headers="headers" :items="room.invs">
                    <template v-slot:[`item.index`]="{ index }">
                        {{ index + 1 }}
                    </template>
                    <template v-slot:[`item.date`]="{ item }">
                        {{ item.date_time | DateFormat }}
                    </template>
                    <template v-slot:[`item.time`]="{ item }">
                        {{ item.date_time | TimeFormat }}
                    </template>
                    <template v-slot:[`item.users_count`]="{ item }">
                        {{ item.users.length }} / {{ item.users_limit }}
                    </template>
                    <template v-slot:[`item.actions`]="{ item }">
                        <v-btn
                            style="text-decoration: none"
                            :to="{
                                name: 'invProfile',
                                params: { id: item.id }
                            }"
                            icon
                            small
                            ><v-icon small>mdi-pencil</v-icon></v-btn
                        >
                        <v-btn
                            @click="confirmDeletInv(item)"
                            style="text-decoration: none"
                            icon
                            small
                            color="error"
                            ><v-icon small>mdi-delete</v-icon></v-btn
                        >
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <v-row v-if="!loading && room.invs && room.invs.length === 0">
            <v-col>
                <v-alert outlined color="warning"
                    >Sorry no invs to be displayed</v-alert
                >
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from "../../components/Loading.vue";
import Alert from "../../components/Alert.vue";
import Confirmation from "../../components/Confirmation.vue";
export default {
    components: {
        Loading,
        Alert,
        Confirmation
    },
    data() {
        return {
            room: Object,
            loading: true,
            btnLoading: false,
            headers: [
                { text: "#", value: "index" },
                { text: "date", value: "date" },
                { text: "time", value: "time" },
                { text: "users count", value: "users_count" },
                { text: "course", value: "course.code" },
                { text: "department", value: "course.department.name" },
                { text: "actions", value: "actions" }
            ],
            room_numberRules: [
                v => !!v || "Room number is required",
                v =>
                    /[a-zA-Z][0-9][0-9][0-9]/.test(v) ||
                    "Room number should contains E followed by 3 digits",
                v =>
                    (v && v.length === 4) ||
                    "Room number should contains E followed by 3 digits"
            ],
            users_limitRules: [
                v => !!v || "Users limit is required",
                v => /^\d+$/.test(v) || "Users limit must be digits "
            ],
            alert: false,
            alertType: null,
            alertMessage: null,

            errorMessages: [{ number: "" }, { users_limit: "" }],

            dialog: false,
            dialogData: null,
            dialogText: null,
            dialogFunction: null
        };
    },
    mounted() {
        let roomId = this.$route.params.id;
        axios({
            method: "get",
            url: `/api/rooms/${roomId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem("token")}`
            }
        })
            .then(response => {
                this.room = response.data.room;
                this.loading = false;
            })
            .catch(error => {
                this.alert = true;
                this.alertType = "error";
                this.alertMessage = error.response.data.message;
            });
    },
    methods: {
        updateInfo() {
            this.btnLoading = true;
            let formData = new FormData();
            formData.append("room_id", this.room.id);
            formData.append("room_number", this.room.number.toUpperCase());
            formData.append("users_limit", this.room.users_limit);

            axios({
                method: "post",
                url: "/api/rooms/edit",
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem(
                        "token"
                    )}`
                }
            })
                .then(response => {
                    this.btnLoading = false;
                    this.alert = true;
                    this.alertType = "success";
                    this.alertMessage = response.data.message;
                })
                .catch(error => {
                    this.btnLoading = false;
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                    this.errorMessages["number"] =
                        error.response.data.errors["room_number"];
                    this.errorMessages["users_limit"] =
                        error.response.data.errors["users_limit"];
                });
        },
        confirmDeletInv(inv) {
            this.dialog = true;
            this.dialogData = inv.id;
            this.dialogText = `Are you sure you want to delete on ${this.$options.filters.DateFormat(
                inv.date_time
            )} at ${this.$options.filters.TimeFormat(inv.date_time)} ?`;
            this.dialogFunction = this.deleteInv;
        },
        deleteInv(invId) {
            this.dialog = false;
            let formData = new FormData();
            formData.append("id", invId);
            axios({
                method: "post",
                url: "/api/invs/delete",
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem(
                        "token"
                    )}`
                }
            })
                .then(response => {
                    this.alert = true;
                    this.alertType = "success";
                    this.alertMessage = response.data.message;
                    this.room.invs = this.room.invs.filter(item => {
                        return item.id != invId;
                    });
                })
                .catch(error => {
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                });
        },
        confirmDeletAllInvs() {
            this.dialog = true;
            this.dialogData = this.room.id;
            this.dialogText = `Are you sure you want to delete on all invs from ${this.room.number} ?`;
            this.dialogFunction = this.deletAllInvs;
        },
        deletAllInvs() {
            this.dialog = false;
            let formData = new FormData();
            formData.append("room_id", this.room.id);
            axios({
                method: "post",
                url: "/api/rooms/deleteallinvs",
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem(
                        "token"
                    )}`
                }
            })
                .then(response => {
                    this.alert = true;
                    this.alertType = "success";
                    this.alertMessage = response.data.message;
                    this.room.invs = [];
                })
                .catch(error => {
                    this.alert = true;
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                });
        }
    },
    filters: {
        DateFormat(value) {
            return moment(value).format("ddd, MMM DD, YYYY");
        },
        TimeFormat(value) {
            return moment(value).format("hh : mm A");
        },
        ago(value) {
            return moment(value).fromNow();
        }
    }
};
</script>
