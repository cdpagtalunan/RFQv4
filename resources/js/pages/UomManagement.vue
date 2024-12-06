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
                    <Card :cardBody="true" :cardHeader="true">
                        <template #header>
                            <label class="h4">UOM List</label>
                            <button class="btn btn-primary float-right" @click="addUser"><icons icon="fas fa-plus"></icons>Add UOM</button>
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columnsUOM"
                                ajax="api/dt_get_uom"
                                ref="tableUOM"
                                :options="optionsUOM"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalUOMTitle" id="modalUOM" :modal-footer="true">
        <template #body>
            <input type="hidden" v-model="formUOM.id">
            <div class="row">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text w-50">UOM:</span>
                        <input type="text"  id="" class="form-control" name="uom_abbrv" v-model="formUOM.uom_abbrv">
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text w-50">Description:</span>
                        <input type="text"  id="" class="form-control" name="uom_desc" v-model="formUOM.uom_desc">
                    </div>
                </div>
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" id="btnSaveUOM" @click="saveUOM">Save</button>
        </template>
    </Modal>
</template>

<script setup>
    import { onMounted, reactive, ref, inject } from 'vue';
    import api from '../axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const Toast = inject('Toast');
    const Swal = inject('Swal');

    /**
        * UOM Modal variables 
    */
    const modalUOM = ref();
    const modalUOMTitle = ref('');

    /**
        * Table UOM Variables
    */
    let dtUOM;
    const tableUOM = ref();
    const columnsUOM = [
        { 
            data: 'action',
            title: 'Action',
            createdCell(cell){
                if(cell.querySelector('.btnEditUom')){
                    cell.querySelector('.btnEditUom').addEventListener('click', function(){
                        let data = JSON.parse(this.getAttribute('data-uoms'));
                        formUOM.id = data.id;
                        formUOM.uom_abbrv = data.uom_abbrv;
                        formUOM.uom_desc = data.uom_desc;
                        modalUOMTitle.value = "Edit UOM";
                        modalUOM.value.show();
                    })
                }

                cell.querySelector('.btnEditStatus').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status');
                    let message = '';
                    if(status == 0){
                        message = 'enable';
                    }
                    else{
                        message = 'disable';
                    }
                    Swal.fire({
                        // title: `Are you sure?`,
                        text:`Are you sure you want to ${message} this UOM?`,
                        icon: 'question',
                        position: 'top',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateStatusUOM(id, status);
                        }
                    })

                });
            }
        },
        { data: 'uom_abbrv', title: 'UOM' },
        { data: 'uom_desc', title: 'Description' },
    ];

    const optionsUOM = {
        serverSide: true,
        responsive: true,
        language: {
            lengthMenu: 'Display _MENU_ UOMS',
            entries: {
                _: 'UOMS',
                1: 'UOM'
            }
        },
        columnDefs: [
            {"className": 'dt-head-center', "targets": [0]},
            {"className": "dt-body-center", "targets": [0]},
            {"className": 'dt-head-left', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"}
        ],
    };

    /**
        * UOM Form Variable 
    */
    const formUOMIniVal = {
        id: '',
        uom_abbrv : '',
        uom_desc : ''
    };
    const formUOM = reactive({...formUOMIniVal});

    onMounted(() => {
        dtUOM = tableUOM.value.dt; // Initialize DataTable

        modalUOM.value = new Modal(document.querySelector('#modalUOM'), {});

        document.getElementById("modalUOM").addEventListener('hidden.bs.modal', event => {
            Object.assign(formUOM, formUOMIniVal); 
        })
    });

    const addUser = () => {
        modalUOM.value.show();
        modalUOMTitle.value = "Add UOM";
    }

    const saveUOM = () => {
        document.getElementById('btnSaveUOM').disabled = true;
        api.post('api/save_uom', formUOM).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                modalUOM.value.hide();
                document.getElementById('btnSaveUOM').disabled = false;
                dtUOM.ajax.reload();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong!'
                });
            }
        }).catch((err) => {
            let data = err.response.data;
            console.log(err.response);
            if(err.response.status == 422){
                handleValidatorErrors(data.errors);
                Toast.fire({
                    icon: 'error',
                    title: data.message
                });
            }
            document.getElementById('btnSaveUOM').disabled = false;
        });
    }

    const updateStatusUOM = (id, status) => {
        api.post('api/change_uom_status', {id: id, status: status}).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                dtUOM.ajax.reload();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong!'
                });
            }
        }).catch((err) => {
            console.log(err);
        });
    }
</script>