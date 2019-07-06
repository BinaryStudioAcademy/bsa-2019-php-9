<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Resize Photo</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 file-input-container">
                                <file-input
                                    :hasLoaded="hasLoaded"
                                    v-on:change="onFile"
                                />
                            </div>
                            <div class="col-md-8">
                                <div class="image-container">
                                    <image-container
                                        :src="images['100']"
                                        :width="100"
                                        :height="100"
                                    />
                                    <image-container
                                        :src="images['150']"
                                        :width="150"
                                        :height="150"
                                    />
                                    <image-container
                                        :src="images['250']"
                                        :width="250"
                                        :height="250"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FileInput from './FileInput';
import image from './Image';

export default {
    components: {
        'file-input': FileInput,
        'image-container': image
    },
    data() {
        return {
            hasLoaded: null,
            images: {
                '100': '',
                '150': '',
                '250': ''
            },
            timer: null
        };
    },
    methods: {
        addImage(size, url) {
            this.images[size] = url;
        },

        imageUploaded(status) {
            this.hasLoaded = status;

            if (this.timer) {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(() => {
                this.hasLoaded = null;
            }, 5000);
        },

        sendFile(url, file) {
            const fd = new FormData();

            fd.append('file', file);

            return axios.post(url, fd, {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            });
        },

        onFile(file) {
            const YOUR_API_URL = '';

            return this.sendFile(YOUR_API_URL, file)
                .then(() => {
                    this.imageUploaded(true);
                })
                .catch((error) => {
                    this.imageUploaded(false);
                    console.error(error);
                });
        }
    },
    mounted() {
        // !! Here should be subscription on notifications from server !!

        // Echo.channel(`notifications`)
        //     .listen('.message', (e) => {
        //         console.log(e.message);
        //     });
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

