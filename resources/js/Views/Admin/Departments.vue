<template>
    <v-container>
        <Loading :loading="loading"/>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType"/>
        <v-row v-if="!loading">
           <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
       </v-row>
        <v-row v-if="!loading && departments && departments.length > 0">
            <v-col>
                <v-data-table :headers="headers" :items="departments" :search="search">
                    <template v-slot:[`item.index`]="{index}">
                        {{index+1}}
                    </template>
                    <template v-slot:[`item.actions`]="{item}">
                        <v-btn style="text-decoration: none" icon small :to="{name: 'departmentProfile', params:{id: item.id}}"><v-icon small>mdi-folder-open-outline</v-icon></v-btn>
                        <v-btn style="text-decoration: none" icon small :to="{name: 'editDepartment', params:{id: item.id}}"><v-icon small>mdi-folder-edit-outline</v-icon></v-btn>    
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components: {
        Loading, Alert
    },
    data(){
        return{
            loading: true,
            search: '',
            departments: Array,
            headers:[
                {text: '#', value: 'index'},
                {text: 'name', value: 'name'},
                {text: 'courses', value: 'courses_count'},
                {text: 'invs', value: 'invs_count'},
                {text: 'users', value: 'users_count'},
                {text: 'actions', value: 'actions'}
            ],
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    mounted(){
        axios({
            method: 'get',
            url : '/api/departments',
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.departments = response.data.departments
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = "error"
            this.alertMessage = error.response.data.message
            this.loading = false
        })
    }
}
</script>