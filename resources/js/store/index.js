export default {
    state: {
        user: JSON.parse(localStorage.getItem('user')),
        invs: Array,
        roles: Array,
        settings: Array
    },
    mutations: {
        updateUser(state, newUser) {
            state.user = newUser
        },
        updateInvs(state, updatedInvs) {
            state.invs = updatedInvs
        },
        updateRoles(state, updatedRoles) {
            state.roles = updatedRoles
        },
        updateSettings(state, updatedSettings) {
            state.settings = updatedSettings
        }
    },
    actions: {
        updateUser({ commit }, newUser) {
            commit('updateUser', newUser)
        },
        updateInvs({ commit }, updateInvs) {
            commit('updateInvs', updateInvs)
        },
        updateRoles({ commit }, updateRoles) {
            commit('updateRoles', updateRoles)
        },
        updateSettings({ commit }, updateSettings) {
            commit('updateSettings', updateSettings)
        }
    },
    getters: {
        getUser(state){
                return state.user
        },
        getInvs(state) {
            return state.invs
        },
        getRoles(state) {
            return state.roles
        },
        getSettings(state) {
            return state.settings
        }
    },
    modules: {

    }
}
