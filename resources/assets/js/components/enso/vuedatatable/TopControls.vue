<template>
    <div class="top-controls has-background-light">
        <b-row class="columns is-multiline">
            <b-col cols="12" sm="8" md="4" class="table-controls d-flex justify-content-between pb-3 pb-md-0">
                <length-menu :template="template"
                    :length="length"
                    v-on="$listeners"/>
                <column-visibility
                    :template="template"
                    :i18n="i18n"
                    v-on="$listeners"/>
                <style-selector
                    :template="template"
                    class="is-hidden-mobile"/>
                <b-button ref="reloadTable"
                    @click="$emit('reload')">
                    <span class="icon is-small">
                        <fa icon="sync"/>
                    </span>
                </b-button>
                <b-button
                    @click="$emit('reset')">
                    <span class="icon is-small">
                        <fa icon="undo"/>
                    </span>
                </b-button>
                <b-button class="button"
                    @click="$emit('request-full-info')"
                    v-if="info">
                    <span class="icon is-small has-text-info">
                        <fa icon="info-circle"/>
                    </span>
                </b-button>
            </b-col>
            <b-col cols="12" sm="4" md="4" class="table-buttons text-md-right pb-2 pb-md-0">
                <b-button
                    v-for="button in template.buttons.global"
                    :class="button.class"
                    :key="button.label"
                    :href="button.action === 'href' ? button.path : null"
                    @click="button.confirmation ? showModal(button) : doAction(button)">
                    <span class="is-hidden-mobile">
                    {{ i18n(button.label) }}
                    </span>
                    <span class="icon is-small">
                        <fa :icon="button.icon"/>
                    </span>
                    <span class="is-hidden-mobile"/>
                </b-button>
            </b-col>
            <b-col cols="12" md="4" class="search-input">
                <div class="control has-icons-left has-icons-right"
                    v-if="template.searchable">
                    <b-input-group>
                        <span class="icon is-small is-left">
                            <fa icon="search"/>
                        </span>
                        <b-input class="input text-center is-rounded"
                            type="text"
                            :value="value"
                            @input="$emit('input', $event)"
                            :placeholder="i18n('Search')">
                        </b-input>
                    </b-input-group>

                    <span class="icon is-small is-right clear-button"
                        v-if="value && !loading"
                        @click="$emit('input', '')">
                        <a class="delete is-small"/>
                    </span>
                </div>
            </b-col>
        </b-row>
        <modal v-if="modal"
            :show="modal"
            :i18n="i18n"
            :message="button.message"
            @close="closeModal()"
            @commit="doAction(button)"/>
    </div>
</template>

<script>

import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faSync, faUndo, faSearch, faInfoCircle,
} from '@fortawesome/free-solid-svg-icons';
import LengthMenu from './topControls/LengthMenu.vue';
import ColumnVisibility from './topControls/ColumnVisibility.vue';
import StyleSelector from './topControls/StyleSelector.vue';
import Modal from './Modal.vue';

library.add(faSync, faUndo, faSearch, faInfoCircle);

export default {
    name: 'TopControls',

    components: {
        LengthMenu, ColumnVisibility, StyleSelector, Modal,
    },

    props: {
        template: {
            type: Object,
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
        value: {
            type: String,
            required: true,
        },
        info: {
            type: Boolean,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
    },

    data: () => ({
        lengthMenu: false,
        columnVisibility: false,
        modal: false,
        button: null,
    }),

    methods: {
        showModal(button) {
            this.button = button;
            this.modal = true;
        },
        closeModal() {
            this.modal = false;
            this.button = null;
        },
        doAction(button) {
            if (this.modal) {
                this.closeModal();
            }

            if (button.event) {
                this.$emit(button.event);
            }

            if (button.action === 'export') {
                this.$emit('export-data', button.path);
                return;
            }

            if (button.action === 'router') {
                this.$router.push({ name: button.route });
                return;
            }

            if (button.action === 'ajax') {
                this.$emit('action', button.method, button.path, button.postEvent);
            }
        },
    },
};

</script>

<style lang="scss" scoped>

    .top-controls {
        padding: 1em;
        padding-bottom: 1.5em;

        .control.has-icons-right .icon.clear-button {
            pointer-events: all;
        }

        @media screen and (min-width: 1024px) {
            .table-controls {
                order: 1;
            }

            .search-input {
                order: 2;
            }

            .table-buttons {
                order: 3;
            }
        }
    }

</style>
