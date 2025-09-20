<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Sheet</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 14px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }

        .nb td,
        .nb th {
            border: none !important;
            padding: 2px 4px;
        }

        .tight td,
        .tight th {
            padding: 3px 4px;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 14px;
            font-weight: 700;
        }

        .subtitle {
            font-size: 12px;
            font-weight: 700;
        }

        .muted {
            color: #000;
            opacity: .95;
        }

        .spacer-6 {
            height: 6px;
        }

        .spacer-8 {
            height: 8px;
        }

        .sheet-border {
            border: 1.5px solid #000;
            padding: 8px;
        }

        .hrow td {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        .caps {
            text-transform: uppercase;
            letter-spacing: .2px;
        }

        .shade {
            background: #f2f2f2;
        }

        .photo-box {
            border: 1px solid #000;
            height: 140px;
            align-items: center;
            justify-content: center;
        }

        .photo-box img {
            max-height: 135px;
            max-width: 100%;
            object-fit: contain;
        }

        .small {
            font-size: 10px;
        }

        .new-table {
            border: 1.5px solid #000;
        }

        .new-table .shade {
            background: #e0e0e0;
        }
    </style>
</head>

<body>

    <div class="sheet-border">

        {{-- Header --}}
        <table class="tight">
            <tr class="hrow shade">
                <td class="center bold caps" colspan="2">US INDUSTRIES</td>
                <td class="center bold caps">DATE:
                    {{ \Carbon\Carbon::parse($purchaseOrder->po_date ?? now())->format('d-m-Y') }}</td>
                <td class="center bold caps" colspan="2">EVOLUTION</td>
            </tr>
            <tr>
                <td class="center caps">JOB#</td>
                <td class="bold">{{ $purchaseOrder->job_no ?? '-' }}</td>
                <td class="center caps bold">CUTTING PROGRAM</td>
                <td class="center caps">STYLE#</td>
                <td class="bold">{{ $purchaseOrder->style_no ?? '-' }}</td>
            </tr>
        </table>

        <div class="spacer-6"></div>

        {{-- Title / Labels --}}
        <table class="tight">
            <tr class="hrow shade">
                <td class="bold caps center" style="font-size: 16px;" colspan="8">CITI TRENDS ORDER</td>
            </tr>
            <tr>
                <td class="bold caps center" colspan="8">{{ $purchaseOrder->title ?? 'No Title' }}</td>
            </tr>
            <tr class="hrow shade">
                <td class="bold caps center" style="font-size: 16px;" colspan="8">
                    {{ $purchaseOrder->description ?? '-' }} ({{ $purchaseOrder->gsm ?? '200' }} GSM)</td>
            </tr>
            <tr>
                <td class="bold caps center" colspan="4">TAFITA PO LABEL#</td>
                <td class="bold caps center" colspan="4">{{ $purchaseOrder->po_label ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bold caps center" colspan="4">EVOLUTION CARE LABEL#</td>
                <td class="bold caps center" colspan="4">{{ $purchaseOrder->care_label ?? '-' }}</td>
            </tr>
            <tr class="hrow shade">
                <td class="bold caps center" colspan="8">EVOLUTION MAIN CANVAS LABEL &amp; SIZE</td>
            </tr>
        </table>

        <div class="spacer-8"></div>

        {{-- Prepare totals --}}
        @php
            $grandCuttingTotal = 0;
            $grandActualTotal = 0;
            $sizeOrder = ['S', 'M', 'L', 'XL', '2XL', '3XL'];
        @endphp

        {{-- Packs / Colors / Ratios --}}
        @foreach ($grouped as $sizeGroupId => $packInfos)
    @php
        // Collect all unique sizes in this group
        $allSizes = collect();
        foreach ($packInfos as $packInfo) {
            $allSizes = $allSizes->merge($packInfo->pack->sizes);
        }
        $allSizes = $allSizes->unique('id');

        $grandCuttingTotal = 0;
        $grandActualTotal = 0;

        // Track processed colors for "one color, multiple packs" tables
        $processedColors = [];
    @endphp

    @foreach ($packInfos as $packInfo)
        @if ($packInfo->colors->count() > 1)
            {{-- Case: One Pack, Multiple Colors --}}
            @php
                $tableCuttingTotal = 0;
                $tableActualTotal = 0;
                $sizeSums = [];
                foreach ($allSizes as $size) $sizeSums[$size->id] = 0;
            @endphp

            <table class="tight new-table">
                <tr class="shade bold caps">
                    <td>COLOR</td>
                    <td>PACK#</td>
                    @foreach ($allSizes as $size)
                        <th>{{ $size->size_name }}</th>
                    @endforeach
                    <td>CUTTING TOTAL</td>
                    <td>ACTUAL</td>
                </tr>

                @foreach ($packInfo->colors as $color)
                    @php
                        $qty = $color->qty ?? 0;
                        $extraPercent = $color->extra_usage_qty ?? 0;
                        $cuttingTotal = $qty + ($qty * $extraPercent)/100;
                        $actualTotal = $color->qty;

                        $tableCuttingTotal += $cuttingTotal;
                        $tableActualTotal += $actualTotal;
                        $grandCuttingTotal += $cuttingTotal;
                        $grandActualTotal += $actualTotal;
                        $sizeSums = $sizeSums ?? [];
                    @endphp
                    <tr>
                        <td>{{ $color->color_name }}</td>
                        <td>{{ $packInfo->pack->name }}</td>
                        @foreach ($allSizes as $size)
                            @php
                                $val = $color->ratios->where('size_id', $size->id)->sum('qty');
                                $sizeSums[$size->id] = ($sizeSums[$size->id] ?? 0) + $val;
                            @endphp
                            <td>{{ $val }}</td>
                        @endforeach
                        <td>{{ $cuttingTotal }}</td>
                        <td>{{ $actualTotal }}</td>
                    </tr>
                @endforeach

                <tr class="shade bold">
                    <td colspan="2">TOTAL</td>
                    @foreach ($allSizes as $size)
                        <td>{{ $sizeSums[$size->id] }}</td>
                    @endforeach
                    <td>{{ $tableCuttingTotal }}</td>
                    <td>{{ $tableActualTotal }}</td>
                </tr>
            </table>
            <div class="spacer-8"></div>

        @else
            {{-- Case: One Color, Multiple Packs in same sizeGroup --}}
            @php
                $colorName = $packInfo->colors->first()->color_name ?? '-';
            @endphp

            @if (!in_array($colorName, $processedColors))
                @php
                    $processedColors[] = $colorName;

                    // Get all packs in this sizeGroup that have this color
                    $colorPacks = collect($packInfos)->filter(function ($p) use ($colorName) {
                        return $p->colors->pluck('color_name')->contains($colorName);
                    });

                    $tableCuttingTotal = 0;
                    $tableActualTotal = 0;
                    $sizeSums = [];
                    foreach ($allSizes as $size) $sizeSums[$size->id] = 0;
                @endphp

                <table class="tight new-table">
                    <tr class="shade bold caps">
                        <td>COLOR</td>
                        <td>ADD-ON</td>
                        <td>PACK#</td>
                        @foreach ($allSizes as $size)
                            <th>{{ $size->size_name }}</th>
                        @endforeach
                        <td>CUTTING TOTAL</td>
                        <td>ACTUAL</td>
                    </tr>

                    @foreach ($colorPacks as $pInfo)
                        @foreach ($pInfo->colors as $c)
                            @if ($c->color_name == $colorName)
                                @php
                                    $qty = $c->qty ?? 0;
                                    $extraPercent = $c->extra_usage_qty ?? 0;
                                    $cuttingTotal = $qty + ($qty * $extraPercent)/100;
                                    $actualTotal = $c->qty;

                                    $tableCuttingTotal += $cuttingTotal;
                                    $tableActualTotal += $actualTotal;
                                    $grandCuttingTotal += $cuttingTotal;
                                    $grandActualTotal += $actualTotal;
                                @endphp
                                <tr>
                                    <td>{{ $c->color_name }}</td>
                                    <td>{{ $c->addOn->name ?? '-' }}</td>
                                    <td>{{ $pInfo->pack->name }}</td>
                                    @foreach ($allSizes as $size)
                                        @php
                                            $val = $c->ratios->where('size_id', $size->id)->sum('qty');
                                            $sizeSums[$size->id] += $val;
                                        @endphp
                                        <td>{{ $val }}</td>
                                    @endforeach
                                    <td>{{ $cuttingTotal }}</td>
                                    <td>{{ $actualTotal }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach

                    <tr class="shade bold">
                        <td colspan="3">TOTAL</td>
                        @foreach ($allSizes as $size)
                            <td>{{ $sizeSums[$size->id] }}</td>
                        @endforeach
                        <td>{{ $tableCuttingTotal }}</td>
                        <td>{{ $tableActualTotal }}</td>
                    </tr>
                </table>
                <div class="spacer-8"></div>
            @endif
        @endif
    @endforeach
@endforeach




        {{-- Bottom summary --}}
        <table class="tight">
            <tr>
                <td style="width:58%; padding-right:6px;">
                    <table class="tight">
                        <tr class="shade bold caps">
                            <td colspan="3">ACTUAL ORDER QTY</td>
                            <td colspan="2">{{ $purchaseOrder->order_qty }}</td>
                        </tr>
                        <tr class="shade bold caps">
                            <td>COLOUR</td>
                            <td>FABRIC</td>
                            <td>FINISH WIDTH</td>
                            <td>KG</td>
                            <td>FINISH GSM</td>
                        </tr>
                        <tr>
                            <td class="caps">{{ $purchaseOrder->body_color ?? '-' }}</td>
                            <td class="caps">{{ $purchaseOrder->fabrics ?? '-' }}</td>
                            <td class="caps">{{ $purchaseOrder->finish_width ?? '-' }}</td>
                            <td class="caps">{{ $purchaseOrder->KG ?? '-' }}</td>
                            <td class="caps">{{ $purchaseOrder->gsm ?? '-' }}</td>
                        </tr>
                        <tr class="shade bold">
                            <td colspan="2" class="center caps">TOTAL >>>>>>>>>>></td>
                            <td colspan="3"></td>
                        </tr>
                    </table>
                </td>
                <td style="width:42%; padding-left:6px; vertical-align:top;">
                    <table class="tight" style="width:100%;">
                        <tr class="shade bold caps">
                            <td style="width:70%;">CUTTING ORDER QTY</td>
                            <td class="bold" style="width:30%; text-align:right;">{{ $purchaseOrder->final_total  }}</td>
                        </tr>
                        <tr>
                            <td class="photo-box" colspan="2" style="padding-top:8px; text-align:center;">
                                @if (!empty($purchaseOrder->file_path))
                                    <img src="{{ public_path('storage/' . $purchaseOrder->file_path) }}" alt="Garment"
                                        width="120">
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
