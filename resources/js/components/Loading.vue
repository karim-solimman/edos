<template>
    <v-row align="center" justify="center" v-if="loading">
            <v-col cols="1">
                <v-progress-circular
                :size="100"
                :width="5"
                color="primary"
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