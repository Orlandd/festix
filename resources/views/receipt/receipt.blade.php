{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bukti Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            border: 1px solid #e1e1e1;
        }

        th {
            background-color: #f2f2f2;
        }

        .button {
            display: inline-block;
            background: #3498db;
            color: #fff;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bukti Pembayaran</h1>

        <p>Halo, {{ Auth::user()->name }}</p>
        <p>Terima kasih telah melakukan pembayaran. Berikut adalah rincian transaksi Anda:</p>

        <h2>📄 Detail Pembayaran</h2>
        <table>
            <tr>
                <th>ID Pembayaran</th>
                <td>{{ $receipt->id }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($receipt->status) }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $receipt->created_at->format('d M Y, H:i') }}</td>
            </tr>
        </table>

        <h2 style="margin-top: 30px;">🎫 Tiket Acara</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Acara</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Kategori Tempat Duduk</th>
                    <th>Quantity</th>
                    <th>@ Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $receipt->eventPrice->event->name }}</td>
                    <td>{{ $receipt->eventPrice->event->date }}</td>
                    <td>{{ $receipt->eventPrice->event->time }}</td>
                    <td>{{ $receipt->eventPrice->seatCategory->name }}</td>
                    <td>{{ $receipt->amount_ticket }}</td>
                    <td>Rp {{ number_format($receipt->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($receipt->total_payment, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <p class="footer">
            Jika Anda merasa tidak melakukan transaksi ini, segera hubungi tim kami.<br><br>
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>
</body>

</html> --}}


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: monospace;
            background: #fff;
            padding: 20px;
            color: #000;
        }

        .receipt {
            max-width: 380px;
            margin: 0 auto;
            padding: 20px;
            border: 1px dashed #000;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2,
        .footer p {
            margin: 0;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .info,
        .items,
        .totals {
            width: 100%;
            font-size: 14px;
        }

        .info td,
        .items td,
        .totals td {
            padding: 2px 0;
        }

        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
            margin-bottom: 4px;
        }

        .totals {
            margin-top: 10px;
        }

        .totals td {
            font-weight: bold;
        }

        .thankyou {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
            <p>Bukti Pembayaran</p>
        </div>

        <div class="line"></div>

        <table class="info">
            <tr>
                <td>Nama</td>
                <td>: {{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td>ID</td>
                <td>: {{ $receipt->id }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ ucfirst($receipt->status) }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $receipt->created_at->format('d M Y, H:i') }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <table class="items">
            <tr>
                <th colspan="2">Detail Tiket</th>
            </tr>
            <tr>
                <td>Acara</td>
                <td>: {{ $receipt->eventPrice->event->name }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $receipt->eventPrice->event->date }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>: {{ $receipt->eventPrice->event->time }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: {{ $receipt->eventPrice->seatCategory->name }}</td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>: {{ $receipt->amount_ticket }}</td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>: Rp {{ number_format($receipt->price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="line"></div>

        @php
            $subtotal = $receipt->price * $receipt->amount_ticket;
            $ppn = $subtotal * 0.1;
            $admin = 2000;
            $grandTotal = $subtotal + $ppn + $admin;
        @endphp

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td>: Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>PPN (10%)</td>
                <td>: Rp {{ number_format($ppn, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Admin</td>
                <td>: Rp {{ number_format($admin, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Bayar</td>
                <td>: <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="thankyou">
            <p>Terima kasih atas pembelian Anda!</p>
            <p>Hubungi kami jika ada kendala.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
