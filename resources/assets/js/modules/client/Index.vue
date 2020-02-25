<template>

<div class="index">
    <div class="container" v-show="!hideSettings">
        <div class="row">
            <div class="col-sm-8 col-12 offset-sm-2">
                <settings
                        @close="hideSettings = !hideSettings"
                ></settings>
            </div>
        </div>
    </div>
    <div v-show="hideSettings">
        <div class="container">
            <div class="row">
                <div class="top-fixed">
                    <button class="btn mini settings" @click="hideSettings = !hideSettings">
                        <svg class="icon icon-cog"><use xlink:href="#icon-cog"></use></svg>
                    </button>
                </div>
                <div class="top-fixed w-100 d-flex justify-content-center" v-if="noSynchronize">
                    <svg class="icon icon-cog"><use xlink:href="#icon-no-synchronize"></use></svg>
                </div>
                <div class="col bottom-fixed bottom-buttons" v-if="common.trackers.length > 1 && selectedTrackerName">
                    <button class="btn w-100 next" @click="selectNextTracker">
                        <span class="name">
                            <div>{{selectedTrackerName}}</div>
                            <div class="time-passed" v-if="selectedTracker.timePassed">{{selectedTracker.timePassed}}</div>
                        </span>
                        <span>
                            <svg class="icon icon-next"><use xlink:href="#icon-next"></use></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div ref="wrapperMap">
            <yandex-map
                    :options="{
                        suppressMapOpenBlock: true,
                        yandexMapDisablePoiInteractivity: true
                    }"
                    :settings="common.yandexMapSettings"
                    :scroll-zoom="true"
                    :coords="centerMap"
                    zoom="16"
                    :style="styleMap"
                    :cluster-options="{
                        1: {clusterDisableClickZoom: true}
                      }"
                    :behaviors="['drag','multiTouch','dblClickZoom','scrollZoom']"
                    :controls="['typeSelector']"
                    map-type="map"
                    @map-was-initialized="initYmap"

            />
        </div>
    </div>
</div>

</template>

