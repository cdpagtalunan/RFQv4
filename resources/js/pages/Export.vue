<template>
    <Breadcrumbs>
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">UOM Management</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :cardBody="true">
                        <template #body>
                            <div class="row justify-content-center">
                                <div class="col-sm-6">
                                    <form @submit.prevent="exportItem">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text w-50">Date</span>
                                            <input type="month" class="form-control" v-model="formData.date">
                                        </div>

                                        <div>
                                            <button type="submit" class="btn btn-success w-100">Export</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
    import { ref, reactive } from 'vue';
    import api from '../axios';

    const formData = reactive({
        date : ''
    });

    const exportItem = () => {
        window.open(`api/export/${formData.date}`, '_blank');
    }
</script>