<template>
<div class="plan-show">
    <h2><input type="text" v-model="name" value=""></h2>
    <button type="button" @click="modeChange">mode : {{mode}}</button>
    <button type="button" @click="newEvent">new event</button>
    <button v-show="mode=='change'" type="button" @click="update">アップデーーーーーと</button>
    <button v-show="mode=='change'" type="button" @click="destroy">削除しちゃうのおおおお！！</button>
    <ul>
        <li v-for="(event, index) in body" v-bind:key="index" v-bind:index="index" @click="remove">
            <input type="time" step="600" @blur="refresh" v-model="event[0]" v-bind:index="index"> : <input type="text" @blur="refresh" v-model="event[1]" v-bind:index="index">
        </li>
    </ul>
</div>
</template>

<script>
    export default {
        data: ()=>({
            id: '',
            name: '',
            body: {},
            mode: 'change',
        }),
        mounted(){
            const url = 'api/plan/' + this.$route.params.planId
            axios.get(url).then(response=>{
                this.id = response.data.id
                this.name = response.data.name
                this.body = response.data.body
            })
        },
        beforeDestroy(){
            this.$store.dispatch('get_plans')
        },
        methods: {
            update: function(){
                const url = 'api/plan/' + this.$route.params.planId
                const param = {
                    name: this.name,
                    body: this.body
                }
                axios.put(url, param).then(res=>{
                    console.log(res)
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

            destroy: function(){
                const url = 'api/plan/' + this.$route.params.planId
                axios.delete(url).catch(error=>{
                    console.log(error)
                })
                this.$router.push('/plan')
            }
        }
    }
</script>