<script>
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';
    import Settings from "./components/Settings";

    export default {
        name: "webLocator",
        components: {
            Settings,
            yandexMap
        },
        mounted(){
            this.loader = this.$loading.show({
                container: this.$refs.wrapperMap
            });

            let intervalResetHeight = setInterval(() => {
                if(this.appHeight > 0 && this.map){
                    this.styleMap.height = this.appHeight + 'px';
                    clearInterval(intervalResetHeight);
                }
            }, 50);

            if(this.common.trackers.length > 0){
                this.hideSettings = true;
            }
        },

        computed: {
            common(){
                return this.$store.state;
            },
            appHeight(){
                return this.$store.state.dimension.height;
            },
            groupCoordinatesAsArray(){
                let coords = {};
                for(let tracker in this.group){
                    if(this.group.hasOwnProperty(tracker)) {
                        coords[tracker] = [];
                        this.group[tracker].coords.forEach((coord) => {
                            coords[tracker].push([coord.lat, coord.lon, coord.timestamp, coord.accuracy]);
                        });
                    }
                }
                return coords;
            },
            selectedTracker(){
                return this.getTracker(this.selectedTrackerAlias);
            },
            selectedTrackerName(){
                if( ! this.selectedTracker){return false;}
                return this.selectedTracker.name ? this.selectedTracker.name : this.selectedTracker.alias;
            },
            group(){
                let result = {};
                for(let alias in this.aliasCoordinates){
                    let tracker = this.aliasCoordinates[alias];
                    let information = this.getTracker(alias);
                    if(!information){continue;}
                    information.timePassed = tracker.time_passed;
                    information.battery = tracker.battery;
                    if(tracker.blocked){
                        if(this.selectedTrackerAlias === alias){
                            this.selectedTrackerAlias = null;
                        }
                        information.blocked = true;
                        continue;
                    }
                    information.blocked = false;
                    if(!tracker.hasOwnProperty('coords')){continue;}
                    result[alias] = tracker;
                }
                return result;
            }
        },

        watch: {
            appHeight(val){
                this.styleMap.height = this.appHeight + 'px';
            },
            group: {
                handler(){
                    this.refreshTrackers();
                },
                deep:true
            },
            hideSettings(val){
                if(val && this.map){
                    setTimeout(()=>this.map.container.fitToViewport());
                    this.$root.$emit('windowResize');
                    this.intervalLoadCoordinates().then(() => this.refreshTrackers(true));
                }
            },
            // 'common.trackers': {
            //     handler(){
            //         this.refreshTrackers(true);
            //     },
            //     deep: true
            // }
        },

        data() {
            return {
                ymaps:             null,
                map:               null,
                centerMap:         [54.42896654088406, 39.431893822753904],
                styleMap:          {
                    width: '100%',
                    height: '0%',
                    display: 'flex',
                },
                loader: null,
                hideSettings: false,
                selectedTrackerAlias: null,
                groupMarkers: null,
                aliasCoordinates: {},
                intervalCoordinates: null,
                noSynchronize: false
            }
        },

        methods: {
            getTracker(alias){
                return this.common.trackers[ this.$findTrackerByAlias(alias) ];
            },

            intervalLoadCoordinates(){
                return new Promise((resolve, reject)=>{
                    clearInterval(this.intervalCoordinates);
                    this.intervalCoordinates = setInterval(this.loadCoordinates, 10000);
                    this.loadCoordinates().then(()=>resolve());
                });
            },

            loadCoordinates(){
                return new Promise((resolve, reject)=> {
                    let trackers = this.$trackersAlias();
                    if(trackers.length === 0){resolve(); return false;}
                    this.axios.post(configs.apiUrl + "/coordinates/via-alias", {trackers})
                        .then((response) => {
                            this.noSynchronize = false;
                            this.aliasCoordinates = response.data;
                            resolve(response.data);
                            if (this.selectedTrackerAlias) {return;}
                            this.selectedTrackerAlias = Object.keys(this.group)[0];
                        }).catch((error) => {
                            this.noSynchronize = true;
                            console.log(error);
                    });
                });
            },

            initYmap(a) {
                this.map = a;
                this.ymaps = ymaps;

                if( ! this.mapWasInitialized){
                    this.mapWasInitialized = true;
                }
                this.refreshTrackers();
                this.intervalLoadCoordinates().then(() => this.loader.hide());
            },

            selectNextTracker(){
                let indexTracker = this.$findTrackerByAlias(this.selectedTrackerAlias);

                let lastBalloon = this.groupMarkers[this.selectedTrackerAlias].get(1);
                lastBalloon.options.set('zIndex', 0);
                lastBalloon.options.set('iconColor', '#7f78e4');

                indexTracker = this.loopIndexTracker(indexTracker);

                let alias;
                if( this.groupMarkers.length > 0 ){
                    for(var i = 0; i < this.common.trackers.length; i++){
                        alias = this.common.trackers[indexTracker].alias;
                        if(this.groupMarkers[alias]){ break; }
                        indexTracker = this.loopIndexTracker(indexTracker);
                    }
                }
                this.selectedTrackerAlias = alias;

                let nextBalloon = this.groupMarkers[this.selectedTrackerAlias].get(1);
                nextBalloon.options.set('zIndex', 1);
                nextBalloon.options.set('iconColor', '#603ce4');

                this.centeringBySelectedTracker();
            },

            loopIndexTracker(indexTracker)
            {
                if(indexTracker >= this.common.trackers.length - 1){
                    indexTracker = 0;
                }else{
                    indexTracker++;
                }
                return indexTracker;
            },

            centeringBySelectedTracker(){
                if( ! this.selectedTrackerAlias || ! this.map){return false;}
                if( ! this.group[this.selectedTrackerAlias]){
                    this.selectedTrackerAlias = Object.keys(this.group)[0];
                }
                let coords = this.group[this.selectedTrackerAlias].coords;
                let coordsCount = coords.length;
                if(coordsCount === 0){return false;}
                let coord = coords[coordsCount - 1];

                setTimeout(()=> {
                    this.map.panTo([coord.lat, coord.lon], {
                            duration: 500,
                            timingFunction: 'ease-in-out',
                            safe: false,
                            flying: false
                        })
                },20);
            },

            refreshTrackers(reinit){
                if(reinit && this.groupMarkers){
                    for(let i in this.groupMarkers) {
                        let tracker = this.groupMarkers[i];
                        this.map.geoObjects.remove(tracker);
                    }
                    this.groupMarkers = null;
                }

                let group = Object.assign({}, this.group);
                if( this.groupMarkers){
                    for(let i in this.groupMarkers) {
                        let tracker = this.groupMarkers[i];
                        if( group.hasOwnProperty(i) ) {

                            let trail = tracker.get(0);
                            let point = tracker.get(1);

                            trail.geometry.setCoordinates(this.groupCoordinatesAsArray[i]);

                            let coord = group[i].coords[group[i].coords.length - 1];
                            point.geometry.setCoordinates([coord.lat, coord.lon]);
                        }else{
                            this.map.geoObjects.remove(tracker);
                        }
                    }
                } else if(Object.keys(group).length > 0) {
                    this.groupMarkers = [];

                    for (let alias in group) {
                        this.initTracker(alias);
                    }
                }

                this.centeringBySelectedTracker();
            },

            initTracker(alias){
                let trackerIndex = this.$findTrackerByAlias(alias);
                if(trackerIndex === false){return false;}

                let item = this.group[alias];
                if(trackerIndex === false || item.coords.length === 0){return false;}
                let coord = item.coords[item.coords.length - 1];

                let collection = (new this.ymaps.GeoObjectCollection({},{}));
                // добавляем след
                collection.add( new this.ymaps.Polyline(this.groupCoordinatesAsArray[alias], {
                }, {
                    balloonCloseButton: false,
                    strokeColor:        "#475fff",
                    strokeWidth:        6,
                    strokeOpacity:      0.8
                }) );

                let tracker = this.common.trackers[trackerIndex];

                // добавляем точку
                collection.add( new this.ymaps.Placemark([coord.lat, coord.lon], {
                    iconCaption: tracker.name ? tracker.name : tracker.alias,
                }, {
                    preset:              'islands#blueCircleDotIconWithCaption',
                    iconCaptionMaxWidth: '100',
                    iconColor: '#7f78e4',
                    interactiveZIndex: false,
                    zIndex: 0
                }));

                this.groupMarkers[alias] = collection;

                this.map.geoObjects
                    .add(this.groupMarkers[alias]);
            },
        }
    }
</script>

<style scoped>
</style>