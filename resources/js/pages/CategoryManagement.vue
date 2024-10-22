<template>
     <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Category Management</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :cardBody="true" :cardHeader="true">
                        <template #header>
                            <!-- <label class="h3">User</label> -->
                            <button class="btn btn-primary float-right" @click="onAddCategory"><icons icon="fas fa-plus"></icons> Add Category</button>
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_category"
                                ref="table"
                                :options="options"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>

    <Modal :title="modalTitle" backdrop="static" >
        <template #body>
            <input type="hidden" v-model="formCategory.id">
            <div class="form-group">
                <label>Category Name:</label>
                <input type="" class="form-control" v-model="formCategory.category_name" id="txtCatNames" name="category_name">
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" id="btnSaveCategory" @click="onSaveCategory">Save</button>
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
    let dt;
    const table = ref();
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                if(cell.querySelector('.btnEditCat')){
                    cell.querySelector('.btnEditCat').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        let categoryName = this.getAttribute('data-cat-name')
                        formCategory.id = id;
                        formCategory.category_name = categoryName;

                        modalTitle.value = "Edit Category";
                        modal.value.show();
                    });
                }

                cell.querySelector('.btnChangeStat').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status');
                    let message = 'Enable';
                    if(status == 0){
                        message = 'Disable';
                    }
                    Swal.fire({
                        text:`Are you sure you want to ${message} this category?`,
                        icon: 'question',
                        position: 'top',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateStatus(id, status);
                        }
                    })
                });
            
            },
        },
        { data: 'status', title: 'Status' },
        { data: 'category_name', title: 'Category Name' }
        
    ];
    const options = {
        responsive: true,
        serverSide: true,
        language: {
            lengthMenu: 'Display _MENU_ categories',
            entries: {
                _: 'categories',
                1: 'category'
            }
        },
        columnDefs: [
            {"className": "dt-center", "targets": "_all"},
        ],
    };

    const modal = ref();
    const modalTitle = ref();

    const formCategoryInitVal = {
        id           : null,
        category_name: ""
    }

    const formCategory = reactive({...formCategoryInitVal});
    onMounted(() => {
        dt = table.value.dt;
        modal.value = new Modal(document.querySelector('#modalComponentId'), {});
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            Object.assign(formCategory, formCategoryInitVal);
            const invalidElements = document.querySelectorAll('.is-invalid');
            const validElements = document.querySelectorAll('.is-valid');

            invalidElements.forEach(element => {
                element.classList.remove('is-invalid');
            });
            validElements.forEach(element => {
                element.classList.remove('is-valid');
            });
        });
    })

    const onAddCategory = () => {
        modalTitle.value = "Add Category";
        modal.value.show();

    }

    const onSaveCategory = () => {
        
        api.post('api/save_category', formCategory).then((result)=>{
            let res = result.data;
            if(res.result == true){
                Toast.fire({
                    icon: "success",
                    title: res.msg
                });
                dt.ajax.reload();
                modal.value.hide();
            }
            else{
                Toast.error('Something went wrong. <br> Please call ISS.')
                console.log(res);
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

    const updateStatus = (id, status) => {
        api.post('api/update_status', {'id' : id, 'status': status}).then((result)=>{
            let res = result.data;
            if(res.result == true){
                Toast.fire({
                    icon: "success",
                    title: res.msg
                });
                dt.ajax.reload();
            }
            else{
                Toast.fire({
                    icon: "error",
                    title: res.msg
                })
            }
        }).catch((err) => {
            Toast.fire({
                icon: "error",
                title: 'Error'
            })
            console.log(err);
        });
    }
</script>