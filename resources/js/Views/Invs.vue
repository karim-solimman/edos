<template>
        <v-container>
        <Alert @alert-closed="alert = false" :alert="alert" :alertMessage="alertMessage" :alertType="alertType" />
        <Loading :loading="loading" />
        <div v-if="!loading">
            <v-row  v-for="(inv, date) in invs" :key="inv.id">
                <v-col class="text-center my-auto" cols="12" lg="3" md="3">
                   <h1 class="text-h3 font-weight-light">{{date | getDay}} {{date | getMonth}}</h1>
                   <h5 class="text-h5 font-weight-light">{{date | getDayName}}</h5>
                   <p class="text-overline">{{date | getYear}}</p>
                </v-col>
                <v-col cols="12" lg="3" md="3" v-for="(item, time) in inv" :key="item.id">
                    <v-hover v-slot="{hover}">
                        <v-card hover :color="isExists(time)? 'green lighten-5' : 'grey lighten-5'"  @click="cardAction(item, time)">
                            <v-card-title class="d-flex justify-space-between">
                                <h1 class="text-h4 font-weight-light">{{time | TimeFormat}}</h1>
                                <v-icon v-if="isExists(time)" color="green">mdi-account</v-icon>
                            </v-card-title>
                            <v-card-text>
                                <v-chip small dark :color="item.users_count < item.users_limit? 'green darken-2' : 'red darken-2'"><v-icon left>mdi-account-group</v-icon>{{item.users_count}} / {{item.users_limit}}</v-chip>
                                <v-chip small outlined><v-icon left>mdi-clock-outline</v-icon>{{item.updated_at | ago}}</v-chip>
                            </v-card-text>
                            <v-expand-transition>
                                <div
                                    v-if="hover"
                                    class="d-flex transition-fast-in-fast-out blue-grey darken-4 v-card--reveal text-h5 font-weight-light white--text"
                                    style="height: 100%;"
                                >
                                <span class="text-h4 font-weight-thin text-center" v-if="isExists(time)"><v-icon dark x-large left color="error">mdi-table-large-remove</v-icon><br/>REMOVE</span>
                                <span class="text-h4 font-weight-thin text-center" v-else><v-icon dark x-large left color="success">mdi-table-large-plus</v-icon><br/>ADD</span>
                                </div>
                            </v-expand-transition>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>
        </div>
    </v-container>
</template>

<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  position: absolute;
  opacity: 0.9;
  width: 100%;
}
</style>

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
          cardAction(item, date_time){
              if(this.isExists(date_time)){
                  this.removeInv(date_time)
              }
              else{
                  if(item.users_count < item.users_limit) {
                      this.addInv(date_time)
                  }
                  else {
                      this.alert = true
                      this.alertType = 'error'
                      this.alertMessage = this.$options.filters.DateFormat(date_time) + " " + this.$options.filters.TimeFormat(date_time) + ", is FULL can't be added." 
                  }
              }
          },
            updateInvs(){
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
          addInv(date_time){
              let formData = new FormData()
              formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
              formData.append('date_time', date_time)
              axios.get('/sanctum/csrf-cookie')
              .then(response => {
                  axios({
                      method: 'post',
                      url: '/api/invs/adduser',
                      data: formData,
                      headers: {
                          Authorization: `Bearer ${window.localStorage.getItem('token')}`
                      }
                  })
                  .then((response) => {
                      this.alert = true
                      this.alertType = "success"
                      this.alertMessage = response.data.message
                      this.$store.dispatch('updateInvs', response.data.invs)
                      this.user_invs = this.$store.getters.getInvs
                      this.updateInvs()
                  })
                  .catch((error) => {
                      this.alertType = "error"
                      this.alert = true
                      this.alertMessage = error.response.data.message
                  })
              })
          },
            removeInv(date_time) {
                this.btnLoading = true
                let formData = new FormData()
                formData.append('user_id', JSON.parse(localStorage.getItem('user')).id)
                formData.append('date_time', date_time)
                axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios({
                        method: 'post',
                        url: '/api/invs/removeuserbydate',
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
