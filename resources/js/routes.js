import VueRouter from "vue-router";

const routes = [
    //public routes
    {
        path: '/',
        component: require('./Views/Login').default,
        name: 'home'
    },
    {
        path: '/about',
        component: require('./Views/About').default,
        name: 'about'
    },
    {
        path: '/register',
        component: require('./Views/Home').default,
        name: 'register'
    },
    //user routes
    {
        path: '/profile',
        component: require('./Views/Profile').default,
        name: 'profile'
    },
    {
        path: '/invs',
        component: require('./Views/Invs').default,
        name: 'invs'
    },
   
    //admin routes
    {
        path: '/admin/invs',
        component: require('./Views/Admin/Invs').default,
        name: 'admin-invs'
    },
    {
        path: '/admin/addinv',
        component: require('./Views/Admin/AddInv').default,
        name: 'add-inv'
    },
    {
        path: '/admin/invs/:id',
        component: require('./Views/Admin/InvProfile').default,
        name: 'invProfile'
    },
    {
        path: '/admin/invs/:id/edit',
        component: require('./Views/Admin/EditInv.vue').default,
        name: 'edit-inv'
    },
    {
        path: '/admin/users',
        component: require('./Views/Admin/Users').default,
        name: 'users'
    },
    {
        path: '/admin/users/import',
        component: require('./Views/Admin/UsersImport').default,
        name: 'usersImport'
    },
    {
        path: '/admin/users/:id',
        component: require('./Views/Admin/UserProfile').default,
        name: 'userProfile'
    },
    {
        path: '/admin/users/edit/:id',
        component: require('./Views/Admin/EditUser').default,
        name: 'editUser'
    },
    {
        path: '/admin/roles',
        component: require('./Views/Admin/Roles').default,
        name: 'roles'
    },
    {
        path: '/admin/dashboard',
        component: require('./Views/Admin/Dashboard').default,
        name: 'dashboard'
    },
    {
        path: '/admin/adduser',
        component: require('./Views/Admin/AddUser').default,
        name: 'addUser'
    },
    {
        path: '/admin/departments',
        component: require('./Views/Admin/Departments').default,
        name: 'departments'
    },
    {
        path: '/admin/departments/:id',
        component: require('./Views/Admin/DepartmentProfile').default,
        name: 'departmentProfile'
    },
    {
        path: '/admin/departments/:id/edit',
        component: require('./Views/Admin/EditDepartment').default,
        name: 'editDepartment'
    },
    {
        path: '/admin/adddep',
        component: require('./Views/Admin/AddDepartment').default,
        name: 'addDepartment'
    },
    {
        path: '/admin/rooms',
        component: require('./Views/Admin/Rooms').default,
        name: 'rooms'
    },
    {
        path: '/admin/rooms/:id',
        component: require('./Views/Admin/RoomProfile').default,
        name: 'roomProfile'
    },
    {
        path: '/admin/rooms/:id/edit',
        component: require('./Views/Admin/EditRoom').default,
        name: 'editRoom'
    },
    {
        path: '/admin/addroom',
        component: require('./Views/Admin/AddRoom').default,
        name: 'addRoom'
    },
    {
        path: '/admin/courses',
        component: require('./Views/Admin/Courses').default,
        name: 'courses'
    },
    {
        path: '/admin/addcourse',
        component: require('./Views/Admin/AddCourse').default,
        name: 'addCourse'
    },
    {
        path: '/admin/course/:id/edit',
        component: require('./Views/Admin/EditCourse').default,
        name: 'editCourse'
    },
    {
        path: '/admin/courses/:id',
        component: require('./Views/Admin/CourseProfile').default,
        name: 'courseProfile'
    },
    {
        path: '/admin/settings',
        component: require('./Views/Admin/Settings').default,
        name: 'settings'
    },
    {
        path: '/admin/random',
        component: require('./Views/Admin/RandomDistribution').default,
        name: 'random-distribution'
    }

]

export default new VueRouter({
    mode: 'history',
    routes,
    scrollBehavior(){
        return {x:0, y:0}
    }
})
