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
        .left { text-align: left; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .title { font-size: 14px; font-weight: 700; }
        .subtitle { font-size: 12px; font-weight: 700; }
        .muted { color:#000; opacity:.95; }
        .spacer-6 { height: 6px; }
        .spacer-8 { height: 8px; }
        .sheet-border { border: 1.5px solid #000; padding: 8px; }
        .hrow td { border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; }
        .caps { text-transform: uppercase; letter-spacing: .2px; }
        .shade { background: #f2f2f2; }
        .photo-box { border: 1px solid #000; height: 140px; align-items: center; justify-content: center; }
        .photo-box img { max-height: 135px; max-width: 100%; object-fit: contain; }
        .small { font-size: 10px; }
        .new-table { border: 1.5px solid #000; }
        .new-table .shade { background: #e0e0e0; }
    </style>
</head>
<body>

<div class="sheet-border">

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

    <table class="tight">
        <tr class="hrow shade">
            <td class="bold caps center" style="font-size: 16px;" colspan="8">CITI TRENDS ORDER</td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="8" >{{ $order->title ?? 'No Title' }}</td>
        </tr>
        <tr class="hrow shade">
            <td class="bold caps center" style="font-size: 16px;" colspan="8">{{ $order->description ?? '-' }} ({{ $order->gsm ?? '200' }} GSM)</td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="4">TAFITA PO LABEL#</td>
            <td class="bold caps center" colspan="4"> {{ $order->po_label ?? '-' }} </td>
        </tr>
        <tr class="">
            <td class="bold caps center" colspan="4">EVOLUTION CARE LABEL#</td>
            <td class="bold caps center" colspan="4"> {{ $order->care_label ?? '-' }} </td>
        </tr>
        <tr class="hrow shade">
            <td class="bold caps center" colspan="8">EVOLUTION MAIN CANVAS LABEL &amp; SIZE</td>
        </tr>
    </table>

    <div class="spacer-8"></div>

    @php
        $grandCuttingTotal = 0;
        $grandActualTotal = 0;
    @endphp

    @if(!empty($order->colors))
        @foreach($order->colors as $color)
            @if(!empty($color->packs))
                @php
                    $colorRatios = collect($color->packs)->flatMap(function($pack) {
                        return collect($pack->ratios)->map(function($ratio) {
                            return ['size_name' => strtoupper($ratio->size_name), 'qty' => $ratio->actual_qty, 'ratio' => $ratio->ratio];
                        });
                    });
                    $sizeOrder = ['S', 'M', 'L', 'XL', '2XL', '3XL'];
                    $allSizes = $colorRatios->pluck('size_name')->unique()->sort(function($a, $b) use ($sizeOrder) {
                        $posA = array_search($a, $sizeOrder);
                        $posB = array_search($b, $sizeOrder);
                        return $posA <=> $posB;
                    })->values();
                    //$allSizes = $colorRatios->pluck('size_name')->unique()->sort()->values();
                    $ratioHeaders = [];
                    $dataRows = [];

                    // These variables will hold the total for the current table only
                    $tableCuttingTotal = 0;
                    $tableActualTotal = 0;
                @endphp
                <table class="tight new-table">
                    @foreach($color->packs as $pack)
                        @php
                            $ratiosBySize = collect($pack->ratios)->keyBy(fn($ratio) => strtoupper($ratio->size_name));
                            $packCuttingTotal = $pack->pack_qty + ($pack->pack_extra_qty ?? 0);
                            $packActualTotal = $pack->pack_qty;

                             // Accumulate for the current table
                            $tableCuttingTotal += $packCuttingTotal;
                            $tableActualTotal += $packActualTotal;

                            $grandCuttingTotal += $packCuttingTotal;
                            $grandActualTotal += $packActualTotal;
                            
                            $ratioHeaders[] = '
                                <tr class="shade bold caps">
                                    <td colspan="2">RATIO# '.($pack->pack->name ?? 'N/A').' PACK</td>
                                    '.$allSizes->map(fn($size) => '<td>'.($ratiosBySize[$size]['ratio'] ?? '-').'</td>')->implode('').'
                                    <td class="shade">'.collect($ratiosBySize)->sum('ratio').'</td>
                                    <td colspan="2"></td>
                                </tr>
                            ';

                            $rowCells = '';
                            foreach ($allSizes as $size) {
                                $rowCells .= '<td>'.($ratiosBySize[$size]['actual_qty'] ?? 'NO').'</td>';
                            }

                            $dataRows[] = '
                                <tr>
                                    <td class="caps bold">'.($color->color_name ?? '-').'</td>
                                    <td class="caps">'.($pack->pack->name ?? 'N/A').'</td>
                                    '.$rowCells.'
                                    <td>'.$packCuttingTotal.'</td>
                                    <td>'.$packActualTotal.'</td>
                                </tr>
                            ';
                        @endphp
                    @endforeach

                    {!! implode('', $ratioHeaders) !!}

                    <tr class="shade bold">
                        <td class="caps">COLOR</td>
                        <td>PACK#</td>
                        @foreach($allSizes as $size)
                            <td>{{ $size }}</td>
                        @endforeach
                        <td class="caps">CUTTING TOTAL</td>
                        <td class="caps">ACTUAL</td>
                    </tr>

                    {!! implode('', $dataRows) !!}

                    <tr class="shade bold">
                        <td class="caps" colspan="2">TOTAL</td>
                        @foreach($allSizes as $size)
                            <td>
                                {{ $colorRatios->where('size_name', $size)->sum('qty') }}
                            </td>
                        @endforeach
                        <td>{{ $tableCuttingTotal }}</td>
                        <td>{{ $tableActualTotal }}</td>
                    </tr>
                </table>
                <div class="spacer-8"></div>
            @endif
        @endforeach
    @endif

    <table class="tight">
        <tr>
            <td style="width:58%; padding-right:6px;">
                <table class="tight">
                    <tr class="shade bold caps">
                        <td colspan="3">ACTUAL ORDER QTY</td>
                        <td colspan="2">{{ $grandActualTotal }}</td>
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
            <td style="width:42%; padding-left:6px; vertical-align:top;">
                <table class="tight" style="width:100%;">
                    <tr class="shade bold caps">
                        <td style="width:70%;">CUTTING ORDER QTY</td>
                        <td class="bold" style="width:30%; text-align:right;">{{ $grandCuttingTotal }}</td>
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