<template>
  <v-footer absolute padless height="0" width="auto">
    <v-card width="100%" flat color="grey lighten-4">
      <v-card-text class="text-center">
        <strong>EDOS</strong> | {{ new Date().getFullYear() }} — <router-link id="router-link" :to="{name: 'about'}">About EDOS</router-link>
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