<template>
<div class="day-discription">
    <div class="body">
        <h2>{{$route.params.date}}</h2>
        <p v-if="!plan_name">select a plan</p>
        <p v-if="plan_name">{{plan_name}}</p>
        <p class="no-planer" v-if="plan_name"　@click="remove">no plan</p>
        <ul>
            <li v-for="plan in $store.state.plans" v-bind:key="plan.id">
                <p @click="assign" v-bind:plan_id="plan.id">{{plan.name}}</p>
            </li>
        </ul>
        <p class="closer" @click="$router.push('/calendar')"><font-awesome-icon icon="times-circle" size="2x" /></p>
    </div>
    <div class="back-button" @click="$router.push('/calendar')"></div>
</div>
</template>


<script>
    export default {
        computed: {
            plan_name(){
                return this.$parent.calendar[this.$route.params.date].name
            },
        },
        methods: {
            assign: function(event){
                if(this.plan_name){
                    this.remove()
                }
                const url = 'api/calendar/'+this.$route.params.date+'/'+event.target.getAttribute('plan_id')
                axios.post(url).then(res=>{
                    this.$router.push('/calendar')
                    this.$emit('update')
                })
            },
            remove: function(){
                const url = 'api/calendar/'+this.$route.params.date
                axios.delete(url).then(res=>{
                    this.$router.push('/calendar')
                    this.$emit('update')
                })
            },
        }
    }
</script>
