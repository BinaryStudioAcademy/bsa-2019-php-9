<template>
    <div
        :style="{ width: width + 'px', height: height + 'px' }"
    >
        <img v-if="src" src="src">
        <span v-else>No image</span>
    </div>
</template>
<script>
    export default {
        props: {
            src: String,
            width: Number,
            height: Number
        }
    }
</script>

<style lang="scss" scoped>
    div {
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #797971;
        margin: 5px 10px;

        img {
            max-width: 100%;
            height: auto;
        }
    }
</style>
