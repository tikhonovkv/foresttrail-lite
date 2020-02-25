<template>
    <div class="overlay is-overlay">
        <div class="overlay-loader"/>
    </div>
</template>

<script>

export default {
    name: 'Overlay',
};

</script>

<style scoped>

    .overlay {
        position: absolute;
        top: 0px;
        width: 100%;
        height: calc(100% - 2px);
        background: #444;
        display: flex;
    }

    .overlay-loader {
        margin: auto;
        -webkit-animation: spinAround 500ms infinite linear;
        animation: spinAround 500ms infinite linear;
        border-radius: 50%;
        content: "";
        width: 2em;
        height: 2em;
        border: 2px solid#f44336;
        border-right-color: transparent;
        border-top-color: transparent;
    }

</style>
