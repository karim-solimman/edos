<template>
    <v-dialog width="650" v-model="status">
        <v-card color="grey lighten-4" elevation="5">
            <v-card-title>
                <v-spacer></v-spacer>
                <v-icon color="primary" large>mdi-information</v-icon>
            </v-card-title>
            <v-card-text>
                <h4 class="text-4 font-weight-light"><v-icon left>mdi-calendar</v-icon> {{inv.date_time | DateFormat}}</h4>
                <h4 class="text-4 font-weight-light"><v-icon left>mdi-clock</v-icon> {{inv.date_time | TimeFormat}}</h4>
                <v-chip-group v-if="inv.users.length > 0">
                    <v-chip style="text-decoration: none" :to="{name: 'userProfile', params:{id: user.id}}" v-for="user in inv.users" :key="user.id">{{user.name}}</v-chip>
                </v-chip-group>
                <p v-else class="text-body-2">No users to be displayed</p>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="status = false" text>Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        dialog: false,
        inv: Object
    },
    data(){
        return{
            status: this.dialog,
            item: this.inv,
        }
    },
    methods:{
        close(){
            this.status = false
            this.$emit('user-dialog-closed')
        }
    },
    watch:{
        dialog: function (newVal, oldVal) {
            if(newVal) {
                this.status = newVal
            }
        },           
        inv: function(newval, oldVal){
            this.item = newval
        },
        status: function(newVal, oldVal){
            if(oldVal){
                this.$emit('user-dialog-closed')
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
        },
    
}
</script>