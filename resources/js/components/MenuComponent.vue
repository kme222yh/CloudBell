<template>
    <nav :class="{menu: true, opened: is_menu_opened}">
        <p class="greeting">Hi! {{user_name}}</p>
        <ul>
            <li><router-link to="/home">Home</router-link></li>
            <li><router-link to="/plan">Plans</router-link></li>
            <li><router-link to="/calendar">Calender</router-link></li>
            <li><router-link to="/user">User</router-link></li>
        </ul>

        <p class="closer" @click="menuClose"><font-awesome-icon icon="times" size="2x" /></p>
        <p class="opener" @click="menuOpen"><font-awesome-icon icon="bars" size="2x" /></p>

        <img id="visual-logo" src="image/CloudBell-logo.png" alt="">

        <div class="account_menu">
            <a href="logout">logout</a>
            <!-- <a href="withdraw">withdrow</a> -->
        </div>
    </nav>
</template>


<style lang="scss">
#visual-logo{
    margin: 0 auto;
    display: block;
    margin-top: 15px;
    width: 80%;
}
</style>


<script>
    export default {
        data: ()=>({
            is_menu_opened: false,
            user_name: 'no name',
        }),
        created(){
            this.$router.beforeEach(this.routerMenuClose)
        },
        mounted(){
            let clone = document.getElementsByClassName('greeting')[0]
            this.user_name = clone.innerText
        },
        methods: {
            menuOpen: function(){
                this.is_menu_opened = true
            },
            menuClose: function(){
                this.is_menu_opened = false
            },


            routerMenuClose: function(to, from, next){
                this.menuClose()
                next()
            },
        }
    }
</script>
