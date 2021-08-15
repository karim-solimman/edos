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
           <v-col cols="12" lg="5" md="4">
                <h1 class="text-h3 font-weight-light">{{ user.name }}</h1>
                <h2 class="text-overline">{{ user.email }}</h2>
                <v-chip small color="primary" class="text-overline mr-2" v-for="role in roles" :key="role.id"><v-icon small left>mdi-account</v-icon>{{role}}</v-chip>
                <v-chip small outlined class="text-overline"><v-icon small left>mdi-folder</v-icon>
                    <span v-if="user.department">{{user.department.name}}</span>
                    <span v-else>No department</span>
                </v-chip>
            </v-col>
            <v-col class="d-flex my-auto">
                <h1 class="text-h1 font-weight-light">{{ invs.length }}<span v-if="settings[0].value" class="text-body-1"> of ({{user.invs_limit}})</span> <span class="text-overline">invs</span></h1>
            </v-col>
            <v-col v-if="!loading && invs && invs.length > 0" class="d-flex flex-row my-auto">
                <v-tooltip bottom>
                   <template v-slot:activator="{ on, attrs }">
                        <vue-excel-xlsx :filename="`${user.name} TimeTable`" :data="export_data" :columns="export_fields">
                            <v-btn v-on="on" v-bind="attrs" class="mt-6 mr-5" color="success" icon><v-icon>mdi-microsoft-excel</v-icon></v-btn>
                        </vue-excel-xlsx>
                   </template>
                   <span>Excel Download</span>
                </v-tooltip>
               <v-switch class="mt-11" @click="switchView" :prepend-icon="switchIcon" inset v-model="switchToggle" :persistent-hint="true" :hint="switchHint"></v-switch>
           </v-col>
       </v-row>
       <v-divider></v-divider>
       <v-row v-if="!loading && invs.length === 0 && roles && roles.length > 0">
           <v-col>
               <v-alert
                border="top"
                color="red darken-2"
                type="info"
                elevation="3"
                dismissible
                >
                Sorry, You didn't select any invs!
                </v-alert>
           </v-col>
       </v-row>
       <v-row v-if="!loading && roles && roles.length === 0">
           <v-col>
               <v-alert border="left" color="orange" text type="error" elevation="2">
                   <strong>Sorry</strong>, Admin didn't assign your role in the system, contact admin for this issue
               </v-alert>
           </v-col>
       </v-row>
       <v-row v-if="!loading && settings[0].value && invs.length > 0 && view == 'grid'">
           <v-col lg=4 md=4 cols=6 v-for="inv in invs" :key="inv.id">
               <v-hover v-slot="{hover}">
                    <v-card color="grey lighten-5" hover @click="confirm(inv)">
                        <v-card-title>
                                <h1 class="text-h5 font-weight-light">{{inv.date_time | DateFormat}}</h1>
                                </v-card-title>
                            <v-card-subtitle>Time: {{inv.date_time | TimeFormat}}</v-card-subtitle>
                            <v-card-text>
                                <v-chip-group active-class="primary--text" column>
                                    <v-chip dark small :color="inv.users_count < inv.room.users_limit? 'green darken-2' : 'red darken-2' ">
                                        <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.room.users_limit}}
                                    </v-chip>
                                    <v-chip small color="info" outlined>
                                        <v-icon small left>mdi-clock-outline</v-icon>{{inv.pivot.created_at | ago}}
                                    </v-chip>
                                    <v-chip v-if="settings[1].value" small color="primary">
                                        <v-icon small left>mdi-door</v-icon>{{inv.room.number}}
                                    </v-chip>
                                    <v-chip v-if="settings[1].value" small color="warning">
                                        <v-icon small left>mdi-book</v-icon>{{inv.course.code}}
                                    </v-chip>
                                    <v-chip v-if="settings[1].value" small outlined color="info">
                                        <v-icon small left>mdi-folder</v-icon>{{inv.course.department.name}}
                                    </v-chip>
                                    <v-chip v-if="settings[1].value" small outlined color="teal lighten-3">
                                        <v-icon small left>mdi-alarm</v-icon>{{inv.duration? inv.duration+' hours' : 'no duration'}}
                                    </v-chip>
                                </v-chip-group>
                            </v-card-text>
                            <v-expand-transition>
                                <div
                                    v-if="hover"
                                    class="d-flex transition-fast-in-fast-out blue-grey darken-4 v-card--reveal text-h5 font-weight-light white--text"
                                    style="height: 100%;"
                                >
                                <span class="text-h4 font-weight-thin text-center"><v-icon dark x-large left color="error">mdi-table-large-remove</v-icon><br/>REMOVE</span>
                                </div>
                            </v-expand-transition>
                    </v-card>
               </v-hover>
           </v-col>
       </v-row>
       <v-row v-if="!loading && !settings[0].value && invs.length > 0 && view == 'grid'">
           <v-col lg=4 md=4 cols=6 v-for="inv in invs" :key="inv.id">
                <v-card color="grey lighten-5" hover>
                    <v-card-title>
                            <h1 class="text-h5 font-weight-light">{{inv.date_time | DateFormat}}</h1>
                            </v-card-title>
                        <v-card-subtitle>Time: {{inv.date_time | TimeFormat}}</v-card-subtitle>
                        <v-card-text>
                            <v-chip-group active-class="primary--text" column>
                                <v-chip dark small :color="inv.users_count < inv.room.users_limit? 'green darken-2' : 'red darken-2' ">
                                    <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.room.users_limit}}
                                </v-chip>
                                <v-chip small color="info" outlined>
                                    <v-icon small left>mdi-clock-outline</v-icon>{{inv.pivot.created_at | ago}}
                                </v-chip>
                                <v-chip v-if="settings[1].value" small color="primary">
                                    <v-icon small left>mdi-door</v-icon>{{inv.room.number}}
                                </v-chip>
                                <v-chip v-if="settings[1].value" small color="warning">
                                    <v-icon small left>mdi-book</v-icon>{{inv.course.code}}
                                </v-chip>
                                <v-chip v-if="settings[1].value" small outlined color="info">
                                    <v-icon small left>mdi-folder</v-icon>{{inv.course.department.name}}
                                </v-chip>
                                <v-chip v-if="settings[1].value" small outlined color="teal lighten-3">
                                    <v-icon small left>mdi-alarm</v-icon>{{inv.duration? inv.duration+' hours' : 'no duration'}}
                                </v-chip>
                            </v-chip-group>
                        </v-card-text>
                </v-card>
           </v-col>
       </v-row>
       <v-row v-if="!loading && invs.length > 0 && view == 'table'">
           <v-col>
               <v-simple-table>
                   <thead>
                       <tr>
                           <th>#</th>
                           <th>Date</th>
                           <th>Time</th>
                           <th v-if="settings[1].value">Room</th>
                           <th v-if="settings[1].value">Course</th>
                           <th v-if="settings[1].value">Dep</th>
                           <th v-if="settings[1].value">Dur</th>
                           <th>Users</th>
                           <th>Enrolled</th>
                           <th v-if="settings[0].value">Actions</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr v-for="(inv, i) in invs" :key="inv.id">
                           <td>{{++i}}</td>
                           <td>{{inv.date_time | DateFormat}}</td>
                           <td>{{inv.date_time | TimeFormat}}</td>
                           <td v-if="settings[1].value">{{inv.room.number}}</td>
                           <td v-if="settings[1].value">{{inv.course.code}}</td>
                           <td v-if="settings[1].value">{{inv.course.department.name}}</td>
                           <td v-if="settings[1].value">{{inv.duration? inv.duration + ' hours' : 'No duration'}}</td>
                           <td>
                               <v-chip dark small :color="inv.users_count < inv.room.users_limit? 'green darken-2' : 'red darken-2' ">
                                    <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.room.users_limit}}
                                </v-chip>
                           </td>
                           <td>{{inv.pivot.created_at | ago}}</td>
                           <td v-if="settings[0].value">
                               <v-btn style="text-decoration: none" @click="confirm(inv)" icon small color="error"><v-icon small>mdi-delete</v-icon></v-btn>
                           </td>
                       </tr>
                   </tbody>
               </v-simple-table>
           </v-col>
       </v-row>
   </v-container>
