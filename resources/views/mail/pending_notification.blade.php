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
            {{-- Please be informed that RFQ is for purchasing assignment. --}}
            {{ $body }}
            <br>
            Please see list of pending RFQ below:
            
            <br>
            <table id="tableRFQ">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Category</th>
                        <th>Date Needed</th>
                        <th>Requested By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $rfq)
                        <tr>
                            <td>{{ $rfq['ctrl_no'] }}</td>
                            <td>{{ $rfq['category_details']['category_name'] }}</td>
                            <td>{{ $rfq['date_needed'] }}</td>
                            <td>{{ $rfq['created_by_details']['name'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

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