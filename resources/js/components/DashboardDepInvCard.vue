<template>
  <v-card style="margin-top: 24px;" >
    <v-sheet
      class="v-sheet--offset mx-auto"
      color="cyan"
      elevation="12"
      max-width="calc(100% - 32px)"
    >
      <v-sparkline
        :labels="labels"
        :value="value"
        color="white"
        line-width="2"
        padding="16"
        auto-draw
      ></v-sparkline>
    </v-sheet>

    <v-card-text class="pt-0">
      <div class="text-h6 font-weight-light mb-2">
        Departments Invs
      </div>
      <v-divider class="my-2"></v-divider>
      <v-icon
        class="mr-2"
        small
      >
        mdi-clock
      </v-icon>
      <span class="text-caption grey--text font-weight-light">last update {{last_inv.created_at | ago}}</span>
    </v-card-text>
  </v-card>
</template>

<style>
  .v-sheet--offset {
    top: -24px;
    position: relative;
  }
</style>

<script>
  export default {
    props:{
      department_invs: [],
      last_inv: Object,
    },
    data: () => ({
      labels: [],
      value: [],
    }),
    beforeMount(){
      this.draw()
    },
    methods:{
      draw(){
            $.each(this.department_invs,(index, value) => {
                this.labels.push(value['name'].split(" ").map((n)=>n[0]).join(""))
                this.value.push(value['invs_count'])
            })
        }
    },
    filters:{
      ago(val){
        return moment(val).fromNow()
      }
    }
  }
</script>