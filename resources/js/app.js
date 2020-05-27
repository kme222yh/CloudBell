/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Vuex = require('vuex');
window.VueRouter = require('vue-router').default;

Vue.use(Vuex);
Vue.use(VueRouter);

require('./fontawesome')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
     routes: [
        {path: '/home', name: 'home', component: require('./components/HomeComponent.vue').default},

        {path: '/plan', name: 'plans', component: require('./components/PlanListComponent.vue').default},
        {path: '/plan/create', name: 'create-plan', component: require('./components/PlanDiscription/CreatePlanComponent.vue').default},
        {path: '/plan/show/:planId', name: 'plan', component: require('./components/PlanDiscription/PlanComponent.vue').default},

        {path: '/calendar', name: 'calendar', component: require('./components/CalendarComponent.vue').default, children: [
            {
                path: ':date', name: 'day', component: require('./components/CalendarDayComponent.vue').default,
            },
        ]},

        {path: '/user', name: 'user', component: require('./components/UserComponent.vue').default},
     ]
 });


const store = new Vuex.Store({
     state: {
         plans: null,
         config: null,
         now_loading: true
     },
     mutations: {
         reset_plans(state){
            state.plans = null
         },
         update_plans(state, payload){
            state.plans = payload.plans
         },
         set_config(state, payload){
             state.config = payload.config
         },

         loading_finish(state){
             state.now_loading = false
         },
         loading_start(state){
             state.now_loading = true
         }
     },
     getters: {
     },
     actions: {
         loading_start(ctx){
             ctx.commit('loading_start')
         },
         get_plans(ctx){
             ctx.commit('reset_plans')
             axios.get('api/plan/').then(res=>{
                 ctx.commit('update_plans', {plans: res.data})
                 ctx.commit('loading_finish')
             }).catch(error=>{
                 if(error.response.data.code == 202){
                     ctx.commit('loading_finish')
                 }
             })
         },
         get_config(ctx){
             axios.get('api/config/').then(res=>{
                 ctx.commit('set_config', {config: res.data})
             })
         },
     }
 })


const app = new Vue({
    el: '#app',
    router: router,
    store,
    created(){
        if(this.$route.name != 'home')
            this.$router.push({name: 'home'})
        this.$store.dispatch('get_config')
        this.$store.dispatch('get_plans')
        // setTimeout(this.$store.dispatch, 3000, 'get_config')
    }
});
