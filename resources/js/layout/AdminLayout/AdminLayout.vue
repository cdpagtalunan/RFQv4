<template>
    <div class="wrapper">
        <Sidebar :uType="localStore.uType" :uAccess="localStore.uAccess" :uApprove="localStore.isApprover"></Sidebar>
        <Header :username="localStore.name"></Header>
        <div class="content-wrapper" style="height: auto !important;">
            <router-view />
        </div>
        <Footer></Footer>
    </div>
</template>
<script setup>
    import { onBeforeMount, ref, provide, onMounted } from 'vue';
    import Sidebar from '../../template/Sidebar.vue';
    import Header from '../../template/Header.vue';
    import Footer from '../../template/Footer.vue';
    import { useSessionStore } from '../../store/';
    const localStore = useSessionStore();
</script>