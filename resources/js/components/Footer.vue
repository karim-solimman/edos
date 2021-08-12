<template>
  <v-footer absolute padless height="0" width="auto">
    <v-card width="100%" flat color="grey lighten-4">
      <v-card-text class="text-center">
        <v-btn color="primary" icon><v-icon>mdi-facebook</v-icon></v-btn>
        <v-btn color="info" class="ml-5" icon><v-icon>mdi-linkedin</v-icon></v-btn>
        <v-btn color="deep-purple lighten-1" dark class="ml-5" icon><v-icon>mdi-instagram</v-icon></v-btn>
        <v-divider></v-divider>
        {{ new Date().getFullYear() }} — <router-link id="router-link" :to="{name: 'about'}">Find more about EDOS</router-link>
      </v-card-text>
    </v-card>
  </v-footer>
</template>

<style scoped>
    #router-link{
        text-decoration: none;
        color: grey;
        transition-duration: 0.5s;
    }    
    #router-link:hover{
        color: #0068bd;
        /* font-weight: bold; */
        transition-duration: 0.5s;
    }
</style>

<script>
export default {
    
}
</script>