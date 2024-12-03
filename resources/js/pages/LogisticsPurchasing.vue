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
                                            <!-- <option value="1">Open</option> -->
                                            <option value="2">For Quotation</option>
                                            <!-- <option value="3">For Approval</option> -->
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

        <Modal title="View Request" id="viewModalRequest" modal-size="modal-lg" :modal-footer="viewRequest.modalFooter">
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
                <h5>Requested Item(s)</h5>
                <div class="row">
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
                <button class="btn btn-success" id="btnProceedApproval" @click="saveForApproval">Save</button>
            </template>
        </Modal>

        <Modal title="Quotation List" id="modalAddSupplier" style-size="min-width: 1400px !important;" backdrop="static" :modal-footer="false">
            <template #body>
                <input type="hidden" v-model="itemDetails.itemId">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Item</label>
                            <input type="text" class="form-control" readonly v-model="itemDetails.itemDesc">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="text" class="form-control" readonly v-model="itemDetails.itemQty">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>UOM</label>
                            <input type="text" class="form-control" readonly v-model="itemDetails.itemUom">
                        </div>
                    </div>
                </div>
                <hr>
                <h4>Supplier List</h4>
                <div class="row">
                    <div class="col-sm-12 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm" title="Add Supplier Quotation" @click="modalAddSupplierDetails.show()" v-if="viewRequest.status == 1">Add Supplier Quotation</button>
                    </div>
                </div>
                <div class="row" v-show="status <= 3">
                    <div class="col-sm-12 table-responsive">
                        <DataTable
                            class="table table-sm table-bordered table-hover wrap display"
                            :columns="columnsSupplierQuotation"
                            :ajax="{
                                url: 'api/dt_get_supplier_quotation',
                                data: function (param){
                                    param.item_id = itemDetails.itemId
                                }
                            }"
                            ref="tableSupplierQuotation"
                            :options="optionsSupplierQuotation"
                        />
                    </div>
                </div>
                <!-- For Served only -->
                <div class="row" v-show="status >= 4"> 
                    <div class="col-md-12">
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
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.supplier_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Price</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.currency }} {{ details.price }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Lead Time</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.lead_time }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Warranty</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.warranty }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Quotation Validity</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.quotation_validity }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Terms of Payment</td>
                                    <td v-for="details in quotationDetails" :key="details.id"
                                    :class="[status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                    >
                                        {{ details.terms_of_payment }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </Modal>

        <Modal title="Supplier Info" :modal-footer="true" modal-size="modal-lg" id="modalAddSupplierDetails" :overlay="true">
            <template #body>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-25">Supplier:</span>
                            <input type="text"  id="txtSupplier" name="supplier_name" v-model="formSupplierDetails.supplier_name" class="form-control" list="supplierList" autocomplete="off">
                            <datalist id="supplierList">
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.supplier_name"></option>
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Price:</span>
                            <select class="form-control w-25"  v-model="formSupplierDetails.currency" @change="checkCurrency">
                                <option v-for="currency in currencies" :key="currency.id" :value="currency.currency" :data-rate="currency.rate">{{ currency.currency }}</option>
                            </select>
                            <input type="number" name="price" class="form-control w-25" v-model="formSupplierDetails.price" @keyup="convertCurrencyToPhp">
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-if="showRate">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">{{ formSupplierDetails.currency }} Rate to PHP:</span>
                            <input type="text"  id="" class="form-control" name="rate" v-model="formSupplierDetails.rate" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-if="showRate">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Price Convertion: <icons icon="fas fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="right" title="Product of price and rate"></icons></span>
                            <input type="text"  id="" class="form-control" name="convertion" v-model="formSupplierDetails.convertion" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">MOQ:</span>
                            <input type="text"  id="" class="form-control" name="moq" v-model="formSupplierDetails.moq">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Warranty/Guarantee:</span>
                            <input type="text"  id="" class="form-control" name="warranty" v-model="formSupplierDetails.warranty">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Lead Time:</span>
                            <input type="text"  id="" class="form-control" name="lead_time" v-model="formSupplierDetails.lead_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Date Served:</span>
                            <input type="text"  id="" class="form-control" name="date_served"v-model="formSupplierDetails.date_served">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Quotation Validity:</span>
                            <input type="text"  id="" class="form-control" name="quotation_validity" v-model="formSupplierDetails.quotation_validity">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Terms of Payment:</span>
                            <input type="text"  id="" class="form-control" name="terms_of_payment" v-model="formSupplierDetails.terms_of_payment">
                        </div>
                    </div>
                    
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Attachment:</span>
                            <input type="file"  id="fileId" class="form-control" @change="onFileChange" v-if="!formSupplierDetails.id || checkReupload == true">
                            <input type="input"  class="form-control" v-model="formSupplierDetails.attachment" readonly v-else>
                        </div>
                        <div class="form-check" v-if="formSupplierDetails.id">
                            <input class="form-check-input" type="checkbox" id="reupload" v-model="checkReupload">
                            <label class="form-check-label" for="reupload">
                                Reupload Attachment
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-25">Remarks:</span>
                             <textarea class="form-control" name="remarks" v-model="formSupplierDetails.remarks"></textarea>
                        </div>
                    </div>
                </div>
            </template>
            <template #footerButton>
                <button class="btn btn-success" id="btnSaveSupplier" @click="btnSaveQuotationDetails">Save</button>
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
import { icon } from '@fortawesome/fontawesome-svg-core';
    DataTable.use(DataTablesCore);

    const Toast = inject('Toast');
    const Swal = inject('Swal');

    const reqFilterStatus = ref(2);
    const viewRequest = reactive({
        request: null,
        status : '',
        modalFooter: false,
    });
    const modalView = ref();
    const id = ref(0);
    const checkReupload = ref(false);
    const status = ref();
    const modalSupplier = ref();
    const modalAddSupplierDetails = ref();
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
    const suppliers = ref([]);
    const currencies = ref([]);
    const formSupplierDetailsInitVal = {
        id                : '',
        supplier_name     : null,
        currency          : 'PHP',
        price             : null,
        moq               : null,
        warranty          : null,
        lead_time         : null,
        date_served       : null,
        quotation_validity: null,
        terms_of_payment  : null,
        remarks           : null,
        attachment        : null,
        rate              : '',
        convertion        : null
    }

    const formSupplierDetails = reactive({...formSupplierDetailsInitVal});
    const quotationDetails = ref([]);
    const winningQuotation = ref();
    const showRate = ref(false);

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
                    console.log(status.value);
                    id.value = this.getAttribute('data-id');
                    
                    viewRequest.request = JSON.parse(request);
                    viewRequest.status = 0;

                    dtSupplierQuotation.column(0).visible(false); // Remove action button for viewing purposes
                    dtItemSupplier.draw();
                    modalView.value.show()
                });

                if(cell.querySelector('.btnAddSupplier')){
                    cell.querySelector('.btnAddSupplier').addEventListener('click', function(){
                        let requestDetails = this.getAttribute('data-request');
                        status.value = this.getAttribute('data-status');
                        id.value = this.getAttribute('data-id')

                        viewRequest.request = JSON.parse(requestDetails);
                        viewRequest.modalFooter = true;
                        viewRequest.status = 1;
                        dtSupplierQuotation.column(0).visible(true);

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
        autoWidth: false,
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
                cell.querySelector('.btnAddQuotation').addEventListener('click', function(){
                    // formSupplierDetails.request_item_id = this.getAttribute('data-item-id');
                    itemDetails.itemId = this.getAttribute('data-item-id');
                    itemDetails.itemDesc = this.getAttribute('data-item-name');
                    itemDetails.itemQty = this.getAttribute('data-item-qty');
                    itemDetails.itemUom = this.getAttribute('data-item-uom');
                    dtSupplierQuotation.ajax.reload();

                    if(status.value >= 4){
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
                    }
                    modalSupplier.value.show();
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
        autoWidth: false,
        columnDefs: [
            {"className": 'dt-head-left', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"},
            // { "className": "bg-info text-dark", "targets": [ 1 ] }
        ],
    }

    // Table variables for item supplier quotation
    let dtSupplierQuotation;
    const tableSupplierQuotation = ref();
    const columnsSupplierQuotation = [
        { 
            data: 'action', 
            title: 'Action',
            searchable: false,
            sortable: false,
            createdCell(cell){
                cell.querySelector('.btnEditQuotation').addEventListener('click', function(){
                    let supplierQuotationId = this.getAttribute('data-id');
                    let supplierQuotation = this.getAttribute('data-quotation');
                    let parsedData = JSON.parse(supplierQuotation);
                    formSupplierDetails.id                 = parsedData.id
                    formSupplierDetails.supplier_name      = parsedData.supplier_name
                    formSupplierDetails.currency           = parsedData.currency
                    formSupplierDetails.price              = parsedData.price
                    formSupplierDetails.rate               = parsedData.rate
                    formSupplierDetails.moq                = parsedData.moq
                    formSupplierDetails.warranty           = parsedData.warranty
                    formSupplierDetails.lead_time          = parsedData.lead_time
                    formSupplierDetails.date_served        = parsedData.date_served
                    formSupplierDetails.quotation_validity = parsedData.quotation_validity
                    formSupplierDetails.terms_of_payment   = parsedData.terms_of_payment
                    formSupplierDetails.remarks            = parsedData.remarks
                    formSupplierDetails.attachment         = parsedData.attachment
                    modalAddSupplierDetails.value.show();

                    if(parsedData.currency != 'PHP'){
                        showRate.value = true;
                        convertCurrencyToPhp();
                    }
                })

                cell.querySelector('.btnDeleteQuotation').addEventListener('click', function(){
                    let supplierQuotationId = this.getAttribute('data-id');
                    deleteQuotation(supplierQuotationId);
                })
            }
        },
        { data: 'supplier_name', title: 'Supplier Name'},
        { data: 'currency', title: 'Currency' },
        { data: 'price', title: 'Price' },
        { data: 'rate', title: 'Saved Rate' },
        { data: 'moq', title: 'MOQ' },
        { data: 'warranty', title: 'Warranty' },
        { data: 'lead_time', title: 'Lead Time' },
        { data: 'date_served', title: 'Date Served' },
        { data: 'quotation_validity', title: 'Quotation Validity' },
        { data: 'terms_of_payment', title: 'Terms of Payment' },
        { data: 'attachment_link', title: 'Attachment' },
        { data: 'remarks', title: 'Remarks' },
    ]
    const optionsSupplierQuotation = {
        searching: false,
        info: false,
        paginate: false,
        autoWidth: false,
    }

    onMounted(() => {
        dtLogRequest = tableLotRequest.value.dt;
        dtItemSupplier = tableItemSupplier.value.dt;
        dtSupplierQuotation = tableSupplierQuotation.value.dt;

        // Declare Modal to be used
        modalView.value = new Modal(document.querySelector('#viewModalRequest'), {});
        modalSupplier.value = new Modal(document.querySelector('#modalAddSupplier'), {});
        modalAddSupplierDetails.value = new Modal(document.querySelector('#modalAddSupplierDetails'), {});
        
        document.getElementById("viewModalRequest").addEventListener('hidden.bs.modal', event => {
            assignedRequestDetails.assigned_to = '';
            viewRequest.modalFooter = false;

        })
        document.getElementById("modalAddSupplierDetails").addEventListener('hidden.bs.modal', event => {
            Object.assign(formSupplierDetails, formSupplierDetailsInitVal);
            document.querySelector('#fileId').value = '';
            checkReupload.value = false;
            showRate.value = false;

        })
        // Getting suppliers
        getSupplier();
        // Getting currency
        api.get('api/get_currency').then((result)=>{
            currencies.value = result.data;
        }).catch((err) => {
            console.log(err);
        });
    });

    // For uploading file in supplier details
    const onFileChange = (event) => {
        formSupplierDetails.attachment = event.target.files[0];
    }

    const btnSaveQuotationDetails = () => {
        document.getElementById('btnSaveSupplier').setAttribute('disabled', 'true');
        let formData = new FormData();
        
        Object.keys(formSupplierDetails).forEach(function(key) {
            formData.append(key, formSupplierDetails[key]);
        });

        formData.append('request_item_id', itemDetails.itemId);
    
        api.post('api/save_quotation', formData).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                modalAddSupplierDetails.value.hide();
                dtSupplierQuotation.ajax.reload();
                dtItemSupplier.ajax.reload();
                getSupplier();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong!'
                });
            }
            document.getElementById('btnSaveSupplier').removeAttribute('disabled');

        }).catch((err) => {
            console.log(err);
            Toast.fire({
                icon: 'error',
                title: 'Something went wrong. Please call ISS'
            });
            document.getElementById('btnSaveSupplier').removeAttribute('disabled');

        });
    }

    const getSupplier = () => {
        api.get('api/get_supplier').then((result)=>{
            suppliers.value = result.data;
        }).catch((err) => {
            console.log(err);
        });
    }

    const deleteQuotation = (id) => {
        api.post('api/delete_quotation', {id: id}).then((result)=>{
            if(result.data.result){
                Toast.fire({
                    icon: 'success',
                    title: result.data.msg
                });
                dtSupplierQuotation.ajax.reload();
                dtItemSupplier.ajax.reload();
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong.'
                })
            }
        }).catch((err) => {
            console.log(err);
            Toast.fire({
                icon: 'error',
                title: 'Something went wrong.'
            })
        });
    }

    const saveForApproval = () => {
        Swal.fire({
            title: `Are you sure?`,
            text: `This will proceed on Logistics Manager approval`,
            icon: 'question',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                api.post('api/proceed_approval', {id: viewRequest.request.id}).then((result)=>{
                    dtLogRequest.draw();
                    modalView.value.hide();
                    Toast.fire({
                        icon: 'success',
                        title: result.data.msg
                    });
                }).catch((err) => {
                    console.log(err);
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong. Please call ISS'
                    });
                });
            }
        })
    }

    const checkCurrency = (event) => {
        const selectedOption = event.target.selectedOptions[0];
        showRate.value = false;
        formSupplierDetails.rate = null

        if(selectedOption.value != 'PHP'){
            showRate.value = true;
            formSupplierDetails.rate = selectedOption.getAttribute('data-rate')
        }
    }

    const convertCurrencyToPhp = () => {
        let convertedRate = 0;
        if(showRate.value == true && formSupplierDetails.price != ''){
            convertedRate = parseFloat(formSupplierDetails.price) * parseFloat(formSupplierDetails.rate);
        }
        formSupplierDetails.convertion = convertedRate;
    }
</script>