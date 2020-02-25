<template>

    <div class="row pt-4 screen-share" ref="wrapper">
        <div class="col-12 pb-4 general">
            <label class="label">
                Поделиться

                <a class="close" @click="$emit('close')">
                    <svg class="icon icon-close"><use xlink:href="#icon-close"></use></svg>
                </a>
            </label>
            <div class="center trackers-link">
                <label class="label">Нажмите на ссылку, чтобы скопировать</label>
                <textarea placeholder="Ссылка" readonly @click="copy">{{link}}</textarea>
                <label class="label sub-label" v-show="copied">Ссылка скопирована, можно отправлять друзьям</label>
            </div>
        </div>
        <div class="col-12 bottom-fixed bottom-buttons shadow">
            <button class="btn w-100 next" @click="$emit('close')">
                <span>Назад</span>
                <svg class="icon icon-next"><use xlink:href="#icon-next"></use></svg>
            </button>
        </div>
    </div>

</template>

<script>
    export default {
        name: "webLocator",
        mounted(){
            this.loader = this.$loading.show();
            this.getLink();
        },

        computed: {
            common(){
                return this.$store.state;
            },
            appHeight(){
                return this.$store.state.dimension.height;
            },
        },

        watch: {
            appHeight(val){
                this.styleMap.height = this.appHeight + 'px';
            },
        },

        data() {
            return {
                link: null,
                copied: false,
                timer: null,
                loader: null
            }
        },

        methods: {
            copy(e){
                e.target.select();
                document.execCommand("copy");
                this.copied = true;

                clearTimeout(this.timer);
                this.timer = setTimeout(() => {
                    this.copied = false;
                }, 10000);
            },
            getLink(){

                this.axios.post(configs.apiUrl + "/share/get-link", {'trackers': this.$store.state.trackers})
                    .then((response) => {
                        this.link = response.data.link;
                        this.loader.hide()
                    }).catch((error) => {
                        console.log(error);
                    });
            }
        }
    }
</script>

<style scoped>
</style>