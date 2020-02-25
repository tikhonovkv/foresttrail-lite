<template>

    <div>
        <share @close="openedShare = false" v-if="openedShare"/>
        <!--<contacts @close="openedContacts = false" v-if="openedContacts"/>-->
        <div class="row pt-4" v-else>
            <div class="col-12 pb-4">
                <label class="label">Добавить трекер</label>
                <add-tracker/>
            </div>
            <div class="col-12">
                <div v-if="common.trackers.length > 0">
                    <label class="label">Трекеры на карте</label>
                    <tracker-field v-for="(tracker, index) in common.trackers"
                        :key="index"
                        :value="tracker"
                    />
                </div>
                <div v-else>
                </div>
            </div>
            <div class="col-12 pb-2" v-if="common.trackers.length > 1">
                <button class="btn btn-white w-100 share"
                        @click="openedShare = true">
                    <span>Поделиться трекерами</span>
                    <svg class="icon icon-share"><use xlink:href="#icon-share"></use></svg>
                </button>
            </div>
            <div class="col-12">
                <button class="btn btn-white w-100 share"
                        @click="openJivoSite">
                    <span>Чат с поддержкой</span>
                    <svg class="icon icon-contact"><use xlink:href="#icon-contact"></use></svg>
                </button>
            </div>
            <div class="col-12 space-bottom-button"></div>
            <div class="col-12 bottom-fixed bottom-buttons shadow">
                <button class="btn w-100 next" @click="close">
                    <span>Открыть карту</span>
                    <svg class="icon icon-next"><use xlink:href="#icon-next"></use></svg>
                </button>
            </div>
        </div>
    </div>

</template>

<script>
    import TrackerField from "./TrackerField";
    import AddTracker from "./AddTracker";
    import Share from "./Share";
    //import Contacts from "./Contacts";

    export default {
        components: {
            AddTracker,
            TrackerField,
            Share,
            //Contacts
        },
        name:       "settings",
        data: function () {
            return {
                openedShare: false,
                openedContacts: false
            };
        },
        computed: {
            common(){
                return this.$store.state;
            },
        },
        methods: {
            close() {
                this.$emit('close');
            },
            openJivoSite() {
                jivo_api.open();
            },
        }
    }
</script>

<style scoped>

</style>