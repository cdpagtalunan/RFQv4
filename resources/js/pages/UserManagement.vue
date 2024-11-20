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
                            <button class="btn btn-primary float-right" @click="onAddUser"><icons icon="fas fa-plus"></icons>Add User</button>
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_users"
                                ref="table"
                                :options="options"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>

       
    </section>
    <Modal title="Add User" backdrop="static" id="modalUser">
        <template #body>
            <div class="row">
                <div class="col-sm-12">
                    <input type="text" v-model="formUser.id" class="d-none">
                    <div class="form-group">
                        <label>Rapidx User: </label>
                        <VueMultiselect
                            v-model="formUser.rapidx_id"
                            label="name"
                            placeholder="Select one"
                            :options="rapidxUsers.map(option => option.id)"
                            :custom-label="labelValue => rapidxUsers.find(x => x.id == labelValue).name"
                            :searchable="true"
                            :allow-empty="true">
                            <template #noResult>
                                No User Found. Register User on RapidX.
                            </template>
                        </VueMultiselect>
                    </div>
                    <div class="form-group">
                    
                        <label>User Access:</label>
                        <select id="selUAccess" class="form-control" v-model="formUser.user_access">
                            <option value="0">Requestor</option>
                            <option value="1">Purchasing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>User Type: </label>
                        <select id="selUType" class="form-control" v-model="formUser.user_type">
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                            <option value="2">Super User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Approver?</label>
                        <icons icon="fas fa-circle-info" class="ml-1" data-bs-toggle="tooltip" data-bs-placement="right" title="For logistics approval of quotation" />
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" v-model="formUser.approver">
                            <label class="form-check-label" v-if="formUser.approver" for="flexSwitchCheckDefault">Yes</label>
                            <label class="form-check-label" v-else for="flexSwitchCheckDefault">No</label>
                        </div>
                    </div>
                   
                </div>
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" @click="saveUser"><icons icon="fas fa-check"></icons> Save</button>
        </template>
    </Modal>
</template>

<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const Swal = inject('Swal');

    let dt;
    const modal = ref();
    const table = ref();
    const columns = [
        { 
            data      : 'action',
            title     : 'Action',
            searchable: false,
            orderable : false,
            createdCell(cell) {
                if(cell.querySelector('.btnEditUser')){

                    cell.querySelector('.btnEditUser').addEventListener('click', function(){
                        formUser.approver = false;

                        formUser.id = this.getAttribute('data-id');
                        formUser.rapidx_id = this.getAttribute('data-rapidx-id');
                        formUser.user_access = this.getAttribute('data-u-access');
                        formUser.user_type = this.getAttribute('data-u-type');
                        if(this.getAttribute('data-u-approver') == 1){
                            formUser.approver = true;
                        }
                        modal.value.show();
                    });
                }

                cell.querySelector('.btnUpdateUser').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status')
                    let message = 'Enable';
                    if(status == 0){
                        message = 'Disable';
                    }
                    Swal.fire({
                        // title: `Are you sure?`,
                        text:`Are you sure you want to ${message} this user?`,
                        icon: 'question',
                        position: 'top',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onUpdated(id, status);
                        }
                    })
                });
            
            },
        },
        { data: 'status', title: 'Status' },
        { data: 'rapidx_details.name', title: 'User' },
        { data: 'u_type', title: 'Type' },
        { data: 'u_access', title: 'Access' }
    ];
    const options = {
        responsive: true,
        serverSide: true,
        language: {
            lengthMenu: 'Display _MENU_ users',
            entries: {
                _: 'users',
                1: 'user'
            }
        },
        columnDefs: [
            {"className": "dt-center", "targets": "_all"},
        ],
    };

    const modalTitle = ref('');

    const rapidxUsers = ref([]);

    const formUserInitialVal = {
        id         : '',
        rapidx_id  : '',
        user_access: 0,
        user_type  : 0,
        approver   : 0
    };

    const formUser = reactive({...formUserInitialVal});

    onMounted(() => {
        dt = table.value.dt;
        modal.value = new Modal(document.querySelector('#modalUser'), {});
        getRapidxUserList(document.querySelector(".selRapidxUser"));
    })

    const onAddUser = () => {
        modalTitle.value = "Add User";
        modal.value.show();
    }

    const getRapidxUserList = (cboElement) => {
        api.get('api/get_rapidx_user_list').then((result)=>{
            rapidxUsers.value = result.data;
        }).catch((err) => {
            console.log(err);
        });
    }

    const saveUser = () => {
        api.post('api/save_user', formUser ).then((result)=>{
            if(result.data.result == true){
                modal.value.hide();
                dt.ajax.reload();
                // dt.draw();
                // console.log(dt);
            }
        }).catch((err) => {
            console.log(err);
        });
    }

    const onUpdated = (id, status) => {
        api.post('api/update_status', {"id" : id, "status" : status}).then((result)=>{
            dt.ajax.reload();
        }).catch((err) => {
            console.log(err);
        });
    }
</script>