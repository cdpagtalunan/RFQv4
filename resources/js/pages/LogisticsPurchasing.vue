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
                            <div class="table-responsive">
                                <DataTable
                                    class="table table-sm table-bordered table-hover wrap display"
                                    :columns="columnsLogRequest"
                                    :ajax="{
                                        url: 'api/dt_get_log_request',
                                        data: function (param){
                                            param.status = reqFilterStatus,
                                            param.type = 0
                                        }
                                    }"
                                    ref="tableLotRequest"
                                    :options="optionsLogRequest"
                                />
                            </div>
                           
                        </template>
                    </Card>
                </div>
            </div>
        </div>

        <Modal title="View Request" id="viewModalRequest" :modal-size="status == 4 ? 'modal-xl' : 'modal-lg'" :modal-footer="viewRequest.modalFooter">
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
                        <!-- <input type="text" class="form-control" :value="viewRequest.request == undefined ? '' : viewRequest.request.attachment " readonly> -->
                        <div class="d-flex flex-column">
                            <a v-for="(attachment, index) in attachments" :key="index" :href="`download_attachments/${encodeURIComponent(attachment)}`" v-if="attachments.length > 0">{{ attachment }}</a>
                            <label v-else class="text-danger">No Attachment</label>
                        </div>
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
                <div class="row" v-show="status < 4">
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
                <div class="row" v-show="status == 4">
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
                                    <tr v-for="item in tableSpecialViewData.itemDetails" :key="item.id">
                                        <td class="text-center"><strong>{{ item.item_name }}</strong></td>
                                        <td v-for="supplier in tableSpecialViewData.supplierNames" :key="supplier" class="p-0">
                                            <span class="d-flex justify-content-center" v-html="inputFunction(supplier, item.item_quotation_details)"></span>
                                        </td>
                                    </tr>
                                    <tr v-for="additionalRow in tableSpecialViewData.additionalRows" :key="additionalRow">
                                        <td><strong>{{ additionalRow.title }}</strong></td>
                                        <td v-for="supplier in tableSpecialViewData.supplierNames" :key="supplier" class="p-0 text-center">
                                            {{ tableSpecialViewData.uniqueOtherDetailsPerSupplier[supplier][0][additionalRow.tblColName] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </template>
            <template #footerButton>
                <button class="btn btn-success" id="btnProceedApproval" @click="saveForApproval">Save</button>
            </template>
        </Modal>
        <!-- style-size="min-width: 1400px !important;" -->
        <Modal title="Quotation List" id="modalAddSupplier" modal-size="modal-xl" backdrop="static" :modal-footer="false">
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
                            <label>Supplier:</label>
                            <VueMultiselect
                                v-model="formSupplierDetails.supplier_name"
                                label="name"
                                placeholder="Select one"
                                :options="suppliers.map(option => option.supplier_name)"
                                :custom-label="labelValue => suppliers.find(x => x.supplier_name == labelValue).supplier_name"
                                :searchable="true"
                                :allow-empty="true">
                                <template #noResult>
                                    No Supplier Found. Register Supplier on EPRPO.
                                </template>
                            </VueMultiselect>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="switchNoQuotation" v-model="formSupplierDetails.no_quote">
                            <label class="form-check-label" for="switchNoQuotation">Decline to Quote</label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="switchNoQuotationThisTime" v-model="formSupplierDetails.no_quote_this_time">
                            <label class="form-check-label" for="switchNoQuotationThisTime">Still no quote as of this time</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Price:</span>
                            <select class="form-control w-25"  v-model="formSupplierDetails.currency" @change="checkCurrency" :disabled="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                                <option v-for="currency in currencies" :key="currency.id" :value="currency.currency" :data-rate="currency.rate">{{ currency.currency }}</option>
                            </select>
                            <input type="number" name="price" class="form-control w-25" v-model="formSupplierDetails.price" @keyup="convertCurrencyToPhp" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-if="showRate">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">{{ formSupplierDetails.currency }} Rate to PHP:</span>
                            <input type="text"  id="" class="form-control" name="rate" v-model="formSupplierDetails.rate" readonly :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-if="showRate">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Price Convertion: <icons icon="fas fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="right" title="Product of price and rate"></icons></span>
                            <input type="text"  id="" class="form-control" name="convertion" v-model="formSupplierDetails.convertion" readonly :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">MOQ:</span>
                            <input type="text"  id="" class="form-control" name="moq" v-model="formSupplierDetails.moq" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Warranty/Guarantee:</span>
                            <input type="text"  id="" class="form-control" name="warranty" v-model="formSupplierDetails.warranty" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Lead Time:</span>
                            <input type="text"  id="" class="form-control" name="lead_time" v-model="formSupplierDetails.lead_time" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Date Served:</span>
                            <input type="text"  id="" class="form-control" name="date_served"v-model="formSupplierDetails.date_served" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Quotation Validity:</span>
                            <input type="text"  id="" class="form-control" name="quotation_validity" v-model="formSupplierDetails.quotation_validity" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Terms of Payment:</span>
                            <input type="text"  id="" class="form-control" name="terms_of_payment" v-model="formSupplierDetails.terms_of_payment" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                        </div>
                    </div>
                    
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text w-50">Attachment:</span>
                            <input type="file"  id="fileId" class="form-control" @change="onFileChange" multiple v-if="!formSupplierDetails.id || formSupplierDetails.checkReupload == true" :disabled="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time">
                            <input type="input"  class="form-control" v-model="formSupplierDetails.attachment" readonly v-else>
                        </div>
                        <div class="form-check" v-if="formSupplierDetails.id">
                            <input class="form-check-input" type="checkbox" id="reupload" v-model="formSupplierDetails.checkReupload">
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
                             <textarea class="form-control" name="remarks" v-model="formSupplierDetails.remarks" :readonly="formSupplierDetails.no_quote || formSupplierDetails.no_quote_this_time"></textarea>
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
    // const checkReupload = ref(false);
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
        supplier_name     : '',
        currency          : 'PHP',
        price             : '',
        moq               : '',
        warranty          : '',
        lead_time         : '',
        date_served       : '',
        quotation_validity: '',
        terms_of_payment  : '',
        remarks           : '',
        attachment        : [],
        rate              : '',
        convertion        : '',
        no_quote          : false,
        no_quote_this_time: false,
        checkReupload     : false
    }

    const formSupplierDetails = reactive({...formSupplierDetailsInitVal});
    const quotationDetails = ref([]);
    const winningQuotation = ref();
    const showRate = ref(false);
    const shouldHaveDrawCallback = ref(false);
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
            title: 'Action',
            searchable: false,
            sortable: false,
            createdCell(cell) {
                cell.querySelector('.btnViewRequest').addEventListener('click', function(){
                    let request = this.getAttribute('data-request');
                    status.value = this.getAttribute('data-status');
                    id.value = this.getAttribute('data-id');
                    
                    viewRequest.request = JSON.parse(request);
                    viewRequest.status = 0;

                    if(viewRequest.request.attachment != null){
                        attachments.value = viewRequest.request.attachment.split(",");
                    }

                    dtSupplierQuotation.column(0).visible(false); // Remove action button for viewing purposes
                    if(status.value < 4){
                        dtItemSupplier.draw();
                    }
                    else{
                        api.get('api/get_request_details', {params: {id :  this.getAttribute('data-id')}}).then((result)=>{
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

                    modalView.value.show()
                    shouldHaveDrawCallback.value = false;

                });

                if(cell.querySelector('.btnAddSupplier')){
                    cell.querySelector('.btnAddSupplier').addEventListener('click', function(){
                        let requestDetails = this.getAttribute('data-request');
                        status.value = this.getAttribute('data-status');
                        id.value = this.getAttribute('data-id')

                        viewRequest.request = JSON.parse(requestDetails);
                        if(viewRequest.request.attachment != null){
                            attachments.value = viewRequest.request.attachment.split(",");
                        }
                        viewRequest.modalFooter = true;
                        viewRequest.status = 1;
                        dtSupplierQuotation.column(0).visible(true);

                        dtItemSupplier.draw();
                        modalView.value.show();
                        shouldHaveDrawCallback.value = true;

                        
                    });
                }

                if(cell.querySelector('.btnCancelRFQ')){
                    cell.querySelector('.btnCancelRFQ').addEventListener('click',async function(){
                        let requestId = this.getAttribute('data-id');
                        console.log(requestId);
                        const {value: text}  = await Swal.fire({
                            input: "textarea",
                            inputLabel: "Remarks",
                            inputPlaceholder: "Type your remarks here...",
                            inputAttributes: {
                                "aria-label": "Type your remarks here"
                            },
                            showCancelButton: true
                        });

                        if (text) {
                            api.post('api/log_cancel_request', {id: requestId, remarks: text}).then((result)=>{
                                if(result.data.result == true){
                                    Toast.fire({
                                        icon: 'success',
                                        title: result.data.msg
                                    });
                                    dtLogRequest.ajax.reload();
                                }
                                else{
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'Something went wrong!'
                                    });
                                }
                            }).catch((err) => {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Something went wrong!'
                                });
                                console.log(err);
                            });
                        }
                        else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Cancelled',
                                text: 'You need to input a remarks to proceed'
                            });
                        }
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
            render: function(data) {
                let toShow = "---";
                if (data) {
                    toShow = data; 
                }
                return toShow;
            }
        },
        { data: 'assigned_by_details.name', title: 'Assigned By' },
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
        // drawCallback: function( data ) {
        //     /*
        //         * This script is to disable or enable the serve button.
        //         * disabled = true => there is a item with no selected winning quotation 
        //     */
        //     let dtDatas = data.json.data;
        //     console.log(dtDatas);
        //     if(shouldHaveDrawCallback.value){
        //         if(dtDatas.length != 0){
        //             document.getElementById('btnProceedApproval').disabled = false;
        //             for (let index = 0; index < dtDatas.length; index++) {
        //                 const data = dtDatas[index];
        //                 if(data['item_quotation_details'].length == 0){
        //                     document.getElementById('btnProceedApproval').disabled = true;
        //                 }
        //             }
        //         }
        //     }
        // }
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
                    console.log(parsedData);
                    formSupplierDetails.id                 = parsedData.id
                    formSupplierDetails.supplier_name      = parsedData.supplier_name
                    formSupplierDetails.currency           = parsedData.currency
                    formSupplierDetails.price              = parsedData.price
                    if(parsedData.rate != null){
                        formSupplierDetails.rate               = parsedData.rate
                    }
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
            tableSpecialViewData.rfqDetails                    = [];
            tableSpecialViewData.supplierNames                 = [];
            tableSpecialViewData.itemDetails                   = [];
            tableSpecialViewData.uniqueOtherDetailsPerSupplier = [];
            attachments.value = [];
        })
        document.getElementById("modalAddSupplierDetails").addEventListener('hidden.bs.modal', event => {
            Object.assign(formSupplierDetails, formSupplierDetailsInitVal);
            document.querySelector('#fileId').value = [];
            formSupplierDetails.checkReupload = false;
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
        // formSupplierDetails.attachment = event.target.files[0];
        formSupplierDetails.attachment = Array.from(event.target.files);

    }

    const btnSaveQuotationDetails = () => {
        document.getElementById('btnSaveSupplier').setAttribute('disabled', 'true');
        let formData = new FormData();
        
        Object.keys(formSupplierDetails).forEach(function(key) {
            formData.append(key, formSupplierDetails[key]);
        });

        formData.append('request_item_id', itemDetails.itemId);
        
        if(Array.isArray(formSupplierDetails.attachment)){
            formSupplierDetails.attachment.forEach((file) => {
                formData.append("attachment[]", file);
            });
        }
        

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
            if(err.response.status == 422){
                Toast.fire({
                    icon: 'error',
                    title: 'Please fill up required fields!'
                });
            }
            else{
                Toast.fire({
                    icon: 'error',
                    title: 'Something went wrong. Please call ISS'
                });
            }
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
        document.getElementById('btnProceedApproval').disabled = true;
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
                    document.getElementById('btnProceedApproval').disabled = false;
                }).catch((err) => {
                    console.log(err);
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong. Please call ISS'
                    });
                    document.getElementById('btnProceedApproval').disabled = false;
                });
            }
            else{
                document.getElementById('btnProceedApproval').disabled = false;

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

    const inputFunction = (supplier, itemQuotation) => {
        for (let index = 0; index < itemQuotation.length; index++) {

            const element = itemQuotation[index];
            let forAppend = '';
            let forAppendAttachment = '';
            let forSelected = '';
            if(supplier == element['supplier_name']){
                if(element['status'] == 1){
                    return `<strong>Decline to Quote</strong>`;
                }
                else if(element['status'] == 2){
                    return `<strong>Still no quote as of this time</strong>`;
                }
                if(element['currency'] != 'PHP'){
                    forAppend = `<tr>
                            <td> Rate/${ element['currency'] }: </td>
                            <td>${ formatNumber(element['rate']) }</td>
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
                    forAppendAttachment += `<a href='download/${encodeURIComponent(attachment)}'>${attachment}</a><br>`
                })
                return `
                <table class="table table-borderless table-sm w-50">
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
                            <td class='w-50'>Remarks: </td>
                            <td>${element['remarks'] == null ? 'N/A' : element['remarks']}</td>
                        </tr>
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