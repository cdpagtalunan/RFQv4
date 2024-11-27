<template>
    <Breadcrumbs>
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">RFQv4</li>
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
                                            <!-- <option value="2">For Quotation</option> -->
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

        <Modal title="View Request" id="viewModalRequest" modal-size="modal-lg">
            <template #body>
                <input type="hidden" :value="viewRequest.request == undefined ? '' : viewRequest.request.id">
                <div class="row">
                    <div class="col-md-3">
                        <label>Control No.:</label>
                        <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.ctrl_no" readonly>
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
                <h4>Requested Item(s)</h4>
                <div class="row" v-show="viewRequest.status > 1">
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
            <template #footerButton>
                <button class="btn btn-success" title="Serve Quotation" id="btnServeQuotation" @click="serveQuotation">Serve</button>
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

        <Modal title="Quotation Summary" id="modalQuotationSummary" modal-size="modal-xl" :modal-footer="true">
            <template #body>
                <div class="row">
                    <div class="col-sm-12">

                    </div>
                </div>
                <div class="table-responsive">
                    <DataTable
                        class="table table-sm table-bordered table-hover wrap display"
                        :columns="columnsQuotationSummary"
                        :ajax="{
                            url: 'api/dt_get_quotation_summary',
                            data: function (param){
                                param.req_id = itemDetails.itemId
                            }
                        }"
                        ref="tableQuotationSummary"
                        :options="optionsQuotationSummary"
                    />
                </div>
            </template>
            <template #footerButton>
                <button class="btn btn-success" id="btnSaveWinningQuotation" @click="saveWinningQuotation" title="Select Winning Supplier">Select</button>
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
    const Swal = inject('Swal');

    const reqFilterStatus = ref(3);
    const viewRequest = reactive({
        request: null,
        status : '',
    });
    const id = ref(0);
    const modalView = ref();
    const modalAssign = ref();
    const modalQuotationSummary = ref();
    const purchaseStaff = ref([]);
    const assignedRequestDetails = reactive({
        assigned_to: '',
        request_id : null,
    });
    const itemDetails = reactive({
        itemId: null,
        itemDesc: '',
        itemQty : 0,
        itemUom : ''
    })
    const winningQuotation = ref(null);

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
                    id.value = this.getAttribute('data-id');
                    viewRequest.status =  this.getAttribute('data-status');

                    viewRequest.request = JSON.parse(request);
                    modalView.value.show()
                    if(viewRequest.status > 1){

                        dtItemSupplier.draw();
                    }
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
                        // status.value = this.getAttribute('data-status');
                        id.value = this.getAttribute('data-id')

                        viewRequest.request = JSON.parse(requestDetails);
                        modalView.value.show();
                        
                    });
                }

                if(cell.querySelector('.btnDisapproveQuot')){
                    cell.querySelector('.btnDisapproveQuot').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        Swal.fire({
                            title: `Are you sure?`,
                            text: `This will go back to assigned logistics purchasing.`,
                            icon: 'question',
                            position: 'top',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                api.post('api/disapprove_quotation', {'status' : 2, 'request_id': id}).then((result)=>{
                                    if(result.data.result == true){
                                        dtLogRequest.ajax.reload();
                                    }
                                }).catch((err) => {
                                    console.log(err);
                                });
                            }
                        })
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

    let dtItemSupplier;
    const tableItemSupplier = ref();
    const columnsItemSupplier = [
        { 
            data: 'action', 
            title: 'Action',
            createdCell(cell){
                cell.querySelector('.btnAddQuotation').addEventListener('click', function(){
                    // formSupplierDetails.request_item_id = this.getAttribute('data-item-id');
                    itemDetails.itemId = this.getAttribute('data-item-id');
                    itemDetails.itemDesc = this.getAttribute('data-item-name');
                    itemDetails.itemQty = this.getAttribute('data-item-qty');
                    itemDetails.itemUom = this.getAttribute('data-item-uom');
                    modalQuotationSummary.value.show();

                    dtQuotationSummary = tableQuotationSummary.value.dt;
                    dtQuotationSummary.ajax.reload();
                })
            }
        },
        { data:  'no_of_quotation', title: 'No. of Quotation'},
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
            {"className": "dt-body-left", "targets": "_all"},
            // { "className": "bg-info text-dark", "targets": [ 1 ] }
        ],
    }

    // Quotation Summary Table Variable
    let dtQuotationSummary;
    const tableQuotationSummary = ref();
    const columnsQuotationSummary = [
        { 
            data: 'action',
            title: 'Price',
            createdCell(cell){
                // This is to set value if logistics already selected winning quotation
                if(cell.querySelector('input[name="radioSelect"]').getAttribute('data-selected') == 1){
                    cell.querySelector('input[name="radioSelect"]').checked = true;
                    winningQuotation.value = cell.querySelector('input[name="radioSelect"]').value;
                }
                cell.querySelector('input[name="radioSelect"]').addEventListener('change', function(){
                    winningQuotation.value = this.value;
                });
            }
        },
        { data: 'supplier_name', title: 'Supplier Name' },
        // { data: 'raw_price', title: 'Price' },
        { data: 'lead_time', title: 'Lead Time' },
        { data: 'warranty', title: 'Warranty/Guarantee' },
        { data: 'quotation_validity', title: 'Quotation Validity' },
        { data: 'terms_of_payment', title: 'Terms of Payment' }
    ]
    const optionsQuotationSummary = {
        responsive: true,
        serverSide: true,
        searching: false,
        info: false,
        paginate: false,
        ordering: false,
        autoWidth: false,
        columnDefs: [
            {"className": 'dt-head-left', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"},
            // { "className": "bg-info text-dark", "targets": [ 1 ] }
        ],
        
    }
    
    onMounted(() => {
        dtLogRequest = tableLotRequest.value.dt;
        dtItemSupplier = tableItemSupplier.value.dt;

        // Declare Modal to be used
        modalView.value = new Modal(document.querySelector('#viewModalRequest'), {});
        modalAssign.value = new Modal(document.querySelector('#modalAssign'), {});
        modalQuotationSummary.value = new Modal(document.querySelector('#modalQuotationSummary'), {});
        
        
        document.getElementById("viewModalRequest").addEventListener('hidden.bs.modal', event => {
            assignedRequestDetails.assigned_to = '';
            // viewRequest.modalFooter = false;

        })
        document.getElementById("modalAssign").addEventListener('hidden.bs.modal', event => {
            assignedRequestDetails.assigned_to = '';
        })
        document.getElementById("modalQuotationSummary").addEventListener('hidden.bs.modal', event => {
            winningQuotation.value = null;
        })
       
    });

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
            Toast.fire({
            icon: 'error',
                title: 'Something went wrong. Please call ISS'
            });
            console.log(err);
        });
    }

    const saveWinningQuotation = () => {
        if(winningQuotation.value == null){
            Toast.fire({
                icon: 'error',
                title: 'Please select winning supplier quotation.'
            });
            return;
        }
        api.post('api/select_winning_quotation', {'req_item_quot' : winningQuotation.value, 'req_item_id': itemDetails.itemId}).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                modalQuotationSummary.value.hide();
                dtItemSupplier.ajax.reload();
            }
        }).catch((err) => {
            Toast.fire({
                icon: 'error',
                title: 'Something went wrong!'
            });
            console.log(err);
        });
    }
    
</script>