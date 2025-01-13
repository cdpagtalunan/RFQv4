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
                                            <option value="1">For Assignment</option>
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
                                        param.status = reqFilterStatus,
                                        param.type = 1
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

        <Modal title="View Request" id="viewModalRequest" modal-size="modal-xl">
            <template #body v-if="viewRequest.request != undefined">
                <input type="hidden" :value="viewRequest.request.id">
                <div class="row">
                    <div class="col-md-3">
                        <label>Control No.:</label>
                        <input type="text" class="form-control" :value="viewRequest.request.ctrl_no" readonly>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Category:</label>
                        <input type="text" class="form-control" :value="viewRequest.request.category_details.category_name " readonly>
                        
                    </div>
                    <div class="col-md-6">
                        <label>Date Needed:</label>
                        <input type="text" class="form-control" :value="viewRequest.request.date_needed " readonly>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Attachment:</label>
                        <!-- <input type="text" class="form-control" :value="viewRequest.request.attachment " readonly> -->
                        <div class="d-flex flex-column">
                            <a v-for="(attachment, index) in attachments" :key="index" :href="`download_attachments/${attachment}`" v-if="attachments.length > 0">{{ attachment }}</a>
                            <label v-else class="text-danger">No Attachment</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Send CC to:</label>
                        <input type="text" class="form-control" :value="viewRequest.request.cc " readonly>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label> Justification:</label>
                        
                        <textarea class="form-control" :value="viewRequest.request.justification " readonly></textarea>
                    </div>
                </div>
                <hr>
                <h4>Requested Item(s)</h4>
                <div class="row" v-show="viewRequest.status != 1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="tableViewApprovals">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th v-for="supplier in tableSpecialViewData.supplierNames" :key="supplier">{{ supplier }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- * For Suppliers and their quotations -->
                                    <tr v-for="item in tableSpecialViewData.itemDetails" :key="item.id">
                                        <td class="text-center"><strong>{{ item.item_name }}</strong></td>
                                        <td v-for="supplier in tableSpecialViewData.supplierNames" :key="supplier" class="p-0" v-if="item.item_quotation_details.length > 0">
                                            <span class="d-flex justify-content-center" v-html="inputFunction(supplier, item.item_quotation_details)"></span>
                                        </td>
                                        <td v-else class="text-center text-danger" :colspan="tableSpecialViewData.supplierNames.length"><strong>-- No Quotation --</strong></td>
                                    </tr>
                                    <!-- * For additional data on datatable (Durations, etc.) -->
                                    <tr v-for="additionalRow in tableSpecialViewData.additionalRows" :key="additionalRow">
                                        <td><strong>{{ additionalRow.title }}</strong></td>
                                        <td v-for="supplier in tableSpecialViewData.supplierNames" :key="supplier" class="p-0 text-center">
                                            {{ 
                                                tableSpecialViewData.uniqueOtherDetailsPerSupplier[supplier] == undefined ? '' :
                                                tableSpecialViewData.uniqueOtherDetailsPerSupplier[supplier][0][additionalRow.tblColName]
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="row" v-show="viewRequest.status > 1">
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
                </div> -->
                <div class="row" v-show="viewRequest.status == 1">
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
                        <table class="table table-sm table-bordered wrap">
                            <thead>
                                <tr>
                                    <th class="text-center" >{{ viewRequest.request == undefined ? '' : viewRequest.request.ctrl_no }}</th>
                                    <th class="text-center" :colspan="quotationDetails.length">{{ itemDetails.itemDesc }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Select Quotation</td>
                                    <td v-for="details in quotationDetails" :key="details.id" class="text-center">
                                        <input type='radio' name='radioSelect' 
                                        class="form-check-input"
                                        :value='details.id'
                                        v-model="winningQuotation"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Supplier Name</td>
                                    <td v-for="details in quotationDetails" :key="details.id" 
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.supplier_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Price</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.currency }} {{ details.price }}
                                        <div v-if="details.currency != 'PHP'">
                                            <strong>{{ details.currency }} Saved rate:</strong> {{ details.rate }} <br>
                                            <strong>{{ details.currency }} Convertion to PHP:</strong> {{ parseFloat(details.price)*parseFloat(details.rate) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Lead Time</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.lead_time }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Warranty</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.warranty }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Quotation Validity</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.quotation_validity }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Terms of Payment</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[viewRequest.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.terms_of_payment }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
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

    const reqFilterStatus = ref(1);
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
    const attachments = ref([]);

    /**
     * @variable {Array} additionalRows - This will serve as the additinal rows in #tableViewApprovals.
     * Add other data on array to reflect on table
     * @variable {String} additionalRows.title - This will be the table data along side with additionalRows.tblColName
     * @variable {string} additionalRows.tblColName - will be the name of the column form database
    */
    const tableSpecialViewData = reactive({
        rfqDetails : [],
        supplierNames: [],
        itemDetails: [],
        uniqueOtherDetailsPerSupplier : [],
        additionalRows : [
            {
                title: 'Durations',
                tblColName: 'lead_time'
            }, 
            {
                title: 'Terms',
                tblColName: 'terms_of_payment'

            }, 
            {
                title: 'Warranty',
                tblColName: 'warranty'
            }, 
        ],
    })


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
                    document.getElementById('btnServeQuotation').classList.add('d-none');
                    document.getElementById('btnSaveWinningQuotation').classList.add('d-none');
                    
                    viewRequest.request = JSON.parse(request);
                    if(viewRequest.request.attachment != null){
                        attachments.value = viewRequest.request.attachment.split(",");
                    }

                    if(viewRequest.status == 3){
                        document.getElementById('btnServeQuotation').classList.remove('d-none');
                        document.getElementById('btnSaveWinningQuotation').classList.remove('d-none');
                    }
                    if(viewRequest.status > 2){
                        getDetails(id.value);
                    }
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
                                api.post('api/disapprove_quotation', {'request_id': id}).then((result)=>{
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
        { data: 'assigned_to_details.name', title: 'Assigned To',
            render: function(data){
                let toShow = "---";
                if(data){
                    toShow = data;
                }
                return toShow;
            }
        },
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
    const quotationDetails = ref([]);
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
                    
                    api.get('api/get_quotations', {params: {id : itemDetails.itemId}}).then((result)=>{
                        // Script Here
                        quotationDetails.value = result.data;
                        quotationDetails.value.forEach(element => {
                            if(element.selected_quotation == 1){
                                winningQuotation.value = element.id;
                            }
                        });
                    }).catch((err) => {
                        console.log(err);
                    });
                    // dtQuotationSummary = tableQuotationSummary.value.dt;
                    // dtQuotationSummary.ajax.reload();
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
        drawCallback: function( data ) {
            /*
                * This script is to disable or enable the serve button.
                * disabled = true => there is a item with no selected winning quotation 
            */
            let dtDatas = data.json.data;
            document.getElementById('btnServeQuotation').disabled = false;

            for (let index = 0; index < dtDatas.length; index++) {
                const element = dtDatas[index];
                if(element['raw_sel_quotation_status'] == 0){
                    document.getElementById('btnServeQuotation').disabled = true;
                }
            }
        }
    }

    onMounted(() => {
        dtLogRequest = tableLotRequest.value.dt;
        // dtItemSupplier = tableItemSupplier.value.dt;

        // Declare Modal to be used
        modalView.value = new Modal(document.querySelector('#viewModalRequest'), {});
        modalAssign.value = new Modal(document.querySelector('#modalAssign'), {});
        modalQuotationSummary.value = new Modal(document.querySelector('#modalQuotationSummary'), {});
        
        
        document.getElementById("viewModalRequest").addEventListener('hidden.bs.modal', event => {
            assignedRequestDetails.assigned_to = '';
            const radios = document.querySelectorAll('input[type="radio"].radioSelectionWinner');
            radios.forEach(radio => radio.checked = false);
            tableSpecialViewData.rfqDetails                    = [];
            tableSpecialViewData.supplierNames                 = [];
            tableSpecialViewData.itemDetails                   = [];
            tableSpecialViewData.uniqueOtherDetailsPerSupplier = [];
            attachments.value = [];
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
    
    const serveQuotation = () => {
        document.getElementById('btnServeQuotation').disabled = true;
        /**
            * Validation
            * To prevent saving withoung selectiong winning quotation. 
        */
        let stop = false;
        // Select all radio buttons
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        // Get unique names
        const uniqueNames = Array.from(new Set(Array.from(radioButtons).map(button => button.name)));
        uniqueNames.forEach((uniqueNames) => {
            const radioGroup = document.querySelectorAll(`input[name="${uniqueNames}"]`);

            const isChecked = Array.from(radioGroup).some(radio => radio.checked);

            if (stop) return false;
            
            if (!isChecked) {
                Swal.fire({
                    title: `Invalid!`,
                    text: `Please select winning quotations.`,
                    icon: 'error',
                    position: 'center',
                })
                stop = true;
            }
        });


        let selectedRadioArray = [];
        let radioInputs = document.querySelectorAll('input[type="radio"].radioSelectionWinner:checked');
        radioInputs.forEach(radio => {
            selectedRadioArray.push(radio.value);
            // console.log(radio.value); // Access the value of each radio button
        });

        if(!stop){
            Swal.fire({
                title: `Serve request?`,
                text: `Are you sure you'd like to serve this request?`,
                icon: 'question',
                position: 'center',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.post('api/serve_quotation', {'id' : viewRequest.request.id, 'selected_winner': selectedRadioArray}).then((result)=>{
                        if(result.data.result == true){
                            dtLogRequest.ajax.reload();
                            modalView.value.hide();
                            Toast.fire({
                                icon: 'success',
                                title: result.data.msg
                            });
                        }
                        else{
                            Toast.fire({
                                icon: 'error',
                                title: 'Something went wrong.'
                            });
                        }
                    }).catch((err) => {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something went wrong.'
                        });
                        console.log(err);
                    });
                }
            })
          
        }

        document.getElementById('btnServeQuotation').disabled = false;

      
    }

    const getDetails = (id) => {
        api.get('api/get_request_details', {params: {id : id}}).then((result)=>{
            // details.value = result.data.rfqDetails;
            // suppliers.value = result.data.supplier_names;
            tableSpecialViewData.rfqDetails = result.data.rfqDetails;
            tableSpecialViewData.supplierNames = result.data.supplierNames;
            tableSpecialViewData.itemDetails = result.data.itemDetails;
            tableSpecialViewData.uniqueOtherDetailsPerSupplier = result.data.uniqueOtherDetailsPerSupplier;
            
        }).catch((err) => {
            console.log(err);
        });
    }

    const inputFunction = (supplier, itemQuotation) => {
        for (let index = 0; index < itemQuotation.length; index++) {

            const element = itemQuotation[index];
            let forAppend = '';
            let forAppendAttachment = '';
            let forSelected = '';
            if(supplier == element['supplier_name']){
                console.log('element', element);
                if(element['currency'] === null){
                    return `<strong>Decline to Quote</strong>`;
                }
                if(element['currency'] != 'PHP'){
                    forAppend = `<tr>
                            <td> Rate/${ element['currency'] }: </td>
                            <td>${ element['rate'] }</td>
                        </tr>
                        <tr >
                            <td> PHP:</td>
                            <td>${ formatNumber( element['price'] * element['rate']) }</td>
                        </tr>
                        `
                }
                if(element['selected_quotation'] == 1){
                    forSelected = `checked`
                };

                /**
                    * For attachments 
                */
                let attachments = element['attachment'].split(',')
                attachments.forEach(attachment => {
                    forAppendAttachment += `<a href='download/${attachment}'>${attachment}</a><br>`
                })
                return `
                <table class="table table-borderless table-sm w-100">
                    <thead>
                        <tr>
                            <th colspan=2 class='text-center'>
                                <div class="form-check">
                                    <input class="form-check-input radioSelectionWinner" type="radio" name='${element['request_item_id']}' value='${element['id']}' ${forSelected}>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class='w-50'>${ element['currency'] }:</td>
                            <td>${ element['price'] }</td>
                        </tr>
                        ${forAppend}
                        <tr>
                            <td colspan='2'>${forAppendAttachment}</td>
                        </tr>
                        
                    </tbody>
                </table>`;
            }
        }
    }
    const formatNumber = (value) => {
      return new Intl.NumberFormat().format(value);
    }
</script>