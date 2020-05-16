<template>
<div class="plan-show">
    <h2><input type="text" v-model="name" value=""></h2>
    <button type="button" @click="modeChange">mode : {{mode}}</button>
    <button type="button" @click="newEvent">new event</button>
    <button v-show="mode=='change'" type="button" @click="create">追加しちゃうよおおおお</button>
    <ul>
        <li v-for="(event, index) in body" v-bind:key="index" v-bind:index="index" @click="remove">
            <input type="time" step="600" @blur="refresh" v-model="event[0]"> : <input type="text" v-model="event[1]">
        </li>
    </ul>
</div>
</template>

<script>
    export default {
        data: ()=>({
            name: 'hogehoge',
            body: [['10:00', 'event1'], ['12:00', 'event2']],
            mode: 'change',
        }),
        beforeDestroy(){
            this.$store.dispatch('get_plans')
        },
        methods: {
            create: function(){
                const url = 'api/plan'
                const param = {
                    name: this.name,
                    body: this.body
                }
                axios.post(url, param).then(res=>{
                    this.$router.push({name: 'plans'})
                }).catch(error=>{
                    console.log(error)
                })
            },

            refresh: function(){
                this.body.sort((a, b) => {
                    if (a[0] < b[0]) return -1
                    if (a[0] > b[0]) return 1
                })
            },

            remove: function(event){
                if(this.mode == 'delete'){
                    this.body.splice(event.target.getAttribute('index'), 1)
                }
            },

            modeChange: function(){
                if(this.mode == 'change'){
                    this.mode = 'delete'
                }
                else{
                    this.mode = 'change'
                }
            },

            newEvent: function(){
                this.body.push(['12:00', 'new event'])
            },
        }
    }
</script>
