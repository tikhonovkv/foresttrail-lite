<template>
<div>
    <div class="map-wrapper" ref="loadingContainer">
        <yandex-map
            :settings="common.yandexMapSettings"
            :options="{
                    suppressMapOpenBlock: true,
                    yandexMapDisablePoiInteractivity: true,
                    maxZoom: 18
                }"
            :coords="centerMap"
            zoom="18"
            @map-was-initialized="initYmap"
            :behaviors="['drag','multiTouch','dblClickZoom','scrollZoom']"
            :controls="['typeSelector', 'searchControl', 'fullscreenControl']"
        >
        </yandex-map>
    </div>
</div>
</template>

<script>
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';

    export default {
        name: "view-tracks",
        props: ['tracks'],
        components: {
            yandexMap
        },

        mounted() {
            this.loader = this.$loading.show({
                container: this.$refs.loadingContainer
            });
        },

        data() {
            return {
                ymaps: null,
                map: null,
                mapWasInitialized: false,
                centerMap: [53.757171, 87.136716],
                mapPaths: null,
                loader: null
            }
        },

        computed: {
            common() {
                return this.$store.state;
            }
        },

        watch: {
            tracks(val, oldVal){
                if(val !== oldVal) {
                    this.showTracks(val);
                }
            }
        },

        methods: {
            initYmap(a) {
                this.map = a;
                this.ymaps = ymaps;

                require('../../services/YmapsAnimatedLine');
                ymaps.ready(['AnimatedLine']).then(() => {
                    this.loader.hide();

                    if (!this.mapWasInitialized) {
                        this.mapWasInitialized = true;
                    }

                    this.showTracks();
                });
            },

            clearPaths(){
                if( ! this.mapPaths ){ return false; }
                this.map.geoObjects.remove(this.mapPaths);
                this.mapPaths = null;

                return true;
            },

            showTracks() {
                this.clearPaths();

                let tracks = Object.assign({}, this.tracks);

                let collection = (new this.ymaps.GeoObjectCollection({},{}));

                for (let track in tracks) {
                    if(tracks[track].length <= 2) {continue;}
                    collection = this.initTrack(tracks[track], collection);
                }
                this.mapPaths = collection;
                this.map.geoObjects
                    .add(collection);

                this.map.setBounds(collection.getBounds());

                collection.each((a) => {
                    if( ! a.reset){return;}
                    a.reset();
                });

                let iterator = collection.getIterator();
                this.recursiveAnimate(iterator);

            },

            recursiveAnimate(iterator){
                let nextPath = iterator.getNext();
                while(typeof nextPath.animate === 'undefined' || nextPath === iterator.STOP_ITERATION){
                    if(nextPath === iterator.STOP_ITERATION){break;}
                    nextPath = iterator.getNext();
                }

                if(nextPath != iterator.STOP_ITERATION){
                    return nextPath.animate().then(_ => this.recursiveAnimate(iterator))
                }else{
                    return Promise.resolve();
                }
            },

            ColorLuminance(hex, lum) {
                // validate hex string
                hex = String(hex).replace(/[^0-9a-f]/gi, '');
                if (hex.length < 6) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                }
                lum = lum || 0;

                // convert to decimal and change luminosity
                var rgb = "#", c, i;
                for (i = 0; i < 3; i++) {
                    c = parseInt(hex.substr(i*2,2), 16);
                    c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
                    rgb += ("00"+c).substr(c.length);
                }

                return rgb;
            },

            initTrack(track, collection){

                let lighten = collection.getLength() % 2 === 0;
                collection.add( new this.ymaps.AnimatedLine(this.tracksAsCoordinates(track), {
                }, {
                    balloonCloseButton: false,
                    strokeColor:        this.ColorLuminance("#475fff", lighten ? 0.5 : -0.5),
                    strokeWidth:        6,
                    strokeOpacity:      0.8,
                    animationTime: 4000
                }) );

                let firstPoint = track[0];
                collection.add( new this.ymaps.GeoObject({
                    geometry: {
                        type: "Point",
                        coordinates: [ firstPoint.lat, firstPoint.lon ]
                    },
                    properties: {
                        iconContent: firstPoint.created_at,
                        hintContent: 'Начало'
                    }
                }, {
                    preset: 'islands#orangeStretchyIcon'
                }) );

                let lastPoint = track[track.length - 1];
                collection.add( new this.ymaps.GeoObject({
                    geometry: {
                        type: "Point",
                        coordinates: [ lastPoint.lat, lastPoint.lon ]
                    },
                    properties: {
                        iconContent: lastPoint.created_at,
                        hintContent: 'Конец'
                    }
                }, {
                    preset:    'islands#greenStretchyIcon'
                }) );

                return collection;
            },

            tracksAsCoordinates(track){
                let coords = [];
                track.forEach((coord) => {
                    coords.push([coord.lat, coord.lon, coord.created_at, coord.accuracy]);
                });
                return coords;
            },
        }
    }
</script>

<style lang="scss" scoped>

div ::v-deep .map-wrapper {
    @import "~@styles/partials/view_tracks.scss";
}

</style>