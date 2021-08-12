<template>
    <v-navigation-drawer disable-resize-watcher v-model="drawer" app>
            <v-list dense>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title class="text-h6">
                            <v-icon small left>mdi-vector-polyline-edit</v-icon> EDOS
                        </v-list-item-title>
                        <v-list-item-subtitle>Exams Distribution Observers System</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>

                  <v-list-item color="primary" style="text-decoration: none" link v-if="isAdmin" :to="{name: 'dashboard'}">
                    <v-list-item-icon><v-icon>mdi-view-dashboard</v-icon></v-list-item-icon>
                    <v-list-item-content><v-list-item-title>Dashboard</v-list-item-title></v-list-item-content>
                </v-list-item>
                
                <v-list-group v-if="isAdmin" no-action :value="false" prepend-icon="mdi-account-circle">
                    <template v-slot:activator>
                        <v-list-item-title>Users</v-list-item-title>
                    </template>
                     <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'users'}">
                        <v-list-item-icon><v-icon>mdi-account-group</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>All Users</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'addUser'}">
                        <v-list-item-icon><v-icon>mdi-account-plus</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Add new user</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin">
                        <v-list-item-icon><v-icon>mdi-upload</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Upload users</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>

                <v-list-group v-if="isAdmin" no-action :value="false" prepend-icon="mdi-table-eye">
                    <template v-slot:activator>
                        <v-list-item-title>Invs</v-list-item-title>
                    </template>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'admin-invs'}">
                        <v-list-item-icon><v-icon>mdi-table-check</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>All Invs</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'add-inv'}">
                        <v-list-item-icon><v-icon>mdi-table-plus</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Add new inv</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin">
                        <v-list-item-icon><v-icon>mdi-upload</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Upload invs</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>

                <v-list-group v-if="isAdmin" no-action :value="false" prepend-icon="mdi-folder">
                    <template v-slot:activator>
                        <v-list-item-title>Departments</v-list-item-title>
                    </template>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'departments'}">
                        <v-list-item-icon><v-icon>mdi-folder-multiple-outline</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>All departments</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'addDepartment'}">
                        <v-list-item-icon><v-icon>mdi-folder-plus-outline</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Add new dep</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>

                 <v-list-group v-if="isAdmin" no-action :value="false" prepend-icon="mdi-door">
                    <template v-slot:activator>
                        <v-list-item-title>Rooms</v-list-item-title>
                    </template>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'rooms'}">
                        <v-list-item-icon><v-icon>mdi-door-closed</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>All rooms</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'addRoom'}">
                        <v-list-item-icon><v-icon>mdi-door-open</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Add new room</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>


                <v-list-group v-if="isAdmin" no-action :value="false" prepend-icon="mdi-book-open-page-variant-outline">
                    <template v-slot:activator>
                        <v-list-item-title>Courses</v-list-item-title>
                    </template>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'courses'}">
                        <v-list-item-icon><v-icon>mdi-book-multiple-outline</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>All Courses</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link color="primary" style="text-decoration:none" v-if="isAdmin" :to="{name: 'addCourse'}">
                        <v-list-item-icon><v-icon>mdi-book-plus-outline</v-icon></v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Add new course</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>


                <v-list-item color="primary" style="text-decoration: none" link v-if="isAdmin" :to="{name: 'roles'}">
                    <v-list-item-icon><v-icon>mdi-lock-check-outline</v-icon></v-list-item-icon>
                    <v-list-item-content><v-list-item-title>Roles and Permissions</v-list-item-title></v-list-item-content>
                </v-list-item>

                <v-list-item color="primary" style="text-decoration: none" link v-if="isAdmin" :to="{name: 'settings'}">
                    <v-list-item-icon><v-icon>mdi-cog</v-icon></v-list-item-icon>
                    <v-list-item-content><v-list-item-title>System Settings</v-list-item-title></v-list-item-content>
                </v-list-item>



                <v-list-item color="primary" style="text-decoration:none" link v-if="isUser" :to="{name: 'profile'}">
                    <v-list-item-icon><v-icon>mdi-account</v-icon></v-list-item-icon>
                    <v-list-item-content><v-list-item-title>Profile</v-list-item-title></v-list-item-content>
                </v-list-item>
                <v-list-item color="primary" style="text-decoration:none" v-if="isUser" link :to="{name: 'invs'}">
                    <v-list-item-icon><v-icon>mdi-table-eye</v-icon></v-list-item-icon>
                    <v-list-item-content><v-list-item-title>Invs</v-list-item-title></v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
</template>

<script>
export default {
   name: 'navigation-drawer',
   data(){
       return{
           drawer: this.drawerToggle
       }
   },
   props: {
       drawerToggle: Boolean,
       isAdmin: Boolean,
       isUser: Boolean
   },
   watch:{
       drawerToggle: function(newVal, oldVal) {this.drawer = newVal},
       drawer: function(newVal, oldVal) {
           if(oldVal)
                this.$emit('drawer-close')
        }
   }
}
</script>