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
        .photo-box { border: 1px solid #000; height: 140px;  align-items: center; justify-content: center; }
        .photo-box img { max-height: 135px; max-width: 100%; object-fit: contain; }
        .small { font-size: 10px; }
    </style>
</head>
<body>

<div class="sheet-border">

    <!-- Header -->
    <table class="tight">
        <tr class="hrow shade">
            <td class="center bold caps" colspan="2">US INDUSTRIES</td>
            <td class="center bold caps">DATE: {{ \Carbon\Carbon::parse($order->po_date ?? now())->format('d-m-Y') }}</td>
            <td class="center bold caps" colspan="2">EVOLUTION</td>
        </tr>
        <tr>
            <td class="center caps">JOB#</td>
            <td class="bold">{{ $order->job_no ?? '-' }}</td>
            <td class="center caps bold">CUTTING PROGRAM</td>
            <td class="center caps">STYLE#</td>
            <td class="bold">{{ $order->style_no ?? '-' }}</td>
        </tr>
    </table>

    <div class="spacer-6"></div>
    
    <!-- Main Size/Ratio Table -->
    <table class="tight">
        <tr class="hrow shade">
            <td class="bold caps center" style="font-size: 16px;" colspan="8">CITI TRENDS ORDER</td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="8" >{{ $order->title ?? 'No Title' }}</td>
        </tr>
        <tr class="hrow shade">
            <td class="bold caps center" style="font-size: 16px;" colspan="8">{{ $order->description }} ({{ $order->gsm ?? '200' }} GSM)</td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="4">TAFITA PO LABEL#</td>
            <td class="bold caps center" colspan="4"> {{ $order->po_label }} </td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="4">EVOLUTION CARE LABEL#</td>
            <td class="bold caps center" colspan="4"> {{ $order->care_label }} </td>
        </tr>
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
            <td colspan="2"></td>
        </tr>

        <!-- Body Color + Sizes Row -->
        <tr>
            <td class="caps bold">BODY COLOR</td>
            <td>PACKS</td>
            @foreach($order->ratios as $r)
                <td>{{ strtoupper($r->size_name) }}</td>
            @endforeach
            <td>CUTTING TOTAL</td>
            <td colspan="2">ACTUAL</td>
        </tr>

        <!-- Cutting Qty -->
        <tr>
            <td class="caps bold">{{ $order->body_color ?? '-' }}</td>
            <td class="caps">{{ $order->pack?->name ?? '-' }}</td>
            @foreach($order->ratios as $r)
                <td>{{ $r->cutting_qty ?? 0 }}</td>
            @endforeach
            <td>{{ $order->ratios->sum('cutting_qty') }}</td>
            <td colspan="2">{{ $order->ratios->sum('actual_qty') }}</td>
        </tr>

        <!-- Totals -->
        <tr class="shade bold">
            <td class="caps">TOTAL</td>
            <td></td>
            @foreach($order->ratios as $r)
                <td>{{ $r->cutting_qty ?? 0 }}</td>
            @endforeach
            <td>{{ $order->ratios->sum('cutting_qty') }}</td>
            <td colspan="2">{{ $order->ratios->sum('actual_qty') }}</td>
        </tr>
    </table>

    <div class="spacer-8"></div>

    <!-- Bottom Section -->
    <table class="tight">
        <tr>
            <!-- Left -->
            <td style="width:58%; padding-right:6px;">
                <table class="tight">
                    <tr class="shade bold caps">
                        <td colspan="3">ACTUAL ORDER QTY</td>
                        <td colspan="2">{{ $order->ratios->sum('actual_qty') }}</td>
                    </tr>
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
                        <td colspan="2" class="center caps">TOTAL >>>>>>>>>>></td>
                        <td colspan="1" class="center caps"></td>
                        <td colspan="1" class="center caps"></td>
                        <td colspan="1" class="center caps"></td>

                    </tr>
                </table>
            </td>

            <!-- Right -->
            <td style="width:42%; padding-left:6px; vertical-align:top;">
                <table class="tight" style="width:100%;">
                    <tr class="shade bold caps">
                        <td style="width:70%;">CUTTING ORDER QTY</td>
                        <td class="bold" style="width:30%; text-align:right;">
                            {{ $order->ratios->sum('cutting_qty') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="photo-box" colspan="2" style="padding-top:8px; text-align:center;">
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
