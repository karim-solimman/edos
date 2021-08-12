<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Confirmation
         @dialog-closed="dialog = false"
         :confirmationText="dialogText"
         :onDeleteFunction="dialogFunction" 
         :dialogData="dialogData" 
         :dialog="dialog" 
         />
        <v-row v-if="!loading">
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title>
                        <h2 class="text-h5 font-weight-light">Random distribution</h2>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            With random distribution, EDOS will automatically distribute users over all the invs. <br/>
                            <strong>Warning</strong>, using automatic distribution will unlink and detach all users first then redistribute them. <br/>
                            <strong>Be Attention</strong>, that users count must be greater than the greatest count of users required per inv period. <br/>
                            <strong>Take care</strong>, if the users count is less than the greatest count of users required per any inv period the system can't distribute.
                        </p>
                        <v-row>
                            <v-col>
                                <v-alert v-if="slots === 0" border="top" colored-border elevation="2" type="error">
                                    Automatic distribution can't be executed. No invs in the system.
                                </v-alert>
                                <v-alert v-else-if="users < max_slot.users_count" border="top" colored-border elevation="2" type="error">
                                    can't run automatic distribution due to count of <strong>users is {{users}}</strong> less than of <strong>required {{max_slot.users_count}}</strong> <br/>
                                    <v-icon left small>mdi-table-eye</v-icon> {{max_slot.date_time | DateFormat}} <br/>
                                    <v-icon left small>mdi-clock</v-icon> {{max_slot.date_time | TimeFormat}}
                                </v-alert>
                                <v-alert v-else border="top" colored-border elevation="2" type="success">
                                    Automatic users distribution can be executed.
                                </v-alert>
                            </v-col>
                        </v-row>
                        <v-row v-if="slots > 0 && users >= max_slot.users_count">
                            <v-col>
                                <p class="text-h6">{{slots}}<span class="text-caption">(slots)</span> / {{users}}<span class="text-caption">(users)</span> = {{(slots/users).toFixed(2)}} <span class="text-caption">each user may have {{Math.floor((slots/users).toFixed(2))}} or {{Math.ceil((slots/users).toFixed(2))}} invs.</span></p>
                            </v-col>
                        </v-row>                        
                    </v-card-text>
                    <v-card-actions v-if="slots > 0 && users >= max_slot.users_count">
                        <v-btn style="text-decoration: none" :to="{name: 'random-distribution'}" block color="success"><v-icon left>mdi-shuffle</v-icon> execute random distribution</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Flush all invs</h1>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            <strong>Warning</strong>, flushing all the invs will delete all the invs, detach and unlink all the users, courses, departments, and rooms from all invs. <br/>
                            <strong>This action can't be undo.</strong>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn :loading="btn1Loading" @click="confirmFlushInvs" color="error" block><v-icon left>mdi-delete</v-icon>flush edos system invs</v-btn>
                    </v-card-actions>
                </v-card>
                 <v-card class="mt-5" color="grey lighten-4">
                    <v-card-title>
                        <h1 class="text-h5 font-weight-light">Clear all users invs</h1>
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            <strong>Warning</strong>, Clearing all users invs will permenantly remove and unlink all users invs. <br/>
                            <strong>No inv will be removed.</strong> <br/>
                            <strong>This action can't be undo.</strong>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn :loading="btn2Loading" @click="confirmDetachAllInvs" color="error" block><v-icon left>mdi-delete-forever</v-icon>clear all users invs</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">EDOS options</h1>
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col class="my-auto" cols="6" lg="3" md="3">
                                <v-switch
                                v-model="settings[0].value"
                                @click="updateSettings"
                                color="success"
                                inset>
                                <template v-slot:label>
                                    <span class="my-auto mt-2">Manual Selection</span>
                                </template>
                                </v-switch>
                            </v-col>
                            <v-col cols="6" lg="9" md="9">
                                <p class="text-body-1 mt-6">
                                    Apply the manual selection so the users can access and manually select invs based on their
                                    convience.<br/><strong>Notice:</strong> it's not allowed to distribute users automatically while manual 
                                    selection is ON.
                                </p>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="my-auto" cols="6" lg="3" md="3">
                                <v-switch
                                color="info"
                                v-model="settings[1].value"
                                @click="updateSettings"
                                inset>
                                <template v-slot:label>
                                    <span class="my-auto mt-2">Show Invs Details</span>
                                </template>
                                </v-switch>
                            </v-col>
                            <v-col cols="6" lg="9" md="9">
                                <p class="text-body-1 mt-6">
                                    Show invs details to users. The information that will be shown are 
                                    <strong>[Department Name, Course Code, Course Name, Exam Duration]</strong>.
                                    Users can only see these information after they inv selection not before.
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
import Confirmation from '../../components/Confirmation.vue'
export default {
    components:{
        Loading, Alert, Confirmation
    },
    data(){
        return{
            loading: true,
            max_slot: null,
            slots: null,
            users: null,
            btn1Loading: false,
            btn2Loading: false,

            alert: false,
            alertType: null,
            alertMessage: null,

            dialog: false,
            dialogText: null,
            dialogData: null,
            dialogFunction: null,

            settings: null
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: 'api/invs/statistics',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.loading = false
            this.users = response.data.users
            this.slots = response.data.sum
            this.settings = response.data.settings
            if(response.data.max_slot === 0)
            {
                this.max_slot = {}
                this.max_slot['users_count'] = 0
                this.max_slot['date_time'] = Date.now()
            }
            else {
                this.max_slot = response.data.max_slot
            }
        })
        .catch((error) => {
            this.loading = false
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    },
    methods:{
        confirmFlushInvs(){
            this.dialog = true
            this.dialogText = 'Are you sure you want to flush all invs?'
            this.dialogFunction = this.flushInvs
        },
        flushInvs(){
            this.btn1Loading = true
            axios({
                method: 'get',
                url: '/api/invs/flush',
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response)=>{
                this.btn1Loading = false
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.slots = 0
            })
            .catch((error)=>{
                this.btn1Loading = false
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        },
        confirmDetachAllInvs()
        {
            this.dialog = true
            this.dialogText = 'Are you sure you want to unlink all users?'
            this.dialogFunction = this.detachAllInvs
        },
        detachAllInvs()
        {
            this.btn2Loading = true
            axios({
                method: 'post',
                url: '/api/invs/detachallusers',
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message
                this.btn2Loading = false
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
                this.btn2Loading = false
            })
        },
        updateSettings(){
            let formData = new FormData()
            formData.append('manual_selection', this.settings[0].value ? 1 : 0)
            formData.append('show_details', this.settings[1].value ? 1 : 0)
            axios({
                method: 'post',
                url: '/api/settings/update',
                data: formData,
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.alert = true
                this.alertType = 'success'
                this.alertMessage = response.data.message

                this.$store.dispatch('updateSettings', response?.data?.settings)
                this.settings = response?.data?.settings
            })
            .catch((error) => {
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
            })
        }
    },
    filters:{
            DateFormat(value)
            {
                return moment(value).format("ddd, MMM DD, YYYY") 
            },
            TimeFormat(value)
            {
                return moment(value).format("hh : mm A")
            },
            ago(value)
            {
                return moment(value).fromNow()
            }
        }
    
}
</script>