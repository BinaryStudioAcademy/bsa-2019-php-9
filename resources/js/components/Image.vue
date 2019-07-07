<template>
    <div
        :style="{ width: width + 'px', height: height + 'px' }"
    >
        <img v-if="src" :src="src">
        <span v-else-if="processing" class="processing">Processing</span>
        <span v-else>{{ width }}x{{ height }}</span>
    </div>
</template>
<script>
    export default {
        props: {
            src: String,
            width: Number,
            height: Number,
            status: String
        },
        computed: {
            processing() {
                return this.status === 'processing';
            }
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

    .processing {
        white-space: nowrap;

        &:after {
            content: '';
            display: inline-block;
            width: 15px;
            animation: procesiing 3s infinite;

            @keyframes procesiing {
                from { content: '' }
                15% { content: '.' }
                30% { content: '..' }
                45% { content: '...' }
                60% { content: '..' }
                75% { content: '.' }
                to { content: '' }
            }
        }
    }
</style>
