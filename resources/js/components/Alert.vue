<template>
    <!-- <v-row v-if="alert">
        <v-col>
            <v-alert elevation="3" border="top" @click="close" :value="alert" dismissible :type="alertType">{{alertMessage}}</v-alert>
        </v-col>
    </v-row> -->
    <v-snackbar :timeout="timeout" v-model="alert" elevation="3" :color="alertType">
      <v-icon left v-if="alertType == 'success'">mdi-check-circle-outline</v-icon>
      <v-icon left v-else>mdi-alert-circle-outline</v-icon>
      {{ alertMessage }}
      <template v-slot:action="{ attrs }">
        <v-btn
          text
          v-bind="attrs"
          @click="close"
        >
        Close
        </v-btn>
      </template>
    </v-snackbar>
</template>

<script>
export default {
    data(){
        return{
            alertClose: false,
            timeout: 3000
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