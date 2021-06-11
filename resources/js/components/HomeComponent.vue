<template>
<div id="usage">
    <p>使い方</p>
    <p>① Plansからメッセージスケジュールを作成</p>
    <p>② Calendarからお好みの日付にスケジューを割り当て</p>
    <p>③ Lineから{{bot_id}}を友達登録するとメッセージがくるよ！</p>
</div>
</template>

<script>
    export default {
        data: ()=>({
            bot_id: '',
        }),
        mounted(){
            this.bot_id = document.getElementsByName('bot_id')[0].content;
        },
    }
</script>

<style lang="scss">
#usage{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;

    padding: 10px;

    p{
        margin-top: 40px;
    }
}
</style>
