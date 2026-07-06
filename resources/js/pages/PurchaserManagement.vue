<template>
    <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Purchaser Management</li>
        </template>
    </Breadcrumbs>

    <section class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center">
                <div class="col-6">
                    <Card :cardBody="true">
                        <template #body>
                            <div class="form-group">
                                <label>Old Purchaser:</label>
                                <!-- <input type="text" class="form-control" v-model="formEditPurchaser.oldPurchaser" id="txtPurchaser" name="old_purchaser"> -->
                                <VueMultiselect
                                    v-model="formEditPurchaser.oldPurchaser"
                                    label="name"
                                    placeholder="Select one"
                                    class=""
                                    :options="listOfPurchaser.map(option => option.rapidx_id)"
                                    :custom-label="labelValue => listOfPurchaser.find(x => x.rapidx_id == labelValue).rapidx_details.name"
                                    :searchable="true"
                                    :allow-empty="true">
                                    <template #noResult>
                                        No User Found. Register User on RapidX.
                                    </template>
                                </VueMultiselect>
                            </div>
                            <div class="form-group">
                                <label>New Purchaser:</label>
                                <!-- <input type="email" class="form-control" v-model="formEditPurchaser.newPurchaser" id="newPurchaser" name="new_purchaser"> -->
                                <VueMultiselect
                                    v-model="formEditPurchaser.newPurchaser"
                                    label="name"
                                    placeholder="Select one"
                                    class=""
                                    :options="listOfPurchaser.map(option => option.rapidx_id)"
                                    :custom-label="labelValue => listOfPurchaser.find(x => x.rapidx_id == labelValue).rapidx_details.name"
                                    :searchable="true"
                                    :allow-empty="true">
                                    <template #noResult>
                                        No User Found. Register User on RapidX.
                                    </template>
                                </VueMultiselect>
                            </div>
                            <div class="form-group">
                                <label>Control Number(s):</label>
                                <VueMultiselect
                                    v-model="formEditPurchaser.quotation_ids"
                                    label="name"
                                    placeholder="Select one"
                                    :options="listOfForQuotations.map(option => option.id)"
                                    :custom-label="labelValue => listOfForQuotations.find(x => x.id == labelValue).ctrl_no"
                                    :searchable="true"
                                    :multiple="true"
                                    :allow-empty="true">
                                    <template #noResult>
                                        No User Found. Register User on RapidX.
                                    </template>
                                    <template #noOptions>
                                        Please select an old purchaser first to see the list of quotations.
                                    </template>
                                </VueMultiselect>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-primary w-100" @click="updatePurchaser">Update Purchaser</button>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted, reactive, inject, watch } from 'vue';
    import api from '../axios';
    const Swal = inject('Swal');


    const formEditPurchaser = reactive({
        oldPurchaser: '',
        newPurchaser: '',
        quotation_ids: []
    });

    const listOfForQuotations = ref([]);
    const listOfPurchaser = ref([]);

    onMounted(async () => {
        await getListOfPurchaser();
    });

    watch(
        () => [formEditPurchaser.oldPurchaser, formEditPurchaser.newPurchaser],
        async ([oldVal, newVal]) => {
            // 1. Handle data fetching for oldPurchaser
            if (oldVal) {
                await getListOfForQuotations(oldVal);
            } else {
                listOfForQuotations.value = [];
            }

            // 2. Handle cross-field validation
            if (newVal && newVal === oldVal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    titleText: 'Invalid Selection',
                    text: 'New Purchaser cannot be the same as Old Purchaser.',
                });
                formEditPurchaser.newPurchaser = '';
            }
        }
    );

    const getListOfPurchaser = () => {
        api.get('/api/get_list_of_purchaser')
        .then((response) => {
            listOfPurchaser.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
    }

    const getListOfForQuotations = (oldPurchaser) => {
        api.get(`/api/get_list_of_for_quotations`, {
            params: {
                oldPurchaser: oldPurchaser
            }
        })
        .then((response) => {
            listOfForQuotations.value = [
                {"id": "all", "ctrl_no": "All"}
            ];
            listOfForQuotations.value = listOfForQuotations.value.concat(response.data);
        })
        .catch((error) => {
            console.error(error);
        });
    }

    const updatePurchaser = () => {

        if(!formEditPurchaser.oldPurchaser || !formEditPurchaser.newPurchaser || formEditPurchaser.quotation_ids.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                titleText: 'Missing Fields',
                text: 'Please fill in all required fields.',
            });
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to update the purchaser for the selected quotations.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the purchaser.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                api.post('/api/update_purchaser', formEditPurchaser)
                .then((response) => {
                    Swal.close();
                    if (response.data.result) {
                        Swal.fire(
                            'Updated!',
                            response.data.msg,
                            'success'
                        );
                        // Reset form
                        formEditPurchaser.oldPurchaser = '';
                        formEditPurchaser.newPurchaser = '';
                        formEditPurchaser.quotation_ids = [];
                        listOfForQuotations.value = [];
                    } else {
                        Swal.fire(
                            'Error!',
                            response.data.msg,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    Swal.close();
                    console.error(error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while updating the purchaser.',
                        'error'
                    );
                });
            }
        });
    }
</script>