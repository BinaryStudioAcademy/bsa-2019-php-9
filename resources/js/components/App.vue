<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <auth v-on:auth="onAuth">
                    <template v-slot:title>Resize Photo </template>
                    <div class="row">
                        <div class="col-md-4 file-input-container">
                            <file-input
                                :status="status"
                                v-on:change="onFile"
                            />
                        </div>
                        <div class="col-md-8">
                            <div class="image-container">
                                <image-container
                                    :src="images['100']"
                                    :width="100"
                                    :height="100"
                                    :status="status"
                                />
                                <image-container
                                    :src="images['150']"
                                    :width="150"
                                    :height="150"
                                    :status="status"
                                />
                                <image-container
                                    :src="images['250']"
                                    :width="250"
                                    :height="250"
                                    :status="status"
                                />
                            </div>
                        </div>
                    </div>
                </auth>
            </div>
        </div>
    </div>
</template>

<script>
import Auth from './Auth/Auth'
import FileInput from './FileInput';
import ImageContainer from './Image';
import requestService from '../services/requestService';
import echoService from '../services/echoService';

export default {
    components: {
        'file-input': FileInput,
        'image-container': ImageContainer,
        'auth': Auth
    },
    data() {
        return {
            status: '',
            images: {
                '100': '',
                '150': '',
                '250': ''
            }
        };
    },
    methods: {
        addImage(size, url) {
            this.images[size] = url;
        },

        resetImages() {
            this.addImage(100, '');
            this.addImage(150, '');
            this.addImage(250, '');
        },

        success() {
            this.status = 'success';
        },

        fail() {
            this.status = 'fail';
        },

        processing() {
            this.status = 'processing';
            this.resetImages();
        },

        onFile(file) {
            // !! HERE SENDING FILE !!
        },

        onAuth(user) {
            /* !! HERE NOTIFICATION SUBSCRIPTION !! */
            const Echo = echoService.getEchoInstance(user);
        }
    }
}
</script>

<style lang="scss" scoped>
    .image-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .file-input-container {
        display: flex;
        align-items: center;
    }
</style>

