<template>
<div id="plan-list">

    <li><router-link to="plan/create">新規プラン</router-link></li>

    <ul>
        <li v-for="plan in this.$store.state.plans" v-bind:key="plan.id">
            <router-link :to="{name: 'plan', params: {planId: plan.id}}">{{plan.name}}　更新：{{plan.updated_at}}</router-link>
        </li>
    </ul>
</div>
</template>

<script>
    export default {
    }
</script>
