<template>
    <v-container>
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Import invs time table</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <v-form @submit.prevent="" ref="form">
                    <v-file-input
                    label="Select File"
                    show-size
                    chips
                    clearable
                    :error-messages="errorMsg"
                    counter="1"
                    accept=".xls,.xlsx,application/msexcel"
                    v-model="file"
                    >
                    </v-file-input>
                    <v-btn><v-icon left>mdi-upload</v-icon>upload file</v-btn>
                </v-form>
            </v-col>
        </v-row>
    </v-container>    
</template>

<script>
export default {
    data(){
        return{
            file: null,
        }
    }
}
</script>