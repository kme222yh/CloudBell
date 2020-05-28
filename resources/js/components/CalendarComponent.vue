<template>
<div id="calendar">
    <p class="year-month">{{focused.now.getFullYear()}} / {{focused.now.getMonth()+1}}</p>

    <ul class="day-bar">
        <li><p>Sun</p></li>
        <li><p>Mon</p></li>
        <li><p>Tue</p></li>
        <li><p>Wed</p></li>
        <li><p>Thu</p></li>
        <li><p>Fri</p></li>
        <li><p>Sat</p></li>
    </ul>

    <ul class="body">
        <li :style="day_style" v-for="n in calendar_prefix"><a></a></li>
        <li :style="day_style" v-for="(day, date) of calendar" :class="{focused: day.focused, today: day.istoday}" v-bind:id="date" v-bind:key="date">
            <router-link :to="{name: 'day', params: {date: date}}">
                <p>{{date2day(date)}}</p>
                <p v-show="day.name">{{day.name}}</p>
            </router-link>
        </li>
        <li :style="day_style" v-for="n in calendar_sufix"><a></a></li>
    </ul>

    <transition><router-view @update="updateEvents"></router-view></transition>
</div>
</template>





<script>
    export default {
        data: ()=>({
            focused: {
                now: new Date,
                previous: new Date,
            },
            calendar: {},
            interval: Array(2),

            day_style: {
                height: '100px',
            },
        }),
        created(){
            this.createCalendar()
            this.focused.now.setDate(1)
            this.focused.previous.setDate(1)
            this.focused.previous.setMonth(this.focused.previous.getMonth()+1)
            this.ajustDayHeight()
        },
        mounted(){
            const height = document.getElementById(this.focused.now.getFullYear()+'-'+(this.focused.now.getMonth()+1)+'-'+this.focused.now.getDate()).getBoundingClientRect().top
            document.getElementsByClassName('body')[0].scrollTop = document.getElementsByClassName('body')[0].scrollTop + height - 100
            this.reFocusAndClearCalendar()
            this.updateEvents()
            this.interval[0] = (setInterval(this.month_check, 100))
            this.interval[1] = (setInterval(this.ajustDayHeight))
        },
        beforeDestroy(){
            clearInterval(this.interval[0])
            clearInterval(this.interval[1])
        },
        computed: {
            calendar_prefix(){
                return (new Date(Object.keys(this.calendar)[0].replace(/-/g,"/"))).getDay()
            },
            calendar_sufix(){
                return 6-(new Date(Object.keys(this.calendar)[Object.keys(this.calendar).length - 1].replace(/-/g,"/"))).getDay()
            },
        },
        methods: {
            date2day(date){
                return (new Date(date.replace(/-/g,"/"))).getDate()     // replace for safari マジクソ
            },
            events2calendar(events){
                for(event of events){
                    const index = this.$store.state.plans.findIndex(plan => plan.id == event.plan_id)
                    const date = new Date(event.date)
                    this.calendar[''+date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()].name = this.$store.state.plans[index].name,
                    this.calendar[''+date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()].id = event.plan_id
                }
                this.$forceUpdate()
            },
            updateEvents(){
                this.reFocusAndClearCalendar()
                const url = 'api/calendar/'+this.focused.now.getFullYear()+'/'+(this.focused.now.getMonth()+1)
                axios.get(url).then(res=>{
                    this.events2calendar(res.data)
                })
            },
            reFocusAndClearCalendar(){
                let year_month = this.focused.now.getFullYear() + '-' + (this.focused.now.getMonth()+1) + '-'
                let day = 1
                while(this.calendar[year_month+day]){
                    this.calendar[year_month+day].name = null
                    this.calendar[year_month+day].id = null
                    this.calendar[year_month+day].focused = true
                    day++
                }
                year_month = this.focused.previous.getFullYear() + '-' + (this.focused.previous.getMonth()+1) + '-'
                day = 1
                while(this.calendar[year_month+day]){
                    this.calendar[year_month+day].focused = false
                    day++
                }
            },
            month_check(){
                let pre = new Date(this.focused.now.getFullYear(), this.focused.now.getMonth(), 1-1)
                let suf = new Date(this.focused.now.getFullYear(), this.focused.now.getMonth()+1, 1)
                const height = document.body.clientHeight
                pre = document.getElementById(pre.getFullYear()+'-'+(pre.getMonth()+1)+'-'+pre.getDate())
                suf = document.getElementById(suf.getFullYear()+'-'+(suf.getMonth()+1)+'-'+suf.getDate())
                if(pre != null && height / 2 < pre.getBoundingClientRect().bottom){
                    this.focused.previous.setMonth(this.focused.now.getMonth())
                    this.focused.now.setMonth(this.focused.now.getMonth()-1)
                }
                else if(suf != null && height / 2 < height - suf.getBoundingClientRect().bottom){
                    this.focused.previous.setMonth(this.focused.now.getMonth())
                    this.focused.now.setMonth(this.focused.now.getMonth()+1)
                }
                else{
                    return
                }
                this.updateEvents()
            },

            ajustDayHeight(){
                const height = document.getElementById('app').clientHeight / 6 - 7
                this.day_style.height = height + 'px'
            },




            createCalendar(){
                let date = new Date
                const previous = this.$store.state.config.calendar.lange.previous-0 // 文字列を数値に変換
                const feature = this.$store.state.config.calendar.lange.feature-0
                date.setDate(1)
                date.setMonth(date.getMonth()-previous)
                let m = 0
                while(m < previous+feature+1){
                    let year = date.getFullYear()
                    while(year == date.getFullYear()){
                        let month = date.getMonth()+1
                        while(Math.abs(date.getMonth() - month) == 1){
                            let day = date.getDate();
                            this.calendar[''+year+'-'+month+'-'+day] = {
                                name: null,
                                id: null,
                                focused: false,
                                istoday: false
                            }
                            date.setDate(day+1)
                        }
                        if(++m >= previous+feature+1)  break
                    }
                }
                const today = new Date
                this.calendar[today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()].istoday = true
            },
        }
    }
</script>
