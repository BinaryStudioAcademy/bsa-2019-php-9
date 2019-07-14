<template>
    <label :class="{ success: success, fail: fail }">
        <input ref="file" type="file" v-on:change="onChange" v-on:click="reset">
        <span class="placeholder">Upload<br />Photo</span>
    </label>
</template>
<script>
    export default {
        props: {
            status: String
        },
        computed: {
            success() {
                return this.status === 'success';
            },
            fail() {
                return this.status === 'fail';
            }
        },
        methods: {
            onChange(e) {
                const file = this.$refs.file.files.item(0);

                this.$emit('change', file);
            },
            reset(e) {
                e.target.value = null;
            }
        }
    }
</script>

<style lang="scss" scoped>
    input[type=file] {
        display: none;
    }

    label {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 180px;
        height: 180px;
        border: 2px dashed #797971;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        transition: background .3s ease-out;

        &:hover {
            opacity: .85;
        }

        &.success {
            border-color: var(--green);
            background-color: rgba(40, 167, 69, .1);
            transition: none;
        }
        &.fail {
            border-color: var(--red);
            background-color: rgba(220, 53, 69, .1);
            transition: none;
        }
    }
</style>
