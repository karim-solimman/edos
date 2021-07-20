<template>
    <v-container>
        <Loading :loading="loading" />
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <v-row v-if="!loading && room">
            <v-col>
                <h1 class="text-h4 font-weight-light"><strong>Edit</strong> {{ room.number }}</h1>
                {{room.invs}}
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
            room: Object,
            loading: true,
            alert: false,
            alertType: null,
            alertMessage: null
        }
    },
    mounted(){
        let roomId = this.$route.params.id
        axios({
            method: 'get',
            url: `/api/rooms/${roomId}`,
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.room = response.data.room
            this.loading = false
        })
        .catch((error) => {
            this.alert = true
            this.alertType = 'error'
            this.alertMessage = error.response.data.message
        })

    }
    
}
</script>