<template>
    <v-snackbar
        width="720"
        multi-line
        :timeout="timeout"
        v-model="value"
        elevation="3"
        :color="alertType"
        bottom
        left
    >
        <v-row>
            <v-col class="my-auto" cols="1">
                <v-icon left v-if="alertType == 'success'"
                    >mdi-check-circle-outline</v-icon
                >
                <v-icon left v-else>mdi-alert-circle-outline</v-icon>
            </v-col>
            <v-col class="my-auto">
                {{ alertMessage }}
            </v-col>
        </v-row>
        <template v-slot:action="{ attrs }">
            <v-btn text v-bind="attrs" @click="close">
                Close
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    data() {
        return {
            timeout: 4000,
            value: this.alert
        };
    },
    props: {
        alert: Boolean,
        alertType: String,
        alertMessage: String
    },
    methods: {
        close() {
            this.value = false;
            this.$emit("alert-closed");
        }
    },
    watch: {
        alert: function(newVal, oldVal) {
            if (newVal) {
                this.value = newVal;
            }
        },
        value: function(newVal, oldVal) {
            if (oldVal) {
                this.close();
            }
        }
    }
};
</script>
