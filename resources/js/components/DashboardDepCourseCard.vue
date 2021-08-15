<template>
    <v-card
        color="teal darken-3"
        dark
        >
        <v-card-text>
        <v-sheet color="rgba(0, 0, 0, .12)">
            <v-sparkline
            :value="value"
            :labels="labels"
            color="rgba(255, 255, 255, .7)"
            height="100"
            line-width="2"
            padding="24"
            stroke-linecap="round"
            auto-draw
            >
            </v-sparkline>
        </v-sheet>
        </v-card-text>

        <v-card-text>
        <div class="text-h5 text-center font-weight-thin">
            Departments Courses
        </div>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props:{
        department_courses: []
    },
    data(){
        return{
            labels: [],
            value: []
        }
    },
    beforeMount(){
        this.draw()
    },
     methods:{
        draw(){
            $.each(this.department_courses,(index, value) => {
                this.labels.push(value['name'].split(" ").map((n)=>n[0]).join(""))
                this.value.push(value['courses_count'])
            })
        }
    }
}
</script>