<template>
<div class="plan-show">
    <h2>{{$route.params.date}}</h2>
    <p>この日にはどのプランを適用する？</p>
    <p @click="remove">でれーとしゅる！！！！</p>
    <ul>
        <li v-for="plan in $store.state.plans" v-bind:key="plan.id">
            <p @click="assign" v-bind:plan_id="plan.id">id : {{plan.id}} name : {{plan.name}}</p>
        </li>
    </ul>
</div>
</template>

<style scoped lang="scss">
</style>

<script>
    export default {
        data: ()=>({
            plans: null,
        }),
        methods: {
            assign: function(event){
                const url = 'api/calendar/'+this.$route.params.date+'/'+event.target.getAttribute('plan_id')
                axios.post(url).then(res=>{
                    this.$router.push('/calendar')
                })
            },
            remove: function(){
                const url = 'api/calendar/'+this.$route.params.date
                axios.delete(url).then(res=>{
                    this.$router.push('/calendar')
                })
            }
        }
    }
</script>
