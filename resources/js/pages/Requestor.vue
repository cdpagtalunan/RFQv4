<template>
    <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">RFQv4</li>
        </template>
    </Breadcrumbs>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :cardBody="true" :cardHeader="true">
                        <template #header>
                            <label class="h3">Request for Quotation</label>
                            <button class="btn btn-primary float-right" id="btnAddRequest" @click="onAddRequest"><icons icon="fas fa-plus"></icons>Add Request</button>
                        </template>
                        <template #body>
                            <div class="table-responsive">
                                <DataTable
                                    class="table table-sm table-bordered table-hover wrap display"
                                    :columns="columnsQuotationRequest"
                                    ajax="api/dt_get_request"
                                    ref="tableQuotationRequest"
                                    :options="optionsQuotationRequest"
                                />
                            </div>
                          
                        </template>
                    </Card>

                </div>
            </div>
        </div>
    </section>

    <!-- Modal For Requesting of quotation -->
    <Modal title="Request Information" id="modalRequestId" backdrop="static" modal-size="modal-xl" :modal-footer="false" >
        <template #body>
            <FormWizard 
                color="gray"
                step-size="xs"
                ref="wizard"
            >
                <TabContent title="Request Details" class="border p-2">
                    <section class="form-wizard-sample-card">
                        <input type="hidden" v-model="formRequest.id">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Control No.:</label>
                                <input type="text" class="form-control" readonly v-model="formRequest.ctrl_no" name="ctrl_no" placeholder="Auto Generated">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Category: <strong class="text-danger"> *</strong></label>
                                <VueMultiselect
                                    v-model="formRequest.category_id"
                                    label="name"
                                    placeholder="Select one"
                                    :options="category.map(option => option.id)"
                                    :custom-label="labelValue => category.find(x => x.id == labelValue).category_name"
                                    :searchable="true"
                                    :allow-empty="true"
                                    name="category_id"
                                >
                                    <template #noResult>
                                        No users found. Register a user on RapidX.
                                    </template>
                                </VueMultiselect>
                            </div>
                            <div class="col-md-6">
                                <label>Date Needed:<strong class="text-danger"> *</strong></label>
                                <input type="date" class="form-control" :min="minDate" v-model="formRequest.date_needed" name="date_needed">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Attachment:</label>
                                <input type="text" class="form-control" readonly v-model="formRequest.attachment" v-if="formRequest.checkedReupload == false && formRequest.id != ''">
                                <input type="file" class="form-control" @change="onFileChange" v-if="formRequest.checkedReupload == true" multiple>

                                <div class="form-check" v-if="formRequest.id != ''">
                                    <input class="form-check-input" type="checkbox" id="chechReupload"  v-model="formRequest.checkedReupload">
                                    <label class="form-check-label" for="chechReupload">
                                        Reupload file
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Send CC to:</label>
                                <VueMultiselect
                                    v-model="formRequest.cc"
                                    label="name"
                                    placeholder="Search name"
                                    :options="rapidxUser.map(option => option.email)"
                                    :custom-label="labelValue => rapidxUser.find(x => x.email == labelValue).name"
                                    :searchable="true"
                                    :allow-empty="true"
                                    :multiple="true"
                                >
                                    <template #noResult>
                                        No User Found. Register User on RapidX.
                                    </template>
                                </VueMultiselect>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label> Justification:<strong class="text-danger"> *</strong></label>
                                <textarea class="form-control" v-model="formRequest.justification" name="justification"></textarea>
                            </div>
                        </div>
                    </section>
                </TabContent>
                <TabContent title="Item Details" class="border p-4">
                    <section class="form-wizard-sample-card">
                        <button class="btn btn-primary btn-sm float-end" @click="onAddItem"><icons icon="fas fa-plus"></icons> Add Item</button>
                        <br>
                        <DataTable
                            class="table table-sm table-bordered table-hover wrap display"
                            :columns="columnsRequestItem"
                            :ajax="{
                                url: 'api/dt_get_request_item',
                                data: function (param){
                                    param.id = formRequest.id
                                }
                            }"
                            ref="tableRequestItem"
                            :options="optionsRequestItem"
                        />
                    </section>
                </TabContent>
                <!-- Access footer  directly with all props options -->
                <!-- You can create custom design and event -->
                <template v-slot:footer="props" >
                    <div class="wizard-footer-left mt-3">
                        <button
                        v-if="props.activeTabIndex > 0"
                        :style="props.fillButtonStyle"
                        @click.native="props.prevTab()"
                        class="wizard-button btn"
                        >
                        Back
                        </button>
                    </div>
                    <div class="wizard-footer-right mt-3">
                        <button
                            v-if="!props.isLastStep"
                            class="wizard-button btn"
                            :style="props.fillButtonStyle"
                            @click="onNext(props)"
                        >
                        Next
                        </button>

                        <button
                        v-else
                        @click.native="confirmMethod"
                        class="finish-button btn btn-success"
                        @click="onDoneRequest"
                        :disabled='btnDisabled'
                        id='btnDone'
                        >
                        {{ props.isLastStep ? "Done" : "Next" }}
                        </button>
                    </div>
                </template>
            </FormWizard>
        </template>
    </Modal>

    <!-- Modal For adding item on request -->
    <Modal title="Request Item" id="modalItemId" modalIcon="fas fa-file-circle-plus fa-sm">
        <template #body>
            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend w-50">
                    <span class="input-group-text w-100">Item/Description</span>
                </div>
                <input type="text" class="form-control" id="txtItemName" name="item_name" v-model="formItem.item_name">
            </div>

            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend w-50">
                    <span class="input-group-text w-100">Quantity</span>
                </div>
                <input type="number" class="form-control" id="txtQty" name="qty" @keypress="isNumber($event)" v-model="formItem.qty">
            </div>

            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend w-50">
                    <span class="input-group-text w-100">UOM</span>
                </div>
                <input list="uom" name="uom" id="txtUom" class="form-control" v-model="formItem.uom" autocomplete="off">

                <datalist id="uom">
                    <option v-for="uom in uoms" :key="uom.id" :value="uom.uom_abbrv">
                        {{ uom.uom_desc }}
                    </option>
                </datalist>
            </div>

            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend w-50">
                    <span class="input-group-text w-100">Remarks</span>
                </div>
                <!-- <input type="text" class="form-control" id="txtRemarks" name="remarks" v-model="formItem.remarks"> -->
                <textarea class="form-control" name="remarks" id="txtRemarks" v-model="formItem.remarks"></textarea>
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" id="btnAddItem" @click="onSaveItem"><icons icon="fas fa-plus"></icons> Add</button>
        </template>
    </Modal>

    <!-- Modal For Viewing of Request -->
     <Modal title="Request for Quotation" id="modalViewRequest" :modal-footer="false" modal-size="modal-xl">
        <template #body v-if="viewRequestData != undefined">
            
            <div class="row">
                <div class="col-md-3">
                    <label>Control No.:</label>
                    <input type="text" class="form-control" :value="viewRequestData.ctrl_no " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Category:</label>
                    <input type="text" class="form-control" :value="viewRequestData.category_details.category_name " readonly>
                    
                </div>
                <div class="col-md-6">
                    <label>Date Needed:</label>
                    <input type="text" class="form-control" :value="viewRequestData.date_needed " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Attachment:</label>
                    <!-- <input type="text" class="form-control" :value="viewRequestData.attachment " readonly> -->
                    <div class="d-flex flex-column">
                        <a v-for="(attachment, index) in attachments" :key="index" :href="`download_attachments/${encodeURIComponent(attachment)}`" v-if="attachments.length > 0">{{ attachment }}</a>
                        <label v-else class="text-danger">No Attachment</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Send CC to:</label>
                    <input type="text" class="form-control" :value="viewRequestData.cc " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label> Justification:</label>
                    
                    <textarea class="form-control" :value="viewRequestData.justification " readonly></textarea>
                </div>
            </div>

            <hr>
            <div class="row" v-show="!statusForDatatable">
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

                            <tr  v-for="itemDetails in viewRequestData.item_details" :key="itemDetails.id" v-if="viewRequestData.item_details.length > 0">
                                <td>{{ itemDetails.item_name }}</td>
                                <td>{{ itemDetails.qty }}</td>
                                <td>{{ itemDetails.uom }}</td>
                                <td>{{ itemDetails.remarks }}</td>
                            </tr>

                            <tr v-else>
                                <td colspan="6" class="text-center font-weight-bold">
                                    No Item Requested
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- <div class="row" v-show="statusForDatatable"> -->
            <div class="row" v-show="statusForDatatable">
                <div class="col-md-12">
                    <DataTable
                        class="table table-sm table-bordered table-hover wrap display"
                        :columns="columnsItemSupplier"
                        :ajax="{
                            url: 'api/dt_get_items_supplier',
                            data: function (param){
                                param.id = viewRequestData.id
                            }
                        }"
                        ref="tableItemSupplier"
                        :options="optionsItemSupplier"
                    />
                </div>
            </div>
        </template>
    </Modal>

    <Modal title="Quotation List" id="modalAddSupplier" modal-size="modal-xl" backdrop="static" :modal-footer="false">
        <template #body>
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
            <!-- For Served only -->
            <div class="row"> 
                <div class="col-md-12">
                    <table class="table table-sm table-bordered wrap">
                        <thead>
                            <tr>
                                <th class="text-center" >{{ viewRequestData == undefined ? '' : viewRequestData.ctrl_no }}</th>
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
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.supplier_name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Price</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.currency }} {{ details.price }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Duration</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.lead_time }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Warranty</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.warranty }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Quotation Validity</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.quotation_validity }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Terms of Payment</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.terms_of_payment }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Remarks</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    {{ details.remarks }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Attachments</td>
                                <td v-for="details in quotationDetails" :key="details.id"
                                :class="[viewRequestData.status == 4 && details.selected_quotation == 1 ? 'bg-success' : '']"
                                >
                                    <span v-html="getAttachment(details.attachment)"></span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
    import { 
        onMounted,
        ref,
        onBeforeMount,
        reactive,
        inject,
        watch,
        nextTick
    } from 'vue';
    import api from '../axios';

    // Import DataTable
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    // Import vue3-form-wizard
    import { FormWizard, TabContent, WizardStep } from "vue3-form-wizard";
    import "vue3-form-wizard/dist/style.css";
    
    const Swal = inject('Swal');
    const Toast = inject('Toast');
    
    const modalRequest = ref();
    const modalItem = ref();
    const modalViewRequest = ref();
    const modalSupplier = ref();
    const minDate = ref();
    const category = ref([]);
    const rapidxUser = ref([]);
    const uoms = ref([]);
    const wizard = ref();
    const btnDisabled = ref(false);
    const quotationDetails = ref([]);
    const winningQuotation = ref();
    const statusForDatatable = ref(false);
    const attachments = ref([]);

    // const formRequest.checkedReupload = ref(true);

    // Request Form Variables
    const formRequestInitVal = {
        id           : '',
        ctrl_no      : '',
        category_id  : '',
        date_needed  : null,
        // attachment   : '',
        attachment   : [],
        cc           : [],
        justification: null,
        checkedReupload : true
    };
    const formRequest = reactive({...formRequestInitVal});

    // Item form variables
    const formItemInitVal = {
        fk_quotation_requests_id: null,
        item_name               : '',
        qty                     : 0,
        uom                     : '',
        remarks                 : '',
    };
    const formItem = reactive({...formItemInitVal});

    // view request variable
    const viewRequestData = ref();

    // Item Datatable variables
    let dtRequestItem
    const tableRequestItem = ref();
    const columnsRequestItem = [
        { 
            data: 'action',
            title: 'Action',
            createdCell(cell){
                cell.querySelector('.btnRemoveReqItem').addEventListener('click', function(){
                    let requestItemId = this.getAttribute('data-id');
                    Swal.fire({
                        title: `Are you sure?`,
                        text: `This will item will be deleted.`,
                        icon: 'question',
                        position: 'top',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onRemoveItem(requestItemId);
                        }
                    })
                });
            }
        },
        { data: 'item_name', title: 'Item/Description' },
        { data: 'qty', title: 'Quantity' },
        { data: 'uom', title: 'UOM' },
        { data: 'remarks', title: 'Remarks' },
    ]
    const optionsRequestItem = {
        responsive: true,
        serverSide: true,
        searching: false,
        paginate: false,
        info: false,
        ordering: false,
        columnDefs: [
            {"className": 'dt-head-center', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"}
        ]
    }

    // Quotation request datatable variables
    const tableQuotationRequest = ref();
    let dtQuotationRequest
    const columnsQuotationRequest = [
        { 
            data: 'action', 
            title: 'Action', 
            orderable: false, 
            sortable: false,
            createdCell(cell) {
                if(cell.querySelector('.btnEditRequest')){
                    cell.querySelector('.btnEditRequest').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        formRequest.checkedReupload = false;
                        getRequestDetailsById(id);
                    });
                }
                if(cell.querySelector('.btnCancelRequest')){
                    cell.querySelector('.btnCancelRequest').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        Swal.fire({
                            // title: `Are you sure?`,
                            text: `Are you sure you want to cancel this request?`,
                            icon: 'question',
                            position: 'top',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cancelRequest(id);
                            }
                        });
                    });
                }
                
                cell.querySelector('.btnViewRequest').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    getRequestDetailsForView(id);
                });
            },
        },
        { data: 'status', title: 'Status', orderable: false, sortable: false },
        { data: 'ctrl_no', title: 'Control No.' },
        { data: 'category_details.category_name', title: 'Category' },
        { data: 'date_needed', title: 'Date Needed' },
        { data: 'created_at', title: 'Created At',
            render: function(data) {
                if (data) {
                    const date = new Date(data);
                    const formattedDate = date.toISOString().replace('T', ' ').split('.')[0];
                    return formattedDate; 
                }
            }
        },
        { data: 'assigned_to_details.name', title: 'Assigned To' },
        { data: 'assigned_at', title: 'Assigned At' },
    ]

    const optionsQuotationRequest = {
        responsive: true,
        serverSide: true,
        language: {
            lengthMenu: 'Display _MENU_ requests',
            entries: {
                _: 'requests',
                1: 'request'
            }
        },
        columnDefs: [
            {"className": 'dt-head-center', "targets": "_all"},
            {"className": "dt-body-left", "targets": "_all"}
        ]
    }

    /**
        * Datatable of served items 
    */
    const itemDetails = reactive({
        itemId: null,
        itemDesc: '',
        itemQty : 0,
        itemUom : ''
    })
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
                    // dtSupplierQuotation.ajax.reload();

                    // if(status.value >= 4){
                        api.get('api/get_quotations', {params: {id : this.getAttribute('data-item-id')}}).then((result)=>{
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
                    // }
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
        bDestroy  : true,
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
    onBeforeMount(() => {

        api.get('api/get_category').then((result)=>{
            category.value = result.data;
        }).catch((err) => {
            console.log(err);
        });

        api.get('api/get_rapidx_user_list').then((result)=>{
            rapidxUser.value = result.data;
        }).catch((err) => {
            console.log(err);
        });

        api.get('api/get_uom').then((result)=>{
            uoms.value = result.data;
        }).catch((err) => {
            console.log(err);
        });
    });
    onMounted(() => {
        minDate.value = new Date().toISOString().split('T')[0];
        dtRequestItem = tableRequestItem.value.dt;
        dtQuotationRequest = tableQuotationRequest.value.dt;
        // dtItemSupplier = tableItemSupplier.value.dt;
        // console.log('onMounted', tableItemSupplier.value)
        // Initialize Modal
        modalRequest.value = new Modal(document.querySelector('#modalRequestId'), {})
        modalItem.value = new Modal(document.querySelector('#modalItemId'), {})
        modalViewRequest.value = new Modal(document.querySelector('#modalViewRequest'), {})
        modalSupplier.value = new Modal(document.querySelector('#modalAddSupplier'), {});


        // Reset form after modal is closed
        document.getElementById("modalItemId").addEventListener('hidden.bs.modal', event => {
            Object.assign(formItem,formItemInitVal)
            const invalidElements = document.querySelectorAll('.is-invalid');
            const validElements = document.querySelectorAll('.is-valid');

            invalidElements.forEach(element => {
                element.classList.remove('is-invalid');
            });
            validElements.forEach(element => {
                element.classList.remove('is-valid');
            });
        })
        document.getElementById("modalRequestId").addEventListener('hidden.bs.modal', event => {
            Object.assign(formRequest, formRequestInitVal)
            wizard.value.reset(); // This will reset the vue3-form-wizard.
            const invalidElements = document.querySelectorAll('.is-invalid');
            const validElements = document.querySelectorAll('.is-valid');

            invalidElements.forEach(element => {
                element.classList.remove('is-invalid');
            });
            validElements.forEach(element => {
                element.classList.remove('is-valid');
            });
        })
        document.getElementById("modalViewRequest").addEventListener('hidden.bs.modal', event => {
            statusForDatatable.value = false;
        })
    });

    const onAddRequest = () => {
        formRequest.checkedReupload = true;
        modalRequest.value.show();
    }

    const onAddItem = () => {
        modalItem.value.show();
    }

    // // Accept number only on input type number
    const isNumber = (evt) => {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
            evt.preventDefault();;
        } else {
            return true;
        }
    }

    const onNext = (props) => {
        let formData = new FormData();
        Object.keys(formRequest).forEach(function(key) {
            formData.append(key, formRequest[key]);
        });
        
        // console.log(Array.isArray(formRequest.attachment));
        if(Array.isArray(formRequest.attachment)){
            if(formRequest.attachment.length != 0){
                    formRequest.attachment.forEach((file) => {
                    formData.append("attachment[]", file);
                });
            }
        }
        
        api.post('api/save_req_details', formData,  {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }).then((result)=>{
            props.nextTab()
            formRequest.id = result.data.id;
            formRequest.ctrl_no = result.data.ctrl_no;
            // formItem.fk_quotation_requests_id = formRequest.id;
            dtQuotationRequest.draw();
            dtRequestItem.draw();
        }).catch((err) => {
            let data = err.response
            if(data.status == 422){
                handleValidatorErrors(data.data.errors);
                Toast.fire({
                    icon: "error",
                    title: 'Please fill up required fields.'
                });
            }
        });
    }
    
    const onSaveItem = () => {
        document.getElementById('btnAddItem').disabled = true;
        formItem.fk_quotation_requests_id = formRequest.id;
        api.post('api/save_item', formItem).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: "success",
                    title: result.data.msg
                });
                dtRequestItem.draw();
                modalItem.value.hide();
            }
            else{
                Toast.fire({
                    icon: "error",
                    title: 'Something went wrong. <br> Please contact ISS.'
                })
            }
            document.getElementById('btnAddItem').disabled = false;

        }).catch((err) => {
            console.log(err);
            let data = err.response.data;
            if(err.response.status == 422){
                Toast.fire({
                    icon: "error",
                    title: 'Please fill required fields!'
                })
                handleValidatorErrors(data.errors);
            }
            document.getElementById('btnAddItem').disabled = false;
        });
    }

    const onDoneRequest = () => {
        btnDisabled.value = true;
        Swal.fire({
            title: `Are you sure?`,
            text: `RFQ will proceed on purchasing assignment.`,
            icon: 'question',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                api.post('api/done_request', {id: formRequest.id}).then((result)=>{
                    if(result.data.result == true){
                        Toast.fire({
                            icon: "success",
                            title: result.data.msg
                        });
                        modalRequest.value.hide();
                        dtQuotationRequest.draw();
                    }
                    else{
                        Toast.fire({
                            icon: "error",
                            title: "Something went wrong. <br> Please contact ISS."
                        })
                    }
                    btnDisabled.value = false;

                }).catch((err) => {
                    console.log(err);
                    btnDisabled.value = false;

                });
            }
            else{
                btnDisabled.value = false;
            }
        })
    }

    const getRequestDetailsById = (id) => {
        api.get('api/get_request_details_by_id', {params: {id : id}}).then((result)=>{
            let data = result.data;
            formRequest.id = data.id;
            formRequest.category_id = data.category_id;
            formRequest.ctrl_no = data.ctrl_no;
            formRequest.date_needed = data.date_needed;
            formRequest.justification = data.justification
            if(data.attachment != null){
                formRequest.attachment = data.attachment.split('||')
            }
            // formRequest.attachment = data.attachment

            console.log(formRequest.attachment);
            if(data.cc != null){
                let splittedData = data.cc.split(",");
                formRequest.cc = splittedData;
            }
            modalRequest.value.show();
        }).catch((err) => {
            console.log(err);
        });
    }

    const getRequestDetailsForView = (id) => {

        api.get('api/get_request_details_by_id', {params: {id : id}}).then((result)=>{

            viewRequestData.value = result.data;
            if(viewRequestData.value.attachment != null){
                attachments.value = viewRequestData.value.attachment.split("||");
            }
            statusForDatatable.value = false;
            if(result.data.status == 4 || result.data.status == 3){
                statusForDatatable.value = true;
                // console.log(tableItemSupplier.value);
                // dtItemSupplier.ajax.reload();
                // dtItemSupplier = tableItemSupplier.value.dt;

            }

            modalViewRequest.value.show();

        }).catch((err) => {
            console.log(err);
        });
    }

    const onFileChange = (event) => {
        // formRequest.attachment = event.target.files[0];
        formRequest.attachment = Array.from(event.target.files);
        console.log(formRequest.attachment);
    }

    const onRemoveItem = (requestItemId) => {
        api.post('api/remove_item', {id: requestItemId}).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon : 'success',
                    title: result.data.msg
                });
                dtRequestItem.draw();
            }
        }).catch((err) => {
            console.log(err);
        });
    }

    const getAttachment = (attachment) => {
        let result = '<label class="text-danger">No Attachment</label>';

        if(attachment != null){
            result = "";
            let splittedAttachment = attachment.split('||');
            splittedAttachment.forEach(attchmnt => {
                result += `<a href='download/${encodeURIComponent(attchmnt)}'>${attchmnt}</a><br>`;
            })
        }
        return result;
      
    }

    const cancelRequest = (requestId) => {
        api.post('api/cancel_request', {id: requestId}).then((result)=>{
            if(result.data.result == true){
                Toast.fire({
                    icon: "success",
                    title: result.data.msg
                });
                dtQuotationRequest.draw();
            }
        }).catch((err) => {
            console.log(err);
        });
    }
    watch(statusForDatatable, async (newValue) => {
        console.log('new', newValue);
        if (newValue) {
            // Wait until DOM updates
            await nextTick();
            dtItemSupplier = tableItemSupplier.value.dt;
            dtItemSupplier.draw();
        }
    });
</script>