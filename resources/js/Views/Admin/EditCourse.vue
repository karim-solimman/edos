<template>
    <v-container>
        <Loading :loading="loading" />
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">{{course.code}} - {{course.name}}</h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title><h1 class="text-h5 font-weight-light">Edit information</h1></v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-text-field label="Code" v-model="course.code"></v-text-field>
                            <v-text-field label="name" v-model="course.name"></v-text-field>
                            <v-text-field label="credit hourse" v-model="course.credit_hours" type="number"></v-text-field>
                            <v-select
                            label="Department"
                            :items="departments"
                            item-value="id"
                            item-text="name"
                            ></v-select>
                            <v-btn color="primary" block><v-icon left>mdi-plus</v-icon>Update</v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="6" md="6">
                <v-card color="grey lighten-5">
                    <v-card-title><h1 class="text-h5 font-weight-light">Course invs</h1></v-card-title>
                    <v-card-text>
                        <v-data-table :headers="headers" :items="course.invs">
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../../components/Loading.vue'
import Alert from '../../components/Alert.vue'
export default {
    components: {
        Alert, Loading
    },
    data(){
        return{
            loading: true,
            course: Object,
            departments:[],
            headers:[
                {text: '#', value: 'index'},
                {text: 'date', value: 'date'},
            ],
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    mounted(){
        let id = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/courses/${id}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.course = response.data.course
            this.departments = response.data.departments
            this.loading = false
        })
        .catch((error) => {
            this.alert= true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })
    }
}
</script>