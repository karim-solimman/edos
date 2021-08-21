<template>
    <v-container>
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Check Invs</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" lg="6" md="6">
                <p class="text-body-1">
                    <b>Check invs</b>, will check the following for you and
                    report back to you in case of any warnings you should take
                    care of.
                </p>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Single-line item</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>Two-line item</v-list-item-title>
                        <v-list-item-subtitle
                            >Secondary text</v-list-item-subtitle
                        >
                    </v-list-item-content>
                </v-list-item>

                <v-list-item three-line>
                    <v-list-item-content>
                        <v-list-item-title>Three-line item</v-list-item-title>
                        <v-list-item-subtitle>
                            Secondary line text Lorem ipsum dolor sit amet,
                        </v-list-item-subtitle>
                        <v-list-item-subtitle>
                            consectetur adipiscing elit.
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {};
</script>
