<template>
    <div>
        <b-row v-if="showFilters">
            <b-col xs="12" md="6"
                   v-if="options2.length > 1">
                <treeselect  @input="selectPos"
                             :options="options2"
                             placeholder="Выберите точки продаж"
                             v-model="queryHas.provider_pos_id"
                             :normalizer="normalizer"
                             :multiple="true"
                />
            </b-col>
            <b-col xs="12" :md="(options2.length > 1 ? '6' : '12')" >
                <select-date-time-range v-model="queryHas.date"/>
            </b-col>
        </b-row>
        <b-row>
        </b-row>
        <div>
            <vue-table class="box"
                       :path="tablePath"
                       id="trackersReport"
                       @row-show="showDetails"
                       @toggle-filters="showFilters = !showFilters"
                       :params = "queryHas"
                       :i18n = "$vueTableI18n"

            />
        </div>
    </div>
</template>

<script>
    import VueTable from "../../components/enso/vuedatatable/VueTable";

    import { library } from '@fortawesome/fontawesome-svg-core';
    import {
        faFilter
    } from '@fortawesome/free-solid-svg-icons';
    import SelectDateTimeRange from "../manager/components/SelectDateTimeRange";
    library.add(faFilter);



    export default {
        components: {VueTable, SelectDateTimeRange},
        name:       "dashboard",
        data: function () {
            return {
                options2: [],
                queryHas: {
                    provider_pos_id: null,      // номер POS  передоваемое в запросе к серверу
                    date: null //
                },
                showFilters: false,
                params: {
                    reportDay_in_date_range: null
                },
            }
        },

        computed: {
            tablePath(){
                return configs.apiUrl + '/report-trackers/init';
            }
        },
        created: function () {
            this.InitPos(null);
        },
        methods: {
            showDetails(row){
                this.$router.push({name: 'providerReportDetails', params: {date: row.dtRowId}});
            },
            normalizer(node) {
                return {
                    id: node.key,
                    label: node.name,
                }
            },
            selectPos(e) {
                return console.log('событие selected - выполненно');
            },
            InitPos(id) {
                let url = configs.apiUrl + '/provider-pos/?provider_id=' + id;
                this.errors = {};
                this.axios.get(url)
                    .then((response_data) => {
                        this.fields = response_data.data[0];
                        this.queryHas.provider_pos_id =  this.fields.pos_id ;//
                        this.options2 = this.fields.pos_tmp;
                        console.log("данные сесии - загружены")
                    })
                    .catch((response_err) => {
                        this.errors = response_err
                    });
                return
            },
        }
    }
</script>

<style scoped>

</style>