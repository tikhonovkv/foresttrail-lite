<template>
    <b-dropdown variant="outline-warning"
                @show="visible = true"
                @hide="visible = false"
                no-caret>
        <template v-slot:button-content>
            <slot name="label"/>
            <span class="icon is-small angle"
                  :aria-hidden="!visible">
                <fa icon="angle-down"/>
            </span>
        </template>

        <slot/>

    </b-dropdown>
</template>

<script>

import { library } from '@fortawesome/fontawesome-svg-core';
import { faAngleDown } from '@fortawesome/free-solid-svg-icons';

library.add(faAngleDown);

export default {
    name: 'Dropdown',

    data: () => ({
        visible: false,
    }),
};

</script>

<style lang="scss" scoped>

    .dropdown-content {
        overflow-y: auto;
    }

    .icon.angle {
        transition: transform .300s ease;

        &[aria-hidden="true"] {
            transform: rotate(180deg);
        }
    }

    .icon {
        align-items: center;
        display: inline-flex;
        justify-content: center;

        .is-small {
            height: 1rem;
            width: 1rem;
        }
    }

</style>
