<template>
    <div>
        <div class="card" :class="{ disabled: isNotAuthenticated }">
            <div class="card-header"><slot name="title"></slot> <a href="#" class="btn btn-secondary logout" v-on:click="logout">Log out</a></div>

            <div class="card-body">
                <slot></slot>
            </div>
        </div>
        <auth-modal
            :show="isNotAuthenticated"
            v-on:hide="onAuth"
        />
    </div>
</template>
<script>
import authService from '../../services/authService';
import Modal from './Modal';

export default {
    components: {
        'auth-modal': Modal
    },
    data() {
        return {
            isAuthenticated: false
        };
    },
    computed: {
        isNotAuthenticated() {
            return !this.isAuthenticated;
        }
    },
    methods: {
        logout() {
            this.isAuthenticated = false;
            authService.logout();
            this.$emit('logout');
        },
        onAuth() {
            this.isAuthenticated = true;

            this.$emit('auth', authService.getUser());
        }
    },
    mounted() {
        try {
            const user = authService.getUser();

            this.$emit('auth', user);
            this.isAuthenticated = true;
        } catch (e) {
            this.isAuthenticated = false;
        }
    }
}
</script>

<style lang="scss" scoped>
    .logout {
        float: right;
    }
    .disabled {
        opacity: .5;
    }
</style>
