<template>
    <v-container>
        <v-row>
            <v-col>
                <h1 class="text-h4 font-weight-light">Random distribution</h1>
            </v-col>
        </v-row>
        <v-row>
            <p v-for="inv in data.invs" :key="inv.id">{{inv}}</p>
        </v-row>
        <v-row>
            {{data.users}}
        </v-row>
    </v-container>
</template>

<script>
export default {
    data(){
        return{
            data: ''
        }
    },
    mounted(){
        axios({
            method: 'post',
            url: '/api/invs/randomdistribution',
            headers:{
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        })
        .then((response) => {
            this.data = response.data
        })
        .catch((error) => {
            console.log(error.response.data)
        })
    }
    
}
</script>