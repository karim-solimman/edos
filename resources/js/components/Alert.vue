<template>
    <v-row v-if="alert">
        <v-col>
            <v-alert @click="close" :value="alert" dismissible :type="alertType">{{alertMessage}}</v-alert>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data(){
        return{
            alertClose: false
        }
    },
    props:{
        alert: Boolean,
        alertType: String,
        alertMessage: String
    },
    methods:{
        close(){
            this.$emit('alert-closed')
        }
    }
}
</script>