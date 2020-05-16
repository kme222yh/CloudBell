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
        {path: '/', component: {template: ''}},

        {path: '/plan', name: 'plans', component: require('./components/PlanListComponent.vue').default},
        {path: '/plan/create', component: require('./components/CreatePlanComponent.vue').default},
        {path: '/plan/show/:planId', name: 'plan', component: require('./components/PlanComponent.vue').default},

        {path: '/calendar', component: require('./components/CalendarComponent.vue').default},
        {path: '/calendar/:date', name: 'day', component: require('./components/CalendarDayComponent.vue').default},
     ]
 });


const store = new Vuex.Store({
     state: {
         plans: null,
     },
     mutations: {
         reset_plans(state){
            state.plans = null
         },
         update_plans(state, payload){
            state.plans = payload.plans
         },
     },
     getters: {
     },
     actions: {
         get_plans(ctx){
             ctx.commit('reset_plans')
             axios.get('api/plan/').then(res=>{
                 ctx.commit('update_plans', {plans: res.data})
             })
         }
     }
 })


const app = new Vue({
    el: '#app',
    router: router,
    store,
    mounted(){
        this.$store.dispatch('get_plans')
    }
});
