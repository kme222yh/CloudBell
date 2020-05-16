<template>
<div>
    <ul id="calendar">
        <div id="calendar_info">
            <p>{{date.getFullYear()}} / {{date.getMonth()+1}}</p>
            <ul>
                <li><p>Sun</p></li>
                <li><p>Mon</p></li>
                <li><p>Tue</p></li>
                <li><p>Wed</p></li>
                <li><p>Thu</p></li>
                <li><p>Fri</p></li>
                <li><p>Sat</p></li>
            </ul>
        </div>

        <li v-for="n in calendar_prefix"></li>
        <li v-for="(day, date) of calendar" v-bind:id="date" v-bind:key="date">
            <router-link :to="{name: 'day', params: {date: date}}">
                <p>{{date2day(date)}}</p>
                <p>{{day.name}}</p>
            </router-link>
        </li>
        <li v-for="n in calendar_sufix"></li>
    </ul>
</div>
</template>


<script>
    let calendar = {}
    let date = new Date
    date.setDate(1)
    date.setMonth(date.getMonth()-1)
    let m = 0
    while(m < 6){
        let year = date.getFullYear()
        while(year == date.getFullYear()){
            let month = date.getMonth()+1
            while(Math.abs(date.getMonth() - month) == 1){
                let day = date.getDate();
                calendar[''+year+'-'+month+'-'+day] = {
                    name: null,
                    id: null
                }
                date.setDate(day+1)
            }
            if(++m >= 6)  break
        }
    }





    export default {
        data: ()=>({
            date: new Date,
            calendar: calendar,
            interval: null,
        }),
        mounted(){
            this.date.setDate(1)
            const height = document.getElementById(this.date.getFullYear()+'-'+(this.date.getMonth()+1)+'-'+this.date.getDate()).getBoundingClientRect().top
            document.getElementsByClassName('content')[0].scrollTop = document.getElementsByClassName('content')[0].scrollTop + height - 250
            this.updateEvents()
            this.interval = setInterval(this.monthly_check, 100)
        },
        beforeDestroy(){
            document.getElementsByClassName('content')[0].scrollTop = -document.getElementsByClassName('content')[0].scrollTop
            clearInterval(this.interval)
        },
        computed: {
            calendar_prefix(){
                return (new Date(Object.keys(this.calendar)[0])).getDay()
            },
            calendar_sufix(){
                return 6-(new Date(Object.keys(this.calendar)[Object.keys(this.calendar).length - 1])).getDay()
            },
        },
        methods: {
            date2day(date){
                return (new Date(date)).getDate()
            },
            events2calendar(events){
                if(this.$store.state.plans === null){
                    setTimeout(this.events2calendar, 100, events)
                    return
                }
                this.clearCalendar()
                for(event of events){
                    const index = this.$store.state.plans.findIndex(plan => plan.id == event.plan_id)
                    const date = new Date(event.date)
                    this.calendar[''+date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()] = {
                        name: this.$store.state.plans[index].name,
                        id: event.plan_id
                    }
                }
            },
            updateEvents(){
                const url = 'api/calendar/'+this.date.getFullYear()+'-'+(this.date.getMonth()+1)+'-1'
                axios.get(url).then(res=>{
                    this.events2calendar(res.data)
                })
            },
            clearCalendar(){
                let year = this.date.getFullYear()
                let month = this.date.getMonth()+1
                let day = 1
                while(this.calendar[''+year+'-'+month+'-'+day]){
                    this.calendar[''+year+'-'+month+'-'+day] = {
                        name: null,
                        id: null,
                    }
                    day++
                }
            },
            monthly_check(){
                let pre = new Date(this.date.getFullYear(), this.date.getMonth(), 1-1)
                let suf = new Date(this.date.getFullYear(), this.date.getMonth()+1, 1)
                const height = document.body.clientHeight
                pre = document.getElementById(pre.getFullYear()+'-'+(pre.getMonth()+1)+'-'+pre.getDate())
                suf = document.getElementById(suf.getFullYear()+'-'+(suf.getMonth()+1)+'-'+suf.getDate())
                if(pre != null && height / 2 < pre.getBoundingClientRect().top){
                    this.date.setMonth(this.date.getMonth()-1)
                }
                else if(suf != null && height / 2 < height - suf.getBoundingClientRect().bottom){
                    this.date.setMonth(this.date.getMonth()+1)
                }
                else{
                    return
                }
                this.updateEvents()
            },
        }
    }
</script>
