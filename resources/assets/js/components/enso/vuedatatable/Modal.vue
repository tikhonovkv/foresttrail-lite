<template>

    <b-modal button-size="sm"
             v-model="showModal"
             v-on="$listeners"
             @ok="$emit('commit')"
             @cancel="$emit('close')"
             @hidden="$emit('close')"
        >
        <template v-slot:modal-title>{{ i18n("Confirm") }}</template>
        <template v-slot:modal-ok>{{ i18n("Yes") }}</template>
        <template v-slot:modal-cancel>{{ i18n("Cancel") }}</template>
        <template v-slot:default>{{ i18n(message || "Are you sure that you want to perform this action?") }}</template>
    </b-modal>

</template>

<script>

export default {

    props: {
        show: {
            type: Boolean,
            required: true,
        },
        message: {
            type: String,
            default: null,
        },
        i18n: {
            type: Function,
            required: true,
        },
    },

    data: function () {
        return {
            showModal: true
        }
    },

    watch: {
        show(val){
            this.showModal = val;
        }
    }
};

</script>
