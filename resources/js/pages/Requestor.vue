<template>
    <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">User Management</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :cardBody="true" :cardHeader="true">
                        <template #header>
                            <label class="h3">User</label>
                            <button class="btn btn-primary float-right"><icons icon="fas fa-plus"></icons>Add User</button>
                        </template>
                        <template #body>
                            <!-- <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_users"
                                ref="table"
                                :options="options"
                            /> -->
                        </template>
                    </Card>
                </div>
            </div>
        </div>

       
    </section>
    <Modal  backdrop="static" >
        <template #body>
            
        </template>
        <template #footerButton>
            <button class="btn btn-success" @click="saveUser"><icons icon="fas fa-check"></icons> Save</button>
        </template>
    </Modal>
</template>

<script setup>
    import { ref } from 'vue';
</script>