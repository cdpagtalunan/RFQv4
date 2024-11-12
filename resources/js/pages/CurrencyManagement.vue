<template>
    <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Currency Settings</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :cardBody="true" :cardHeader="true">
                        <template #header>
                            <label class="h4">Currency List</label>
                            <button class="btn btn-primary float-right" @click="addCurrency"><icons icon="fas fa-plus"></icons> Add Currency</button>
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/dt_get_currency"
                                ref="table"
                                :options="options"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>

    <Modal :title="modalTitle" id="modalCurrency" backdrop="static">
        <template #body>
            <input type="hidden" v-model="formCurrency.id">
            <div class="form-group">
                <label>Currency:</label>
                <input type="" class="form-control" v-model="formCurrency.currency" id="txtCurrency" name="currency">
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" id="btnSaveCurrency" @click="saveCurrency">Save</button>
        </template>
    </Modal>

</template>

<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';

    DataTable.use(DataTablesCore);

    const Toast = inject('Toast');
    const Swal = inject('Swal');
    const modalTitle = ref('');
    const modal = ref();
    // Form Currency Variables'
    const formCurrencyIniVal = {
        id: null,
        currency: ''
    }
    const formCurrency = reactive({...formCurrencyIniVal});

    // Currency Table Variables
    let dt;
    const table = ref();
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                
                cell.querySelector('.btnEditStatus').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status');

                    updateCurrencyStatus(id, status);
                });
                if(cell.querySelector('.btnEditCurrency')){
                    cell.querySelector('.btnEditCurrency').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let currency = this.getAttribute('data-currency');

                    modalTitle.value = "Edit Currency"
                    formCurrency.id = id;
                    formCurrency.currency = currency;
                    modal.value.show();
                });
                }
            
            },
        },
        { data: 'status', title: 'Status' },
        { data: 'currency', title: 'Currency' }
   ];
    const options = {
        responsive: true,
        serverSide: true,
        language: {
            lengthMenu: 'Display _MENU_ currencies',
            entries: {
                _: 'currencies',
                1: 'currency'
            }
        },
        columnDefs: [
            {"className": "dt-center", "targets": "_all"},
        ],
    };


    onMounted(() => {
        modal.value = new Modal(document.querySelector('#modalCurrency'), {});
        document.getElementById("modalCurrency").addEventListener('hidden.bs.modal', event => {
            Object.assign(formCurrency, formCurrencyIniVal);
        });

        dt = table.value.dt;
    })

    const addCurrency = () => {
        modalTitle.value = "Add Currency";
        modal.value.show();
    }

    const saveCurrency = () => {
        api.post('api/save_currency', formCurrency).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                modal.value.hide();
                dt.draw();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: "Something went wrong.<br> Please contact ISS."
                })
            }
        }).catch((err) => {
            let data = err.response.data;
            if(err.response.status == 422){
                Toast.fire({
                    icon: "error",
                    title: 'Please fill required fields!'
                })
                handleValidatorErrors(data.errors);
            }
        });
    }

    const updateCurrencyStatus = (id, status) => {
        api.post('api/update_status', {id: id, status: status}).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                modal.value.hide();
                dt.draw();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: "Something went wrong.<br> Please contact ISS."
                })
            }
        }).catch((err) => {
            Toast.fire({
                icon: 'error',
                title: "Something went wrong.<br> Please contact ISS."
            })
        });
    }
</script>