<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - PS Rental</title>
    <!-- We use inline styles strictly limited to 58mm POS thermal receipt specs -->
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Monospace is best for receipt */
            margin: 0;
            padding: 8px;
            width: 58mm; /* Thermal printer standard width */
            font-size: 11px;
            color: #000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 5px; padding-bottom: 5px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-2 { margin-top: 8px; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        .item-name { max-width: 30mm; word-wrap: break-word; }

        @media print {
            body { margin: 0; padding: 5px; }
            button.no-print { display: none; }
        }

        /* Action buttons for preview screen */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            font-family: sans-serif;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">🖨️ Cetak Struk</button>

    <div class="text-center font-bold" style="font-size: 14px; margin-bottom: 2px;">
        PS RENTAL & KANTIN
    </div>
    <div class="text-center" style="font-size: 9px; margin-bottom: 10px;">
        Jl. Keadilan Semesta No. 99<br>
        Telp: 0812-3456-7890
    </div>

    <div class="border-top border-bottom">
        <table style="font-size: 10px;">
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Admin</td>
                <td class="text-right">{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td class="text-right">{{ $data->user ? $data->user->name : ($data->guest_name ?? 'Guest') }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-2 mb-2 font-bold text-center">
        @if($type === 'rental')
            === STRUK RENTAL PS ===
        @else
            === STRUK KANTIN F&B ===
        @endif
    </div>

    <table>
        @if($type === 'rental')
            <tr>
                <td class="item-name">
                    {{ $data->device->name }}<br>
                    <small>
                        {{ $data->start_time->format('H:i') }} - {{ $data->type == 'fixed' ? $data->end_time->format('H:i') : 'Skrg' }}
                    </small>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    {{ number_format($data->total_price, 0, ',', '.') }}
                </td>
            </tr>
        @elseif($type === 'fnb')
            @foreach($data->items as $item)
            <tr>
                <td class="item-name">
                    {{ $item->product->name }}<br>
                    <small>{{ $item->quantity }} x {{ number_format($item->product->price, 0, ',', '.') }}</small>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        @endif
    </table>

    <div class="border-top mt-2">
        <table>
            <tr>
                <td class="font-bold">TOTAL BAYAR</td>
                <td class="text-right font-bold" style="font-size: 13px;">
                    Rp {{ number_format($data->total_price, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Metode</td>
                <td class="text-right">
                    @if($type === 'rental')
                        Cash / Offline
                    @else
                        {{ $data->payment_method }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center mt-2 border-top" style="font-size: 10px; padding-top: 10px;">
        *** TERIMA KASIH ***<br>
        Main Terus, Lupa Waktu!
    </div>

    <script>
        // Auto-print upon opening the view
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
