<template>
    <Breadcrumbs >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Requests</li>
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
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columnsQuotationRequest"
                                ajax="api/dt_get_request"
                                ref="tableQuotationRequest"
                                :options="optionsQuotationRequest"
                            />
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
                                        No User Found. Register User on RapidX.
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
                                <input type="file" class="form-control" @change="onFileChange" v-if="formRequest.checkedReupload == true">

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
                <input list="uom" name="uom" id="txtUom" class="form-control" v-model="formItem.uom">

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
                <input type="text" class="form-control" id="txtItemName" name="item_name" v-model="formItem.remarks">
            </div>
        </template>
        <template #footerButton>
            <button class="btn btn-success" id="btnAddItem" @click="onSaveItem"><icons icon="fas fa-plus"></icons> Add</button>
        </template>
    </Modal>

    <!-- Modal For Viewing of Request -->
     <Modal title="Request for Quotation" id="modalViewRequest" :modal-footer="false" modal-size="modal-xl">
        <template #body>
            
            <div class="row">
                <div class="col-md-3">
                    <label>Control No.:</label>
                    <input type="text" class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.ctrl_no " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Category:</label>
                    <input type="text" class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.category_details.category_name " readonly>
                    
                </div>
                <div class="col-md-6">
                    <label>Date Needed:</label>
                    <input type="text" class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.date_needed " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Attachment:</label>
                    <input type="text" class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.attachment " readonly>
                </div>
                <div class="col-md-6">
                    <label>Send CC to:</label>
                    <input type="text" class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.cc " readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label> Justification:</label>
                    
                    <textarea class="form-control" :value="viewRequestData == undefined ? '' : viewRequestData.justification " readonly></textarea>
                </div>
            </div>

            <hr>

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
                            <tr v-if="viewRequestData != undefined" v-for="itemDetails in viewRequestData.item_details" :key="itemDetails.id">
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
     </Modal>
</template>

<script setup>
    import { 
        onMounted,
        ref,
        onBeforeMount,
        reactive,
        inject
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
    const minDate = ref();
    const category = ref([]);
    const rapidxUser = ref([]);
    const uoms = ref([]);
    const wizard = ref();
    const btnDisabled = ref(false);
    // const formRequest.checkedReupload = ref(true);

    // Request Form Variables
    const formRequestInitVal = {
        id           : '',
        ctrl_no      : '',
        category_id  : '',
        date_needed  : null,
        attachment   : '',
        // attachments  : null,
        cc           : [],
        justification: null,
        checkedReupload : true
    };
    const formRequest = reactive({...formRequestInitVal});

    // Request Table Variable
    const tableRequestItem = ref();
    let dtRequestItem

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
    const columnsRequestItem = [
        // { data: 'id', title: 'Items' },
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
        // console.log(viewRequestData.value);
        minDate.value = new Date().toISOString().split('T')[0];
        dtRequestItem = tableRequestItem.value.dt;
        dtQuotationRequest = tableQuotationRequest.value.dt;

        // Initialize Modal
        modalRequest.value = new Modal(document.querySelector('#modalRequestId'), {})
        modalItem.value = new Modal(document.querySelector('#modalItemId'), {})
        modalViewRequest.value = new Modal(document.querySelector('#modalViewRequest'), {})

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
    });

    const onAddRequest = () => {
        // api.get('api/generate_control_no').then((result)=>{
        //     formRequest.ctrl_no = result.data;
        // }).catch((err) => {
        //     console.log(err);
        // });
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

        api.post('api/save_req_details', formData).then((result)=>{
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
        }).catch((err) => {
            console.log(err);
        });
    }

    const onDoneRequest = () => {
        btnDisabled.value = true;
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

    const getRequestDetailsById = (id) => {
        api.get('api/get_request_details_by_id', {params: {id : id}}).then((result)=>{
            let data = result.data;
            formRequest.id = data.id;
            formRequest.category_id = data.category_id;
            formRequest.ctrl_no = data.ctrl_no;
            formRequest.date_needed = data.date_needed;
            formRequest.justification = data.justification
            formRequest.attachment = data.attachment

            let splittedData = data.cc.split(",");
            formRequest.cc = splittedData;
            modalRequest.value.show();
        }).catch((err) => {
            console.log(err);
        });
    }

    const getRequestDetailsForView = (id) => {
        api.get('api/get_request_details_by_id', {params: {id : id}}).then((result)=>{
            viewRequestData.value = result.data;
            modalViewRequest.value.show();

        }).catch((err) => {
            console.log(err);
        });
    }

    const onFileChange = (event) => {
        formRequest.attachment = event.target.files[0];
    }

</script>