<template>
    <nav class="pagination is-small"
        role="navigation"
        aria-label="pagination">

        <ul class="pagination"
            v-if="extended">
            <li class="page-item pagination-previous"
                :class="{ 'disabled': page === 1 || loading }"
            >
                <a class="page-link"
                   @click="jumpTo(page - 1)">
                    {{ i18n('Previous') }}
                </a>
            </li>
            <li class="page-item"
                :class="{ 'active': page === 1 }">
                <a class="page-link"
                    :disabled="loading"
                    @click="jumpTo(1)">
                    1
                </a>
            </li>
            <li class="page-item" v-if="pages > 5 && !atStart">
                <span class="pagination-ellipsis">
                    &hellip;
                </span>
            </li>
            <li class="page-item" v-for="i in middlePages"
                :class="{ 'active': page === i }"
                :key="i">
                <a class="page-link"
                    :disabled="loading"
                    @click="jumpTo(i)">
                    {{ i }}
                </a>
            </li>
            <li class="page-item" v-if="pages > 5 && !atEnd">
                <span class="pagination-ellipsis">
                    &hellip;
                </span>
            </li>
            <li class="page-item" v-if="pages > 1"
                :class="{ 'active': page === pages }">
                <a class="page-link"
                    :disabled="loading"
                    @click="jumpTo(pages)">
                    {{ pages }}
                </a>
            </li>
            <li class="page-item pagination-next"
                :class="{ 'disabled': page === pages || loading }"
               >
                <a class="page-link"
                   @click="jumpTo(page + 1)">
                    {{ i18n('Next') }}
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>

export default {
    name: 'Pagination',

    props: {
        loading: {
            type: Boolean,
            required: true,
        },
        records: {
            type: Number,
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
        extended: {
            type: Boolean,
            required: true,
        },
        i18n: {
            type: Function,
            required: true,
        },
    },

    computed: {
        page() {
            return (this.start / this.length) + 1;
        },
        pages() {
            return Math.ceil(this.records / this.length);
        },
        atStart() {
            return this.page < 4;
        },
        atEnd() {
            return this.pages - this.page < 3;
        },
        middlePages() {
            const pages = [];

            if (this.atStart) {
                const max = Math.min(this.pages - 1, 4);
                for (let i = 2; i <= max; i++) {
                    pages.push(i);
                }

                return pages;
            }

            if (this.atEnd) {
                if (pages > 4) {
                    pages.push(this.pages - 3);
                }

                pages.push(this.pages - 2, this.pages - 1);
                return pages;
            }

            pages.push(this.page - 1, this.page, this.page + 1);

            return pages;
        },
    },

    methods: {
        jumpTo(page) {
            if (page === this.page || page < 1 || page > this.pages) {
                return;
            }

            this.$emit('jump-to', (page - 1) * this.length);
        },
    },
};

</script>

<style lang="scss" scoped>

    .pagination {
        padding: 0.75em;
        margin: 0;
    }

</style>
