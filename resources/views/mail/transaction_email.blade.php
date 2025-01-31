@php
    $additionalRows = array(
        [
            'title' => 'Durations',
            'tblColName' => 'lead_time',
        ],
        [
            'title' => 'Terms',
            'tblColName' => 'terms_of_payment',
        ],
        [
            'title' => 'Warranty',
            'tblColName' => 'warranty',
        ],
    );
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<style>
    #tableAdd td, th{
        border:1px solid black;
    }
    #tableItems td, th{
        border:1px solid black;
    }
    #tableQuotation td{
        text-align: center;
    }
    table {
        /* width: 100%; */
        border-collapse: collapse;
    }
</style>
<body>
    <div class="row">
        <div class="col-sm-12">
            Good Day!
            <br>
            {{-- Please be informed that RFQ is for purchasing assignment. --}}
            {{ $body }}
            {{-- {{ $quote_data }} --}}
            <br>
            <hr>
            <strong><i>Request for Quotation Details:</i></strong>
            <table style="width: 80%;">
                <tbody>
                    <tr>
                        <td><strong>Control Number</strong></td>
                        <td>:{{ $data['ctrl_no'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Category</strong></td>
                        <td>:{{ $data['category_details']['category_name'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date Needed</strong></td>
                        <td>:{{ $data['date_needed'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Justification</strong></td>
                        <td>:{{ $data['justification'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Requestor</strong></td>
                        <td>:{{ $data['created_by_details']['name'] }}</td>
                    </tr>
                    @if($data['status'] > 3)
                    <tr>
                        <td><strong>Approver Remarks:</strong></td>
                        <td>:{{ $data['approver_remarks']}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <br>
            @if ($data['status'] > 2)
                <table style="border: 1px solid black; width: 50%;" id="tableItems">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['item_details'] as $item)
                            <tr>
                                <td>{{ $item['item_name'] }}</td>
                                <td>{{ $item['qty'] }}</td>
                                <td>{{ $item['uom'] }}</td>
                                <td>{{ $item['remarks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table border='1' style="width: 100%;" id="tableQuotation">
                    <tr>
                        <th style="width: 50% !important; padding: 2px;"></th>
                        @foreach ($quote_data['supplierNames'] as $supplier)
                            <th style='padding: 2px; background-color: #13ace8'>{{ $supplier }}</th>
                        @endforeach
                    </tr>
                    @foreach ($quote_data['itemDetails'] as $item)
                        <tr>
                            <td class="text-center"><strong>{{ $item['item_name'] }}</strong></td>
                            @foreach ($item['item_quotation_details'] as $itemQuotation)
                                @foreach ($quote_data['supplierNames'] as $supplier)
                                    @php
                                        $element = $itemQuotation;
                                        $forAppend = '';
                                        $forAppendAttachment = '';
                                        $forSelected = '';
                                        
                                    @endphp
                                    @if ($supplier == $element['supplier_name'])
                                       
                                        @if ($element['selected_quotation'] == 1)
                                            <td style="background-color: rgb(39, 146, 39);">
                                        @else
                                            <td >
                                        @endif
                                            <span class="d-flex justify-content-center">
                                                @if ($element['selected_quotation'] == 1)
                                                    <table style="border: 0px !important; color: white">
                                                @else
                                                    <table style="border: 0px !important;">
                                                @endif        
                                                    <tbody>
                                                        <tr>
                                                            <td class='w-50'>{{ $element['currency'] }} :</td>
                                                            <td>{{ $element['price'] }} </td>
                                                        </tr>
                                                        @if($element['currency'] != 'PHP')
                                                            <tr>
                                                                <td> Rate/{{ $element['currency'] }}: </td>
                                                                <td>{{ $element['rate'] }}</td>
                                                            </tr>
                                                            <tr >
                                                                <td> PHP:</td>
                                                                <td>{{ $element['price'] * $element['rate'] }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td class='w-50'>Remarks: </td>
                                                            <td>{{ $element['remarks'] == null ? 'N/A' : $element['remarks'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan='2'></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </span>
                                        </td>
                                    @endif
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                    @foreach ($additionalRows as $additionalRow)
                        <tr>
                            <td><strong>{{ $additionalRow['title'] }}</strong></td>
                            @foreach ($quote_data['supplierNames'] as $supplier)
                                <td class="p-0 text-center">{{ $quote_data['uniqueOtherDetailsPerSupplier'][$supplier][0][$additionalRow['tblColName']] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>

            @endif

          
            {{-- Email Footer --}}
            <hr>
            <br>
            <div class="col-sm-12">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">For more info, please log-in to your Rapidx account. Go to http://rapidx/ and RFQv4 </label>
                </div>
            </div>

            <br>
            <br>

            <div class="col-sm-12">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label"><b> Notice of Disclaimer: </b></label>
                    <br>
                    <label class="col-sm-12 col-form-label"> <i> This message contains confidential information intended for a specific individual and purpose. If you are not the intended recipient, you should delete this message. Any disclosure,copying, or distribution of this message, or the taking of any action based on it, is strictly prohibited.</i></label>
                </div>
            </div>

            <div class="col-sm-12">
                <br><br>
                <label style="font-size: 18px;"><b>For concerns on using the form, please contact ISS at local numbers 205, 206, or 208.</b></label>
            </div>

        </div>
    </div>
</body>
</html>