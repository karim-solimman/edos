<template>
    <v-container>
        <v-row v-if="!loading">
            <v-col>
                <h5 class="text-h5 font-weight-light">{{department.name}} <v-chip x-small>Edit</v-chip> </h5>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="4" md="4">
                <v-card color="grey lighten-4"> 
                    <v-card-title><h1 class="text-h5 font-weight-light">Update name</h1></v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="Name"
                        v-model="department.name"
                        ></v-text-field>
                    </v-card-text>
                    <v-card-actions><v-btn block text color="info"><v-icon left>mdi-pencil</v-icon>update name</v-btn></v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" lg="4" md="4">
                <v-card color="grey lighten-4">
                   <v-card-title><h1 class="text-h5 font-weight-light">Flush all invs</h1></v-card-title>
                   <v-card-text><strong>Warning</strong>, By flushing all the invs you are going to remove all the data related to {{department.name}} department and this action can't be undo.</v-card-text>
                   <v-card-actions><v-btn color="error" block text><v-icon left>mdi-close</v-icon>Flsuh all invs</v-btn></v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card color="grey lighten-4">
                    <v-card-title class="text-h5 font-weight-light">Delete department</v-card-title>
                    <v-card-text><strong>Warning</strong>, deleting / removing {{department.name}} department will remove all the invs of this department also will unlink all courses attached to the department and this can't be undo.</v-card-text>
                    <v-card-actions><v-btn block color="error"><v-icon left>mdi-close</v-icon>Delete department</v-btn></v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table :headers="headers" :items="department.courses">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.date`]="{item}">
                        <span v-if="item.invs.length > 0">{{item.invs[0].date_time | DateFormat}}</span>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.time`]="{item}">
                        <span v-if="item.invs.length > 0">{{item.invs[0].date_time | TimeFormat}}</span>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.rooms`]="{item}">
                        <v-chip-group v-if="item.invs.length > 0">
                            <v-chip small v-for="inv in item.invs" :key="inv.id">{{inv.room.number}}</v-chip>
                        </v-chip-group>
                        <v-chip color="error" x-small v-else><v-icon x-small left>mdi-information-outline</v-icon>No invs</v-chip>
                    </template>
                    <template v-slot:[`item.actions`]="{}">
                        <v-btn icon small color="error"><v-icon>mdi-close</v-icon></v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import AddCourse from './AddCourse.vue'
export default {
  components: { AddCourse },
    data(){
        return{
            loading: true,
            department_id: this.$route.params.id,
            department: Object,
            headers:[
                {text: '#', value: 'index'},
                {text: 'code', value: 'code'},
                {text: 'name', value: 'name'},
                {text: 'credit hours', value: 'credit_hours'},
                {text: 'date', value: 'date'},
                {text: 'time', value: 'time'},
                {text: 'rooms', value: 'rooms'},
                {text: 'actions', value: 'actions'}
            ]
        }
    },
    mounted(){
        axios({
            method: 'get',
            url: `/api/departments/${this.department_id}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.department = response.data.department
            this.loading = false
        })
        .catch((error) => {
            console.log(error.response.data.message)
        })
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