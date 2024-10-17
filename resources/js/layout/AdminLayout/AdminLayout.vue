<template>
    <div class="wrapper">

        <Sidebar></Sidebar>

        <Header username="test"></Header>
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
</script>