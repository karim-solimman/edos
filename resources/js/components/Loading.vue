<template>
    <v-row align="center" justify="space-around" v-if="loading">
            <v-col cols="1">
                <v-progress-circular
                :size="70"
                :width="7"
                color="black"
                indeterminate
                ></v-progress-circular>
            </v-col>
        </v-row>
</template>

<script>
export default {
    name: 'loading',
    props: {
        loading: Boolean,
    }
}
</script>