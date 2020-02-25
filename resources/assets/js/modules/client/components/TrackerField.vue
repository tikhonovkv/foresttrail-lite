<template>
    <div class="tracker-field" :class="{'blocked': value.blocked}">
        <div class="tracker">
            <div class="alias"><b>{{value.alias}}</b><span v-if="value.timePassed"> отметился {{value.timePassed}}</span></div>
            <div class="alias" v-if="value.battery">Батарея: {{value.battery}}%</div>
            <div class="name">
                <input placeholder="Введите имя"
                    v-model="value.name"
                    v-if="!value.blocked"
                />
                <span v-else>ЗАБЛОКИРОВАН</span>
            </div>
        </div>
        <div class="delete" @click="deleteTracker">
            <svg class="icon icon-close"><use xlink:href="#icon-close"></use></svg>
        </div>
    </div>
</template>

<script>
    export default {
        name: "tracker-field",
        props: [
            'value'
        ],
        computed: {
            common(){
                return this.$store.state;
            },
        },
        methods: {
            deleteTracker(){
                if(confirm("Удалить трекер с карты?")){
                    let trackers = this.common.trackers;
                    let index = this.$findTrackerByAlias(this.value.alias);
                    if(index !== false) {
                        trackers.splice(index, 1);
                        this.$emit('delete');
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>