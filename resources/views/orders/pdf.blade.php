<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Sheet</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 14px; color:#000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; vertical-align: middle; }
        .nb td, .nb th { border: none !important; padding: 2px 4px; }
        .tight td, .tight th { padding: 3px 4px; }
        .center { text-align: center; }
        .left   { text-align: left; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }
        .title  { font-size: 14px; font-weight: 700; }
        .subtitle { font-size: 12px; font-weight: 700; }
        .muted  { color:#000; opacity:.95; }
        .spacer-6 { height: 6px; }
        .spacer-8 { height: 8px; }
        .sheet-border { border: 1.5px solid #000; padding: 8px; }
        .hrow td { border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; }
        .caps { text-transform: uppercase; letter-spacing: .2px; }
        .shade { background: #f2f2f2; }
        .photo-box { border: 1px solid #000; height: 140px; display: flex; align-items: center; justify-content: center; }
        .photo-box img { max-height: 135px; max-width: 100%; object-fit: contain; }
        .small { font-size: 10px; }
    </style>
</head>
<body>

<div class="sheet-border">

    <!-- Header -->
    <table class="nb">
        <tr>
            <td class="left bold caps">US INDUSTRIES</td>
            <td class="center bold caps">DATE: {{ \Carbon\Carbon::parse($order->po_date ?? now())->format('d-m-Y') }}</td>
            <td class="right bold caps">EVOLUTION</td>
        </tr>
        <tr>
            <td class="left caps">JOB# <span class="bold">{{ $order->job_no ?? '-' }}</span></td>
            <td class="center caps bold">CUTTING PROGRAM</td>
            <td class="right caps">STYLE# <span class="bold">{{ $order->style_no ?? '-' }}</span></td>
        </tr>
    </table>

    <div class="spacer-6"></div>

    <div class="title center caps">CITI TRENDS ORDER</div>
    <div class="center muted caps small">
        {{ $order->buyer ? ($order->buyer.' ORDER') : 'KID’S FOOTBALL 16 NO PRINT ALLOVER PRINT S/J SS BOXY TEE' }}
    </div>
    <div class="subtitle center caps">
        BOXY STYLE &amp; BOXY SPECS T-SHIRTS S/J ({{ $order->gsm ?? '200' }} GSM)
    </div>

    <div class="spacer-6"></div>

    <!-- Main Size/Ratio Table -->
    <table class="tight">
        <tr class="hrow shade">
            <td class="bold caps center" colspan="8">EVOLUTION MAIN CANVAS LABEL &amp; SIZE</td>
        </tr>

        <!-- Ratio Row -->
        <tr class="shade bold caps">
            <td colspan="2">RATIO</td>
            @foreach($order->ratios as $r)
                <td>{{ $r->ratio }}</td>
            @endforeach
            <td>{{ $order->ratios->sum('ratio') }}</td>
            <td></td>
        </tr>

        <!-- Body Color + Sizes Row -->
        <tr>
            <td class="caps bold">BODY COLOR</td>
            <td>PACKS</td>
            @foreach($order->ratios as $r)
                <td>{{ strtoupper($r->size_name) }}</td>
            @endforeach
            <td>CUTTING TOTAL</td>
            <td>ACTUAL</td>
        </tr>

        <!-- Cutting Qty -->
        <tr>
            <td class="caps bold">{{ $order->body_color ?? '-' }}</td>
            <td class="caps">{{ $order->pack?->name ?? '-' }}</td>
            @foreach($order->ratios as $r)
                <td>{{ $r->cutting_qty ?? 0 }}</td>
            @endforeach
            <td>{{ $order->ratios->sum('cutting_qty') }}</td>
            <td>{{ $order->ratios->sum('actual_qty') }}</td>
        </tr>

        <!-- Totals -->
        <tr class="shade bold">
            <td class="caps">TOTAL</td>
            <td></td>
            @foreach($order->ratios as $r)
                <td>{{ $r->cutting_qty ?? 0 }}</td>
            @endforeach
            <td>{{ $order->ratios->sum('cutting_qty') }}</td>
            <td>{{ $order->ratios->sum('actual_qty') }}</td>
        </tr>
    </table>

    <div class="spacer-8"></div>

    <!-- Bottom Section -->
    <table class="nb">
        <tr>
            <!-- Left -->
            <td style="width:58%; padding-right:6px;">
                <div class="subtitle caps">ACTUAL ORDER QTY</div>
                <table class="tight">
                    <tr class="shade bold caps">
                        <td>COLOUR</td>
                        <td>FABRIC</td>
                        <td>FINISH WIDTH</td>
                        <td>KG</td>
                        <td>FINISH GSM</td>
                    </tr>
                    <tr>
                        <td class="caps">{{ $order->body_color ?? '-' }}</td>
                        <td class="caps">{{ $order->fabrics ?? '-' }}</td>
                        <td class="caps">{{ $order->finish_width ?? '-' }}</td>
                        <td class="caps">{{ $order->KG ?? '-' }}</td>
                        <td class="caps">{{ $order->gsm ?? '-' }}</td>
                    </tr>
                    <tr class="shade bold">
                        <td colspan="5" class="right caps">TOTAL: {{ $order->ratios->sum('actual_qty') }}</td>
                    </tr>
                </table>
            </td>

            <!-- Right -->
            <td style="width:42%; padding-left:6px;">
                <table class="tight">
                    <tr class="shade bold caps">
                        <td>CUTTING ORDER QTY</td>
                    </tr>
                    <tr>
                        <td class="bold">{{ $order->ratios->sum('cutting_qty') }}</td>
                    </tr>
                    <tr>
                        <td class="photo-box">
                            @if(!empty($order->file_path))
                                <img src="{{ public_path('storage/'.$order->file_path) }}" alt="Garment" width="120">
                            @else
                                <span class="small">No Image</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
