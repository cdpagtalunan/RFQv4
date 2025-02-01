<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="{{ asset('public/css/app.css') }}">
</head>
<style>
    #tableRFQ td, th{
        border:1px solid black;
        
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
</style>
<body>
    <div class="row">
        <div class="col-sm-12">
            Good Day!
            <br>
            {{ $body }}
            <br>
            <br>
            <strong><i>Cancel Quotation Details:</i></strong>
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
                    <tr>
                        <td><strong>Logistics Remarks:</strong></td>
                        <td>:{{ $data['log_cancel_remarks']}}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            {{-- Footer of email --}}
            <br>
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