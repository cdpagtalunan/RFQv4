<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
    <meta name=ProgId content=Excel.Sheet>
    <meta name=Generator content="Microsoft Excel 11">
</head>
<body>
    <table>
        <tr>
            <td>List of Request for Quotation</td>
        </tr>
        <tr>
            <td>Control Number</td>
            <td>Items</td>
            <td>Quantity</td>
            <td>UOM</td>
            <td>Requestor</td>
            <td>PIC</td>
            <td>Status</td>
        </tr>

        @foreach ($rfqs as $rfq)
            <tr>
                <td>{{ $rfq['request_details']['ctrl_no'] }}</td>
                <td>{{ $rfq['item_name'] }}</td>
                <td>{{ $rfq['qty'] }}</td>
                <td>{{ $rfq['uom'] }}</td>
                <td>{{ $rfq['request_details']['created_by_details']['name'] }}</td>
                <td>{{ $rfq['request_details']['assigned_to_details']['name'] }}</td>
                @switch($rfq['request_details']['status'])
                    @case(1)
                        <td><strong>For logistics assignment</strong></td>
                        @break
                    @case(2)
                        <td><strong>Waiting for purchasing quotation</strong></td>
                        @break
                    @case(3)
                        <td><strong>For logistics head approval</strong></td>
                        @break
                    @case(4)
                        <td><strong>Served</strong></td>
                        @break
                    @default
                @endswitch
            </tr>
        @endforeach
    </table>
</body>
</html>