<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary" style="height: 100%;">
        <!-- Brand Logo -->
        <a href="" class="brand-link text-center text-decoration-none">
            <!-- <img src="../assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
            <span class="brand-text font-weight-light">RFQv4</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center">
                <div class="info text-center">
                    <h6 class="d-block text-white text-capitalize"></h6>
                </div>
            </div> -->
            <!-- Sidebar Menu -->
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item has-treeview">
                        <a href="../RapidX/" class="nav-link">
                            <icons icon="fas fa-arrow-left" class="nav-icon"></icons>
                            <p>Go to Rapidx</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview"  v-if="uAccess == 1 || uType != 0">
                        <router-link :to="{ name: 'Dashboard' }" class="nav-link">
                            <icons icon="fas fa-gauge" class="nav-icon"></icons>
                            <p>Dashboard</p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Request' }" class="nav-link">
                            <icons icon="fas fa-file-invoice-dollar" class="nav-icon"></icons>
                            <p>Request Quotation</p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview" v-if="uAccess == 1 || uType != 0">
                        <router-link :to="{ name: 'LogisticsPurchasing' }" class="nav-link">
                            <icons icon="fas fa-clipboard-user" class="nav-icon"></icons>
                            <p>Purchasing</p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview" v-if="(uAccess == 1 || uType != 0) && uApprove == 1">
                        <router-link :to="{ name: 'LogisticsHead' }" class="nav-link">
                            <icons icon="fas fa-clipboard-check" class="nav-icon"></icons>
                            <p>Logistics Head</p>
                        </router-link>
                    </li>

                    <li class="nav-header font-weight-bold" v-if="uType != 0">Administrator</li>
                    <li class="nav-item has-treeview" v-if="uType != 0">
                        <router-link :to="{ name: 'UserManagement' }" class="nav-link">
                            <icons icon="fas fa-user" class="nav-icon"></icons>
                            <p>User Management</p>
                        </router-link>
                    </li>
                    <li class="nav-header font-weight-bold" v-if="uType != 0">Settings</li>

                    <li class="nav-item has-treeview" v-if="uType != 0">
                        <router-link :to="{ name: 'CategoryManagement' }" class="nav-link">
                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            <p>Category </p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview" v-if="uType != 0">
                        <router-link :to="{ name: 'CurrencyManagement' }" class="nav-link">
                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            <p>Currency </p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview" v-if="uType != 0">
                        <router-link :to="{ name: 'UomManagement' }" class="nav-link">
                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            <p>UOM </p>
                        </router-link>
                    </li>
                    <li class="nav-header font-weight-bold">Exports</li>
                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Export' }" class="nav-link">
                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            <p>Export </p>
                        </router-link>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    uType: {type: Number, default: 0},
    uAccess: {type: Number, default: 0},
    uApprove: {type: Number, default: 0}
})
</script>
