<template>
    <li class="body-editer">
        <input type="time" step="600" :value="time" @change="$emit('update:time', $event.target.value)" @blur="$emit('refresh:us')">
        <span>:</span>
        <input type="text" :value="text" @input="$emit('update:text', $event.target.value)">
        <button @click="$emit('edit:me', index)">
            <slot>
                <font-awesome-icon icon="trash-alt" size="2x"/>
            </slot>
        </button>
    </li>
</template>



<script>
    export default {
        props: {
            time: {},
            text: {},
            index: {},
        },
    }
</script>
