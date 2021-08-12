<template>
    <v-dialog max-width="780" v-model="status">
        <v-card color="grey lighten-4" elevation="5">
            <v-card-title></v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                     <v-col cols="2" lg="1" md="1">
                        <v-icon class="my-auto" color="error" large>mdi-information-outline</v-icon>
                    </v-col>
                     <v-col class="my-auto" cols="10" lg="11" md="11">
                        <h4 class="text-h5 my-auto font-weight-light">{{confirmationText}}</h4>
                    </v-col>
                </v-row>  
                </v-container>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="status = false" text>Cancel</v-btn>
                <v-btn @click="onDelete" text color="error">Delete</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        dialog: false,
        dialogData: null,
        confirmationText: null,
        onDeleteFunction : null,
    },
    data(){
        return{
            status: this.dialog,
            item: this.dialogData,
            text: this.confirmationText,
        }
    },
    methods:{
        onDelete()
        {
            this.status = false
            this.onDeleteFunction(this.dialogData)
            this.$emit('dialog-closed')
        },
        close(){
            this.status = false
            this.$emit('dialog-closed')
        }
    },
    watch:{
        dialog: function (newVal, oldVal) {
            if(newVal) {
                this.status = newVal
            }
        },           
        dialogData: function(newval, oldVal){
            this.item = newval
        },
        status: function(newVal, oldVal){
            if(oldVal){
                this.$emit('dialog-closed')
            }
        }
    }
    
}
</script>