<template>
<div id="plan-discription">

    <div class="manager">
        <button type="button" @click="create">create plan</button>
    </div>

    <h2><input type="text" v-model="name" value="" placeholder="plan name"></h2>

    <ul>
        <body-editer :time.sync="new_event[0]" :text.sync="new_event[1]" @edit:me="addEvent"><font-awesome-icon icon="plus" size="2x"/></body-editer>
    </ul>

    <ul>
        <body-editer v-for="(event, index) in body" v-bind:key="index" :time.sync="event[0]" :text.sync="event[1]" :index="index" @edit:me="remove" @refresh:us="refresh"/>
    </ul>
</div>
</template>

<script>
Vue.component('body-editer', require('./BodyEditer.vue').default);

    export default {
        data: ()=>({
            id: '',
            name: '',
            body: [['12:00', 'new event'], ['13:00', 'new event']],
            new_event: ['12:00', 'new event'],
        }),
        methods: {
            create: function(){
                const url = 'api/plan'
                const param = {
                    name: this.name,
                    body: this.body
                }
                axios.post(url, param).then(res=>{
                    this.$store.dispatch('loading_start')
                    this.$store.dispatch('get_plans')
                    this.$router.push({name: 'plans'})
                })
            },
            destroy: function(){
                const url = 'api/plan/' + this.$route.params.planId
                axios.delete(url).then(res=>{
                    this.$store.dispatch('get_plans')
                })
                this.$router.push('/plan')
            },


            refresh: function(){
                this.body.sort((a, b) => {
                    if (a[0] < b[0]) return -1
                    if (a[0] > b[0]) return 1
                })
            },

            remove: function(index){
                this.body.splice(index, 1)
            },

            addEvent: function(){
                this.body.push([... this.new_event])
                this.refresh()
            },
        }
    }
</script>
