<template>
    <b-row class="columns align-items-baseline py-3">
        <b-col cols="12" sm="6" class="pb-3 pb-md-0">
            <pagination :loading="loading"
                        :start="start"
                        :length="length"
                        :records="body.filtered"
                        :i18n="i18n"
                        :extended="body.fullRecordInfo"
                        @jump-to="$emit('jump-to', $event)"
                        v-if="hasEntries"/>
        </b-col>
        <b-col cols="12" sm="6" class="text-sm-right">
            <records-info :body="body"
                :i18n="i18n"
                :start="start"
                :selected="selected"/>
        </b-col>
    </b-row>
</template>

<script>

import RecordsInfo from './RecordsInfo.vue';
import Pagination from './Pagination.vue';

export default {
    name: 'BottomControls',

    components: { RecordsInfo, Pagination },

    props: {
        body: {
            type: Object,
            required: true,
        },
        start: {
            type: Number,
            required: true,
        },
        length: {
            type: Number,
            required: true,
        },
        i18n: {
            type: Function,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
        selected: {
            type: Array,
            required: true,
        },
    },

    computed: {
        hasEntries() {
            return this.body.data.length > 0;
        },
    },
};

</script>
