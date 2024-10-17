
<template>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <!-- <i class="fas fa-bars"></i> -->
                    <icons icon="fas fa-bars" class="nav-icon"></icons>
                    
                    <!-- <font-awesome-icon :icon="['fas', 'bars']" class="nav-icon"></font-awesome-icon> -->
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="" class="nav-link">RFQv4</a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item">
                <a class="nav-link" style="cursor: pointer;">
                    <icons icon="fas fa-user" class="nav-icon"></icons>
                    {{ username }}
                </a>
            </li>
           
        </ul>


    </nav>
</template>
<script setup>
    defineProps({
        username: {type: String, default: ''}
    })
</script>
