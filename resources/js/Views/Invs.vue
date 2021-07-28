<template>
        <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading" />
        <div v-if="!loading">
            <v-row  v-for="(inv, date) in invs" :key="inv.id">
                <v-col class="text-center my-auto" cols="12" lg="3" md="3">
                   <h1 class="text-h3 font-weight-light">{{date | getDay}} {{date | getMonth}}</h1>
                   <h5 class="text-h5 font-weight-light">{{date | getDayName}}</h5>
                </v-col>
                <v-col cols="12" lg="3" md="3" v-for="(item, time) in inv" :key="item.id" @click="cardAction(time)">
                    <v-card hover color="grey lighten-5">
                        <v-card-title>
                            <h1 class="text-h4 font-weight-light">{{time | TimeFormat}}</h1>
                        </v-card-title>
                        <v-card-text>
                            <v-chip small dark :color="item.users_count < item.users_limit? 'green' : 'red'"><v-icon left>mdi-account-group</v-icon>{{item.users_count}} / {{item.users_limit}}</v-chip>
                            <v-chip small outlined><v-icon left>mdi-alarm</v-icon>{{item.updated_at | ago}}</v-chip>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </div>
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
                tmp_data: [],
                alert: false,
                alertType: null,
                alertMessage: null,
                loading: true,
            }
        },
        methods:{
          isExists(date_time){
              let status = false
              this.user_invs.forEach((item) => {
                  if (item.date_time === date_time)
                      status = true
              })
              return status
          },
          cardAction(date_time){
              if(this.isExists(date_time)){
                  console.log('remove inv');
                //   this.removeInv(invId);
              }
              else{
                  console.log('add inv', );
                //   this.addInv(invId);
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
                this.alert = true
                this.alertType = 'error'
                this.alertMessage = error.response.data.message
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
            },
            getDayName(value)
            {
                return moment(value).format("dddd")
            },
            getDay(value)
            {
                return moment(value).format("DD")
            },
            getMonth(value)
            {
                return moment(value).format("MMMM")
            },
            getYear(value)
            {
                return moment(value).format("YYYY")
            }
        }
    }
</script>
