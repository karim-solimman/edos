<template>
    <v-container>
        <Loading :loading="loading" />
        <v-row v-if="!loading">
            <v-col>
                <h1 class="text-h4 font-weight-light">Dashboard</h1>
            </v-col>
        </v-row>
        <v-row v-if="!loading && roles">
            <v-col lg=2 md=2 cols=6>
                <v-hover v-slot="{hover}">
                    <v-card color="info" dark :ripple="ripple" :hover="12" style="text-decoration: none" :to="{name: 'users'}">
                        <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon large>mdi-account-tie</v-icon></span>{{roles[0].users_count}}</v-card-title>
                        <v-expand-transition>
                            <div
                                v-if="hover"
                                class="d-flex transition-fast-in-fast-out light-blue v-card--reveal text-h5 font-weight-light white--text"
                                style="height: 100%;"
                            >
                            <v-icon large left>mdi-account-tie</v-icon>Admins
                            </div>
                            </v-expand-transition>
                    </v-card>   
                </v-hover>
            </v-col>
            <v-col lg=2 md=2 cols=6>
                <v-hover v-slot="{hover}">
                    <v-card color="blue darken-4" dark :ripple="ripple" hover style="text-decoration: none" :to="{name: 'users'}">
                        <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon large>mdi-account-group</v-icon></span>{{roles[1].users_count}}</v-card-title>
                        <v-expand-transition>
                            <div
                                v-if="hover"
                                class="d-flex transition-fast-in-fast-out blue darken-4 v-card--reveal text-h5 font-weight-light white--text"
                                style="height: 100%;"
                            >
                            <v-icon large left>mdi-account-group</v-icon> Users
                            </div>
                            </v-expand-transition>
                    </v-card>
                </v-hover>
            </v-col>
            <v-col lg=2 md=2 cols=6>
                <v-card dark color="pink lighten-2" :ripple="ripple" hover style="text-decoration: none" :to="{name: 'admin-invs'}">
                    <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon>mdi-table-eye</v-icon></span>{{invs}}</v-card-title>
                </v-card>
            </v-col>
            <v-col lg=2 md=2 cols=6>
                <v-card color="blue lighten-3" :ripple="ripple" hover style="text-decoration: none" :to="{name: 'departments'}">
                    <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon>mdi-folder</v-icon></span>{{departments}}</v-card-title>
                </v-card>
            </v-col>
            <v-col lg=2 md=2 cols=6>
                <v-card color="cyan lighten-2" :ripple="ripple" hover style="text-decoration: none" :to="{name: 'rooms'}">
                    <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon>mdi-door</v-icon></span>{{rooms}}</v-card-title>
                </v-card>
            </v-col>
            <v-col lg=2 md=2 cols=6>
                <v-card color="blue-grey lighten-2" dark :ripple="ripple" hover style="text-decoration: none" :to="{name: 'courses'}">
                    <v-card-title class="font-weight-light text-h1"><span class="text-overline"><v-icon>mdi-book-open-page-variant-outline</v-icon></span>{{courses}}</v-card-title>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="!loading && department_courses">
            <v-col cols="12" lg="6" md="6">
                <v-card
                color="teal darken-3"
                dark
                >
                <v-card-text>
                <v-sheet color="rgba(0, 0, 0, .12)">
                    <v-sparkline
                    :value="values"
                    :labels="labels"
                    color="rgba(255, 255, 255, .7)"
                    height="100"
                    padding="24"
                    stroke-linecap="round"
                    smooth
                    auto-draw
                    >
                   
                    </v-sparkline>
                </v-sheet>
                </v-card-text>

                <v-card-text>
                <div class="text-h4 text-center font-weight-thin">
                    Departments Courses
                </div>
                </v-card-text>
            </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<style>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  position: absolute;
  width: 100%;
}
</style>


<script>
import Loading from '../../components/Loading.vue'
export default {
    components:{
        Loading
    },
    data: () => ({
        loading: true,
        hover: true,
        ripple: true,
        invs: null,
        departments: null,
        rooms: null,
        courses: null,
        roles: [],
        department_courses: [],
        values: [],
        labels: []
    }),
    mounted(){
        axios({
            method: 'get',
            url: '/api/dashboard',
            headers: {
                Authorization: `Bearer ${window.localStorage.getItem('token')}`
            }
        }).then((response) =>{
            this.roles = response.data.roles
            this.invs = response.data.invs
            this.departments = response.data.departments
            this.rooms = response.data.rooms
            this.courses = response.data.courses
            this.department_courses = response.data.department_courses
            this.draw()
            this.loading = false
        }).catch((error)=>{
            this.loading = false
            console.log(error.response.data.message)
            this.$emit('logged-out', false)
            window.localStorage.clear()
            this.$router.push('/')
        })
    },
    methods:{
        draw(){
            $.each(this.department_courses,(index, value) => {
                this.labels.push(value['name'].split(" ").map((n)=>n[0]).join(""))
                this.values.push(value['courses_count'])
            })
        }
    }
}
</script>