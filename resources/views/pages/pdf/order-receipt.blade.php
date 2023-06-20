<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
</head>
<body>

    <div id="receipt-container">
        <h1>Order Receipt</h1>
        <div id="info-container">
            <table>
                <tr>
                    <td>Order ID</td>
                    <td>{{ $orderId }}</td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td>{{ $invoiceDate }}</td>
                </tr>
            </table>
        </div>
        <div id="product-container">
            <table>
                <thead>
                    <tr>
                        <td>Product</td>
                        <td>Qty</td>
                        <td>Subtotal</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $prod)
                        <tr>
                            <td>{{ $prod->name }}</td>
                            <td>{{ $prod->qty }}</td>
                            <td>RM{{ $prod->total_price_after_foc }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="summary-container">
            <div class="summary">
                <div class="summary-left">Updated Credit</div>
                <div class="summary-right">RM{{ $updatedCredit }}</div>
            </div>
        </div>
    </div>
    
    <style>
        #receipt-container {
            width: 500px;
            margin: auto;
            font-family: sans-serif;
            text-align: center;
        }
        h1 {
            margin-bottom: 2em;
        }
        table {
            width: 100%;
        }
        #info-container table tr td:first-child {
            text-align: left;
        }
        #info-container table tr td:last-child {
            text-align: right;
        }
        #product-container {
            margin: 4em 0;
        }
        #product-container table thead {
            margin-bottom: 4em;
        }
        #product-container table tbody tr {
            padding-bottom: 3em;
        }
        #product-container table thead tr td:first-child,
        #product-container table tbody tr td:first-child {
            text-align: left;
            width: 70%;
        }
        #product-container table thead tr td:last-child,
        #product-container table tbody tr td:last-child {
            text-align: right;
        }
        tr {
            border: solid 50px red;
        }
    </style>
</body>
</html>