<template>
<div id="plan-list">

    <li><router-link :to="{name: 'create-plan'}">New plan</router-link></li>

    <ul>
        <li v-for="plan in this.$store.state.plans" v-bind:key="plan.id">
            <router-link :to="{name: 'plan', params: {planId: plan.id}}">
                <p>{{plan.name}}</p>
                <p>last updated ：{{plan.updated_at}}</p>
            </router-link>
        </li>
    </ul>
</div>
</template>

<script>
    export default {
    }
</script>
