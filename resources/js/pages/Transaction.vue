<template>
    <Breadcrumbs>
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Transaction</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend w-25">
                                            <span class="input-group-text w-100">Status</span>
                                        </div>
                                        <select id="selStatus" class="form-control" v-model="reqFilterStatus" @change="dtLogRequest.draw()">
                                            <option value="1">Open</option>
                                            <option value="2">For Quotation</option>
                                            <option value="3">For Approval</option>
                                            <option value="4">Served</option>
                                            <option >All</option>
                                        </select>
                                    </div>
                                </div>
                                  
                            </div>
                            
                        </template>
                        <template #body>

                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columnsLogRequest"
                                :ajax="{
                                    url: 'api/dt_get_log_request',
                                    data: function (param){
                                        param.status = reqFilterStatus
                                    }
                                }"
                                ref="tableLotRequest"
                                :options="optionsLogRequest"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>

        <Modal title="View Request" id="viewModalRequest" modal-size="modal-lg" :modal-footer="false">
            <template #body>
            <div class="row">
                <div class="col-md-3">
                    <label>Control No.:</label>
                    <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.ctrl_no " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Category:</label>
                    <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.category_details.category_name " readonly>
                    
                </div>
                <div class="col-md-6">
                    <label>Date Needed:</label>
                    <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.date_needed " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Attachment:</label>
                    <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.attachment " readonly>
                </div>
                <div class="col-md-6">
                    <label>Send CC to:</label>
                    <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.cc " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label> Justification:</label>
                    
                    <textarea class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.justification " readonly></textarea>
                </div>
            </div>

            <hr>
            <div v-if="status == 1">
                <h5>Requested Item(s)</h5>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm">
                            <thead>

                                <tr>
                                    <th>Item/Description</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="viewRequest.request != undefined" v-for="itemDetails in viewRequest.request.item_details" :key="itemDetails.id">
                                    <td>{{ itemDetails.item_name }}</td>
                                    <td>{{ itemDetails.qty }}</td>
                                    <td>{{ itemDetails.uom }}</td>
                                    <td>{{ itemDetails.remarks }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            <div class="row" :class="status > 1 ? '' : 'd-none'">
                <div class="col-md-12">
                    <DataTable
                        class="table table-sm table-bordered table-hover wrap display"
                        :columns="columnsItemSupplier"
                        :ajax="{
                            url: 'api/dt_get_items_supplier',
                            data: function (param){
                                param.id = id
                            }
                        }"
                        ref="tableItemSupplier"
                        :options="optionsItemSupplier"
                    />
                </div>
            </div>
        </template>
        </Modal>

        <Modal title="Assign Purchasing Staff" id="modalAssign" :modal-footer="true">
            <template #body>
                <input type="hidden" v-model="assignedRequestDetails.request_id">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-prepend w-50">
                                <span class="input-group-text w-100">Control No.:</span>
                            </div>
                            <input type="text"  id="txtAssignCtrlNo" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-prepend w-50">
                                <span class="input-group-text w-100">Assign to:</span>
                            </div>
                            <select class="form-control" v-model="assignedRequestDetails.assigned_to">
                                <option disabled value=""> - Select Purchasing Staff -</option>
                                <option v-for="staff in purchaseStaff" :key="staff.id" :value="staff.rapidx_id">
                                    {{ staff.rapidx_details.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
            <template #footerButton>
                <button class="btn btn-success" id="btnAssign" @click="onAssignRequest">Assign</button>
            </template>
        </Modal>

        <Modal title="Supplier Info" id="modalAddSupplier" :modal-footer="true">
            <template #body>
                <div class="row">
                    <div class="col-sm-12">
                        
                    </div>
                </div>
            </template>
            <template #footerButton>
                <button class="btn btn-primary" id="btnSaveSupplier">Save</button>
            </template>
        </Modal>
    </section>
</template>

<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../axios';

    // Import DataTable
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const Toast = inject('Toast');
    
    const reqFilterStatus = ref(2);
    // const viewRequest = ref();
    const viewRequest = reactive({
        request : null,
        status : ''
    });
    const modalView = ref();
    const id = ref(0);
    const status = ref();
    const modalAssign = ref();
    const purchaseStaff = ref([]);
    const assignedRequestDetails = reactive({
        assigned_to : '',
        request_id : null,
    });

    // tblLogRequest variables
    let dtLogRequest;
    const tableLotRequest = ref();
    const columnsLogRequest = [
        { 
            data: 'action',
            title: 'Status',
            searchable: false,
            sortable: false,
            createdCell(cell) {
                cell.querySelector('.btnViewRequest').addEventListener('click', function(){
                    let request = this.getAttribute('data-request');
                    status.value = this.getAttribute('data-status');
                    // viewRequest.value = JSON.parse(request);
                    viewRequest.request = JSON.parse(request);
                    viewRequest.status = status;
                    modalView.value.show()
                });

                if(cell.querySelector('.btnAssignRequest')){
                    cell.querySelector('.btnAssignRequest').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        let ctrl = this.getAttribute('data-ctrl');
                        assignedRequestDetails.request_id = id;
                        document.getElementById('txtAssignCtrlNo').value = ctrl;
                        api.get('api/get_purchasing_staff').then((result)=>{
                            purchaseStaff.value = result.data;
                        }).catch((err) => {
                            console.log(err);
                        });
                        modalAssign.value.show();
                    })
                }

                if(cell.querySelector('.btnAddSupplier')){
                    cell.querySelector('.btnAddSupplier').addEventListener('click', function(){
                        let requestDetails = this.getAttribute('data-request');
                        status.value = this.getAttribute('data-status');
                        id.value = this.getAttribute('data-id')

                        viewRequest.request = JSON.parse(requestDetails);
                        dtItemSupplier.draw();
                        modalView.value.show();
                    });
                }
            },
        },
        { data: 'ctrl_no', title: 'Control No.' },
        { data: 'category_details.category_name', title: 'Category' },
        { data: 'date_needed', title: 'Date Needed' },
        { data: 'justification', title: 'Justification' },
        { data: 'created_by_details.name', title: 'Requested By' },
    ];
    const optionsLogRequest = {
        responsive: true,
        serverSide: true,
        columnDefs: [
            {"className": 'dt-head-left', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"}
        ],
        language: {
            lengthMenu: 'Display _MENU_ requests',
            entries: {
                _: 'requests',
                1: 'request'
            }
        },
    }

    // Table Variables for Item/Supplier input
    let dtItemSupplier;
    const tableItemSupplier = ref();
    const columnsItemSupplier = [
        { 
            data: 'action', 
            title: 'Action',
            createdCell(cell){
                cell.querySelector('.btnAddSupplier').addEventListener('click', function(){
                    let itemId = this.getAttribute('data-item-id');


                })
            }
        },
        { data: 'item_name', title: 'Item Name' },
        { data: 'qty', title: 'Quantity' },
        { data: 'uom', title: 'UOM' },
        { data: 'remarks', title: 'Remarks' },
    ];
    const optionsItemSupplier = {
        responsive: true,
        serverSide: true,
        searching: false,
        info: false,
        paginate: false,
        columnDefs: [
            {"className": 'dt-head-left', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"}
        ],
        language: {
            lengthMenu: 'Display _MENU_ items',
            entries: {
                _: 'items',
                1: 'item'
            }
        },
    }

    onMounted(() => {
        dtLogRequest = tableLotRequest.value.dt;
        dtItemSupplier = tableItemSupplier.value.dt;
        // Declare Modal to be used
        modalView.value = new Modal(document.querySelector('#viewModalRequest'), {});
        modalAssign.value = new Modal(document.querySelector('#modalAssign'), {});

        document.getElementById("modalAssign").addEventListener('hidden.bs.modal', event => {
            assignedRequestDetails.assigned_to = '';
        })

    });

    const getRequestDetails = (id) => {
        api.get('api/get_request_details_by_id', {params: {id : id}}).then((result)=>{
            console.log(result);
        }).catch((err) => {
        });
    }

    const onAssignRequest = () => {
        api.post('api/assign_request', assignedRequestDetails).then((result)=>{
            if(result.data.result){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                dtLogRequest.draw();
                modalAssign.value.hide();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong. <br> Please contact ISS.'
                });
            }
        }).catch((err) => {
            console.log(err);
        });
    }
</script>