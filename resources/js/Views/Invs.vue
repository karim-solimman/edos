<template>
        <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading" />
        <v-row v-if="!loading">
            <p v-for="(invs, date) in invs" :key="date.id">{{date}} - {{invs}}</p>
            <!-- <v-col lg=4 md=4 cols=12 v-for="inv in invs" :key="inv.id">
                <v-card @click="cardAction(inv.id)" rounded="xl" :color="isExists(inv.id)? 'pink lighten-5' : 'teal lighten-5'" hover>
                    <v-card-title>
                        <h1 class="text-h4 font-weight-light">{{inv.date_time | DateFormat}}</h1>
                        </v-card-title>
                    <v-card-subtitle>Time: {{inv.date_time | TimeFormat}}</v-card-subtitle>
                    <v-card-text>
                        <v-chip small color="success">
                            <v-icon small left>mdi-account-group</v-icon>{{inv.users_count}} / {{inv.room.users_limit}}
                        </v-chip>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn rounded @click="addInv(inv.id)" block color="info" text v-if="!isExists(inv.id)"><v-icon left>mdi-plus</v-icon>Add</v-btn>
                        <v-btn rounded @click="removeInv(inv.id)" block color="red" text v-if="isExists(inv.id)"><v-icon left>mdi-close</v-icon>Remove</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col> -->
        </v-row>
    </v-container>
</template>

<script>
import Loading from '../components/Loading.vue'
import Alert from '../components/Alert.vue'
    export default {
        name: 'invs',
        components: {
            Loading, Alert
        },
        data()
        {
            return {
                invs: [],
                user_invs: [],
                alert: false,
                alertType: null,
                alertMessage: null,
                loading: true,
            }
        },
        methods:{
          isExists(invId){
              let status = false
              this.user_invs.forEach((item) => {
                  if (item.id === invId)
                      status = true
              })
              return status
          },
          cardAction(invId){
              if(this.isExists(invId)){
                  this.removeInv(invId);
              }
              else{
                  this.addInv(invId);
              }
          },
            updateInvs(){
                this.loading = true
                axios({
                    method: 'get',
                    url: '/api/invforusers',
                    headers: {
                        Authorization: `Bearer ${window.localStorage.getItem('token')}`
                    }
                })
                    .then((response) => {
                        this.invs = response.data.invs
                        this.loading = false
                    })
                    .catch((error) => {
                        this.alert = true
                        this.alertType = 'error'
                        this.alertMessage = error.response.data.message
                        this.loading = false
                    })
            },
          addInv(invId){
              let formData = new FormData()
              formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
              formData.append('inv_id', invId)
              axios.get('/sanctum/csrf-cookie')
              .then(response => {
                  axios({
                      method: 'post',
                      url: '/api/users/addinv',
                      data: formData,
                      headers: {
                          Authorization: `Bearer ${window.localStorage.getItem('token')}`
                      }
                  })
                  .then((response) => {
                      this.alertType = "success"
                      this.alertMessage = response.data.message
                      this.$store.dispatch('updateInvs', response.data.invs)
                      this.user_invs = this.$store.getters.getInvs
                      this.alert = true
                      this.updateInvs()
                  })
                  .catch((error) => {
                      this.alertType = "error"
                      this.alert = true
                      this.alertMessage = error.response.data.message
                  })
              })
          },
            removeInv(invId) {
                this.btnLoading = true
                let formData = new FormData()
                formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
                formData.append('inv_id', invId)
                axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios({
                        method: 'post',
                        url: '/api/users/removeinv',
                        data: formData,
                        headers: {
                            Authorization: `Bearer ${window.localStorage.getItem('token')}`
                        }
                    })
                    .then((response) => {
                        this.alertType = "success"
                        this.alertMessage = response.data.message
                        this.$store.dispatch('updateInvs', response.data.invs)
                        this.user_invs = this.$store.getters.getInvs
                        this.alert = true
                        this.updateInvs()
                        this.btnLoading = false
                    })
                    .catch((error) => {
                        this.alertType = "error"
                        this.alert = true
                        this.alertMessage = error.response.data.message
                    })
                })

            }
        },
        mounted() {
            this.user_invs = this.$store.getters.getInvs
            axios({
                method: 'get',
                url: '/api/invforusers',
                headers:{
                    Authorization: `Bearer ${window.localStorage.getItem('token')}`
                }
            })
            .then((response) => {
                this.invs = response.data.invs
                this.loading = false
            })
            .catch((error) => {
                this.loading = false
                window.localStorage.clear()
                this.$emit('logged-out', false)
                this.$router.push('/').catch(()=>{});
            })
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
        }
    }
</script>
