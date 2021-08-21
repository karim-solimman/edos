<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert
            @alert-closed="alert = false"
            :alert="alert"
            :alertMessage="alertMessage"
            :alertType="alertType"
        />
        <v-row v-if="!loading && !users_count">
            <v-col>
                <v-alert
                    border="top"
                    colored-border
                    type="warning"
                    elevation="2"
                >
                    Sorry, No Users to be displayed!
                </v-alert>
            </v-col>
        </v-row>
        <v-row>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    dense
                    :headers="headers"
                    :items="users"
                    :search="search"
                >
                    <template v-slot:[`item.index`]="{ index }">
                        {{ index + 1 }}
                    </template>
                    <template v-slot:[`item.roles.admin`]="{ item }">
                        <v-switch
                            inset
                            dense
                            color="red darken-3"
                            @click="changeRole(item.id, 'admin')"
                            v-model="switchButtons"
                            :value="item.id + ':admin'"
                        ></v-switch>
                    </template>
                    <template v-slot:[`item.roles.user`]="{ item }">
                        <v-switch
                            inset
                            dense
                            color="green"
                            @click="changeRole(item.id, 'user')"
                            v-model="switchButtons"
                            :value="item.id + ':user'"
                        ></v-switch>
                    </template>
                    <template v-slot:[`item.roles.dataEntery`]="{ item }">
                        <v-switch
                            inset
                            dense
                            color="info"
                            @click="changeRole(item.id, 'de')"
                            v-model="switchButtons"
                            :value="item.id + ':de'"
                        ></v-switch>
                    </template>
                    <template v-slot:[`item.actions`]="{ item }">
                        <v-btn
                            color="info"
                            style="text-decoration: none"
                            icon
                            small
                            :to="{
                                name: 'userProfile',
                                params: { id: item.id }
                            }"
                            ><v-icon small>mdi-account</v-icon></v-btn
                        >
                        <v-btn
                            style="text-decoration: none"
                            class="ml-2"
                            icon
                            small
                            :to="{ name: 'editUser', params: { id: item.id } }"
                            ><v-icon small>mdi-pencil</v-icon></v-btn
                        >
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from "../../components/Loading.vue";
import Alert from "../../components/Alert.vue";
export default {
    components: {
        Loading,
        Alert
    },
    data() {
        return {
            users: [],
            loading: true,
            headers: [
                { text: "#", value: "id", filterable: true },
                { text: "name", value: "name", filterable: true },
                { text: "email", value: "email", filterable: true },
                { text: "department", value: "department.name" },
                { text: "count", value: "invs_count" },
                { text: "limit", value: "invs_limit" },
                { text: "admin", value: "roles.admin", sortable: false },
                { text: "user", value: "roles.user", sortable: false },
                {
                    text: "data entery",
                    value: "roles.dataEntery",
                    sortable: false
                },
                { text: "actions", value: "actions", sortable: false }
            ],
            search: "",
            switchButtons: [],
            alert: false,
            alertType: null,
            alertMessage: null
        };
    },
    computed: {
        users_count() {
            return this.users.length > 0 ? true : false;
        }
    },
    mounted() {
        this.getUsers();
    },
    methods: {
        getUsers() {
            axios({
                method: "get",
                url: "/api/users",
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem(
                        "token"
                    )}`
                }
            })
                .then(response => {
                    this.users = response.data;
                    this.loading = false;
                    $.each(this.users, (user, val) => {
                        this.userRolesSwitchers(val["id"]);
                    });
                })
                .catch(error => {
                    this.alertType = "error";
                    this.alertMessage = error.response.data.message;
                    this.$emit("logged-out", flase);
                    this.$router.push({ name: "home" });
                });
        },
        userRolesSwitchers(userId) {
            let userIndex = 0;
            $.each(this.users, index => {
                if (this.users[index].id === userId) userIndex = index;
            });
            $.each(this.users[userIndex].roles, (index, val) => {
                this.switchButtons.push(userId + ":" + val["slug"]);
            });
        },
        changeRole(userId, roleName) {
            let roleNeedle = String(userId + ":" + roleName);
            if (this.switchButtons.includes(roleNeedle)) {
                let formData = new FormData();
                formData.append("user_id", userId);
                formData.append("role_slug", roleName);
                axios({
                    method: "post",
                    url: "/api/roles/add",
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`
                    }
                })
                    .then(response => {
                        this.alert = true;
                        this.alertType = "success";
                        this.alertMessage = response.data.message;
                    })
                    .catch(error => {
                        this.alert = true;
                        this.alertType = "error";
                        this.alertMessage = error.response.data.message;
                    });
            } else {
                let formData = new FormData();
                formData.append("user_id", userId);
                formData.append("role_slug", roleName);
                axios({
                    method: "post",
                    url: "/api/roles/remove",
                    data: formData,
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`
                    }
                })
                    .then(response => {
                        this.alert = true;
                        this.alertType = "success";
                        this.alertMessage = response.data.message;
                    })
                    .catch(error => {
                        this.alert = true;
                        this.alertType = "error";
                        this.alertMessage = error.response.data.message;
                    });
            }
            this.getUsers();
        }
    }
};
</script>