</template>

<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  position: absolute;
  opacity: 0.9;
  width: 100%;
}
</style>

<script>
import Loading from '../components/Loading.vue'
import Alert from '../components/Alert.vue'
import Confirmation from '../components/Confirmation.vue'
    export default {
        components:{
            Loading, Alert, Confirmation
        },
        data(){
            return {
                loading: true,
                invs: [],
                roles: [],
                user: this.$store.getters.getUser,
                alertType: null,
                alertMessage: null,
                alert: false,
                dialog: false,
                dialogData: null,
                dialogFunction: null,
                dialogText: '',

                switchToggle: false,
                switchHint: 'Table view',
                switchIcon: 'mdi-table-eye',
                view: 'grid',

                settings: null,
                export_fields:
                [
                    {
                        label: "#",
                        field: "index"
                    },
                    {
                        label: "date",
                        field: "date"
                    },
                    {
                        label: "time",
                        field: "time"
                    },
                    {
                        label: "duration",
                        field: "duration"
                    },
                    {
                        label: "code",
                        field: "code"
                    },
                    {
                        label: "course",
                        field: "course"
                    },
                    {
                        label: "room",
                        field: "roomnumber"
                    },
                    {
                        label: "department",
                        field: "department"
                    },
                    {
                        label: "inv name",
                        field: "inv_name"
                    }
                ],
                export_data: [],
            }
        },
        mounted() {
            let formData = new FormData()
            formData.append('user_id', this.user.id)
            axios({
                method: 'post',
                url: '/api/profile',
                data: formData,
                headers: {
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.invs = response?.data?.invs
                this.$store.dispatch('updateInvs', this.invs)
                $.each(response.data.roles, (index, value) => {
                    this.roles.push(response.data.roles[index]['slug'])
                })
                this.$store.dispatch('updateRoles', this.roles)
                this.$store.dispatch('updateSettings', response.data.settings)
                this.settings = this.$store.getters.getSettings
                this.$emit('logged-in', true)
                this.loading = false
                this.setExportData()
            })
            .catch((error) => {
                this.$router.push('/')
                this.$emit('logged-out', false)
                localStorage.clear()
            })
        },
        methods:{
            confirm(inv){
                this.dialog = true
                this.dialogText = `Are you sure you want to delete inv on\n${this.$options.filters.DateFormat(inv.date_time)} at ${this.$options.filters.TimeFormat(inv.date_time)}`
                this.dialogFunction = this.deleteInv
                this.dialogData = inv.id
            },
            deleteInv(invId)
            {
              let formData = new FormData()
              formData.append('inv_id', invId)
              formData.append('user_id', this.user.id)
                axios({
                    method: 'post',
                    url: '/api/users/removeinv',
                    data: formData,
                    headers:{
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                .then((response) => {
                    this.$store.dispatch('updateInvs', response.data.invs)
                    this.invs = response.data.invs
                    this.alertMessage = response.data.message
                    this.alertType = 'success'
                    this.alert = true
                })
                .catch((error) => {
                    this.alert = true
                    this.alertType = 'error'
                    this.alertMessage = error.response.data.message
                })
            },
            switchView(){
                if(!this.switchToggle)
                {
                    this.switchHint = "Table View"
                    this.switchIcon = "mdi-table-eye"
                    this.view = 'grid'
                }
                else 
                {
                    this.switchHint = "Grid View"
                    this.switchIcon = "mdi-view-grid"
                    this.view = 'table'
                }
                
            },
            setExportData(){
                this.export_data = []
                if(this.settings[1].value){
                    $.each(this.invs, (index, value) => {
                        this.export_data.push({
                            'index': index+1,
                            'date': this.$options.filters.DateFormat(value.date_time),
                            'time': this.$options.filters.TimeFormat(value.date_time),
                            'duration': value.duration? value.duration: 'no duration',
                            'code': value.course.code,
                            'course': value.course.name,
                            'roomnumber': value.room.number,
                            'department': value.course.department.name,
                            'inv_name': this.user.name
                        })
                    })
                }
                else{
                    $.each(this.invs, (index, value) => {
                        this.export_data.push({
                            'index': index+1,
                            'date': this.$options.filters.DateFormat(value.date_time),
                            'time': this.$options.filters.TimeFormat(value.date_time),
                            'inv_name': this.user.name
                        })
                    })
                }
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
