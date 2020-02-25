<template>
    <div style="position: inherit;">
        <div class="add-tracker">
            <input class="alias" placeholder="Код трекера"
                @keydown="error = false"
                @keydown.enter="add"
                v-model="alias"
            />
            <button class="btn" @click="add">+</button>
            <label class="error" v-show="error">{{error}}</label>
        </div>
    </div>
</template>

<script>
    export default {
        name: "add-tracker",
        computed: {
            common(){
                return this.$store.state;
            },
        },
        data:function () {
            return {
                alias: null,
                error: false
            }
        },
        methods:{
            add(){
                this.validate().then(()=>{
                    let tracker = {
                        name: '',
                        alias: this.alias,
                        blocked: false,
                        timePassed: null,
                        battery: null
                    };
                    this.common.trackers.push(tracker);

                    this.alias = null;
                }).catch(()=>{
                    return;
                });
            },
            validate(){
                return new Promise((resolve, reject) => {

                    this.error = !this.alias ? 'Укажите код трекера' : false;
                    if(this.$findTrackerByAlias(this.alias) !== false){
                        this.error = 'Трекер уже добавлен на карту';
                    }
                    if(this.error){reject();}

                    this.axios.get(configs.apiUrl + '/device/validate-alias/' + this.alias)
                        .then((response) => {
                            this.error = response.data.error;
                            if(this.error){reject();}else{resolve();}
                        });

                });
            }
        }
    }
</script>

<style scoped>

</style>