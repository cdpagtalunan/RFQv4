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
                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Request' }" class="nav-link">
                            <icons icon="fas fa-file-invoice-dollar" class="nav-icon"></icons>
                            <p>Request Quotation</p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Transaction' }" class="nav-link">
                            <icons icon="fas fa-file-invoice-dollar" class="nav-icon"></icons>
                            <p>Transaction</p>
                        </router-link>
                    </li>

                    <li class="nav-header font-weight-bold">Administrator</li>
                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'UserManagement' }" class="nav-link">
                            <icons icon="fas fa-user" class="nav-icon"></icons>
                            <p>User Management</p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <div class="d-flex justify-content-between align-items-center">
                                <p><icons icon="fas fa-cogs"></icons> Settings </p>
                                <i class="fas fa-angle-down"> </i>
                            </div>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ">
                                <router-link :to="{ name: 'CategoryManagement' }" class="nav-link">
                                    <icons icon="far fa-circle" class="nav-icon"></icons>
                                    <p>Category Management</p>
                                </router-link>
                            </li>
                        </ul>
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
    uType: {type: Number, default: 0}
})
</script>
