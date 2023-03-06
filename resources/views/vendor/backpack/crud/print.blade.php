<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body{
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            color:#333;
            text-align:left;
            font-size:18px;
            margin:0;
        }
        .container{
            margin:0 auto;
            margin-top:35px;
            padding:40px;
            width:750px;
            height:auto;
            background-color:#fff;
        }
        caption{
            font-size:28px;
            margin-bottom:15px;
        }
        table{
            border:1px solid #333;
            border-collapse:collapse;
            margin:0 auto;
            width:740px;
        }
        td, tr, th{
            padding:12px;
            border:1px solid #333;
            width:185px;
            vertical-align: top;
        }
        th{
            background-color: #f0f0f0;
        }
        h4, p{
            margin:0px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <caption>
                INVOICE
            </caption>
            <thead>
                <tr>
                    <th colspan="3">Invoice <strong>#{{ $invoice->invoice_id }}</strong></th>
                    <th>{{ $invoice->created_at->format('D, d M Y') }}</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <h4>Bigbang Computer</h4>
                        <p>Jl. Ki Mangunsarkoro No.92, Brumbungan, <br>
                            Kec. Semarang Tengah, Kota Semarang,<br>
                            Jawa Tengah 50135<br>
                            (024) 3541792 / WA 081325106813
                        </p>
                    </td>
                    <td colspan="2">
                        <h4>Customer: </h4>
                        <p>{{ $customer_data->name }}<br>
                        +62{{ $customer_data->phone }} <br>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h4>
                            Computer
                        </h4>
                        <p>{{ $computer_data->brand }} {{ $computer_data->type }}<br>
                        S/N : {{ $computer_data->serial_number }}
                        </p>
                        <h4>
                            Equipment
                        </h4>
                        <p>
                            @if($computer_data->eq_bag==1 && $computer_data->eq_charger_cable==1)
                            Bag, Charger Cable
                            @elseif($computer_data->eq_charger_cable==1)
                            Charger Cable
                            @elseif($computer_data->eq_bag==1)
                            Bag
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td colspan="2">
                        <h4>
                            Problem
                        </h4>
                        <p>
                            {{ $computer_data->problem }}
                        </p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                @foreach ($invoice_details as $row)
                <tr>
                    <td>{{ $row->sname }}</td>
                    <td>Rp {{ number_format($row->sprice) }}</td>
                    <td>{{ $row->item_qty }}</td>
                    <td>Rp {{ number_format($row->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <td>Rp {{ number_format($invoice->total) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
