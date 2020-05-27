<template>
    <transition>
        <div id="loading-view" v-show="now_loading">
            <div class="loader">Loading...</div>
        </div>
    </transition>
</template>



<script>
    export default {
        computed: {
            now_loading(){
                return this.$store.state.now_loading
            },
        },
    }
</script>
