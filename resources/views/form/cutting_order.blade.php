@extends('layouts.app')
@section('content')

    <h1 class="text-3xl font-bold text-center">
        {{ isset($order) ? 'Edit Order' : 'Order Entry Form' }}
    </h1>

    <!-- Form -->
    <form action="{{ isset($order) ? route('orders.update', $order->id) : route('orders.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($order))
            @method('POST') {{-- because you defined update route as POST --}}
        @endif

        <!-- Section 1: Order Details -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold border-b pb-2">Order Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="job_no"
                       value="{{ old('job_no', $order->job_no ?? '') }}"
                       placeholder="Job #" class="border rounded-lg p-2">

                <input type="text" name="style_no"
                       value="{{ old('style_no', $order->style_no ?? '') }}"
                       placeholder="Style #" class="border rounded-lg p-2">

                <input type="text" name="po_date"
                       value="{{ old('po_date', $order->po_date ?? '') }}"
                       placeholder="PO Date" class="border rounded-lg p-2"
                       onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">

                <input type="text" name="ship_date"
                       value="{{ old('ship_date', $order->ship_date ?? '') }}"
                       placeholder="Ship Date" class="border rounded-lg p-2"
                       onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">

                <input type="text" name="fabrics"
                       value="{{ old('fabrics', $order->fabrics ?? '') }}"
                       placeholder="Fabrics" class="border rounded-lg p-2">

                <input type="number" name="gsm"
                       value="{{ old('gsm', $order->gsm ?? '') }}"
                       placeholder="GSM" class="border rounded-lg p-2">

                <input type="text" name="buyer"
                       value="{{ old('buyer', $order->buyer ?? '') }}"
                       placeholder="Buyer" class="border rounded-lg p-2">

                <input type="number" name="order_qty" id="orderQty"
                       value="{{ old('order_qty', $order->order_qty ?? '') }}"
                       placeholder="Order Qty" class="border rounded-lg p-2" oninput="updateRatios()">

                <input type="file" name="file" class="border rounded-lg p-2 col-span-full">
                @if(isset($order) && $order->file_path)
                    <a href="{{ asset('storage/'.$order->file_path) }}" target="_blank" class="text-blue-600">
                        View Current File
                    </a>
                @endif
            </div>
        </section>

        <!-- Section 2: Color & Pack -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold border-b pb-2">Color & Pack</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="body_color" id="color"
                       value="{{ old('body_color', $order->body_color ?? '') }}"
                       placeholder="Body Color" class="border rounded-lg p-2" oninput="updateTotals()">

                <div>
                    <label class="block font-medium mb-1">Pack</label>
                    <div class="flex gap-2">
                        <select name="pack_id" id="packSelect" class="border rounded-lg p-2 w-full"
                                onchange="updateTotals()">
                            <option value="">Select Pack</option>
                            @foreach($packs as $pack)
                                <option value="{{ $pack->id }}"
                                    {{ old('pack_id', $order->pack_id ?? '') == $pack->id ? 'selected' : '' }}>
                                    {{ $pack->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ratio Table -->
            <div id="ratioContainer" class="space-y-2">
                <h3 class="font-medium">K-Pack Ratios</h3>
                <button type="button" onclick="addRatioRow()" class="bg-black text-white px-3 py-1 rounded-lg">
                    + Add Size
                </button>
                <div class="overflow-x-auto">
                    <table class="min-w-full border mt-2 text-center" id="ratioTable">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-2">Size</th>
                            <th class="border px-2">Ratio</th>
                            <th class="border px-2">Actual Qty</th>
                            <th class="border px-2">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $ratioIndex = 0; @endphp
                        @foreach(old('ratios', isset($order) ? $order->ratios->toArray() : []) as $ratio)
                            <tr>
                                <td class="border">
                                    <input type="text" name="ratios[{{ $ratioIndex }}][size_name]"
                                           value="{{ $ratio['size_name'] ?? '' }}"
                                           class="p-1 border rounded" oninput="updateRatios()">
                                </td>
                                <td class="border">
                                    <input type="number" name="ratios[{{ $ratioIndex }}][ratio]" min="0"
                                           value="{{ $ratio['ratio'] ?? 0 }}"
                                           class="p-1 border rounded ratioInput" oninput="updateRatios()">
                                </td>
                                <td class="border actualCell">
                                    <input type="number" name="ratios[{{ $ratioIndex }}][actual_qty]"
                                           value="{{ $ratio['actual_qty'] ?? 0 }}"
                                           readonly class="p-1 border rounded bg-gray-100 w-full">
                                </td>
                                <td class="border">
                                    <button type="button" onclick="this.closest('tr').remove(); updateRatios();" class="text-red-500">Remove</button>
                                </td>
                            </tr>
                            @php $ratioIndex++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section 3: Extra Usage -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold border-b pb-2">Extra Usage</h2>
            <button type="button" onclick="addExtraRow()" class="bg-black text-white px-3 py-1 rounded-lg">
                + Add Usage
            </button>
            <div class="overflow-x-auto">
                <table class="min-w-full border mt-2 text-center" id="extraTable">
                    <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-2">Name</th>
                        <th class="border px-2">%</th>
                        <th class="border px-2">Value</th>
                        <th class="border px-2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $extraIndex = 0; @endphp
                    @foreach(old('extras', isset($order) ? $order->extras->toArray() : []) as $extra)
                        <tr>
                            <td class="border">
                                <input type="text" name="extras[{{ $extraIndex }}][name]"
                                       value="{{ $extra['name'] ?? '' }}"
                                       class="p-1 border rounded extraName" oninput="updateFinalTotal()">
                            </td>
                            <td class="border">
                                <input type="number" name="extras[{{ $extraIndex }}][percent]" min="0"
                                       value="{{ $extra['percent'] ?? 0 }}"
                                       class="p-1 border rounded percentInput" oninput="updateFinalTotal()">
                            </td>
                            <td class="border extraValue">
                                <input type="number" name="extras[{{ $extraIndex }}][value]"
                                       value="{{ $extra['value'] ?? 0 }}"
                                       readonly class="p-1 border rounded bg-gray-100 w-full">
                            </td>
                            <td class="border">
                                <button type="button" onclick="this.closest('tr').remove(); updateFinalTotal();" class="text-red-500">Remove</button>
                            </td>
                        </tr>
                        @php $extraIndex++; @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                {{ isset($order) ? 'Update' : 'Submit' }}
            </button>
        </div>

        <!-- Totals Section -->
        <section>
            <h2 class="text-xl font-semibold border-b pb-2">Totals</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border text-center" id="totalTable">
                    <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-1">Body Color</th>
                        <th class="border px-2">Pack</th>
                        <th class="border px-5">Ratios</th>
                        <th class="border px-1">Actual</th>
                        <th class="border px-1">Final Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border" id="totalColor">{{ old('body_color', $order->body_color ?? '-') }}</td>
                        <td class="border" id="totalPack">
                            @if(isset($order) && $order->pack) {{ $order->pack->name }} @else - @endif
                        </td>
                        <td class="border" id="totalRatios">-</td>
                        <td class="border" id="actualTotal">0</td>
                        <td class="border font-bold" id="finalTotalDisplay">{{ old('finalTotal', $order->finalTotal ?? 0) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <input type="hidden" id="finalTotal" name="finalTotal"
               value="{{ old('finalTotal', $order->finalTotal ?? 0) }}">
    </form>
</div>


@endsection

@section('scripts')
<script>
    let ratioIndex = {{ $ratioIndex ?? 0 }};
    function addRatioRow() {
        const table = document.getElementById('ratioTable').querySelector('tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border"><input type="text" name="ratios[${ratioIndex}][size_name]" placeholder="Size" class="p-1 border rounded" oninput="updateRatios()"></td>
            <td class="border"><input type="number" name="ratios[${ratioIndex}][ratio]" min="0" value="0" class="p-1 border rounded ratioInput" oninput="updateRatios()"></td>
            <td class="border actualCell"><input type="number" name="ratios[${ratioIndex}][actual_qty]" value="0" readonly class="p-1 border rounded bg-gray-100 w-full"></td>
            <td class="border"><button type="button" onclick="this.closest('tr').remove(); updateRatios();" class="text-red-500">Remove</button></td>
        `;
        table.appendChild(row);
        ratioIndex++;
    }

    function updateRatios() {
        const orderQty = parseInt(document.getElementById('orderQty').value || 0);
        let ratioSum = 0;
        let ratioDetails = [];
        const rows = document.querySelectorAll('#ratioTable tbody tr');

        rows.forEach(row => {
            const sizeName = row.querySelector('input[type="text"]').value || '-';
            const ratioInput = parseInt(row.querySelector('.ratioInput').value || 0);
            ratioSum += ratioInput;
            ratioDetails.push(`${sizeName}: ${ratioInput}`);
        });

        document.getElementById('totalRatios').textContent = ratioDetails.join(', ') || '-';

        let actualTotal = 0;
        rows.forEach(row => {
            const ratioInput = parseInt(row.querySelector('.ratioInput').value || 0);
            const actualQty = ratioSum > 0 ? Math.floor((orderQty / ratioSum) * ratioInput) : 0;
            row.querySelector('.actualCell input').value = actualQty;
            actualTotal += actualQty;
        });

        document.getElementById('actualTotal').textContent = actualTotal;
        updateFinalTotal();
    }

    let extraIndex = {{ $extraIndex ?? 0 }};
    function addExtraRow() {
        const table = document.getElementById('extraTable').querySelector('tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border"><input type="text" name="extras[${extraIndex}][name]" placeholder="Name" class="p-1 border rounded extraName" oninput="updateFinalTotal()"></td>
            <td class="border"><input type="number" name="extras[${extraIndex}][percent]" min="0" value="0" class="p-1 border rounded percentInput" oninput="updateFinalTotal()"></td>
            <td class="border extraValue"><input type="number" name="extras[${extraIndex}][value]" value="0" readonly class="p-1 border rounded bg-gray-100 w-full"></td>
            <td class="border"><button type="button" onclick="this.closest('tr').remove(); updateFinalTotal();" class="text-red-500">Remove</button></td>
        `;
        table.appendChild(row);
        extraIndex++;
        updateFinalTotal();
    }

    function updateFinalTotal() {
        let baseTotal = parseInt(document.getElementById('actualTotal').textContent) || 0;
        let extraTotal = 0;

        document.querySelectorAll('#extraTable tbody tr').forEach(row => {
            const percent = parseFloat(row.querySelector('.percentInput').value || 0);
            const value = Math.floor((baseTotal * percent) / 100);
            row.querySelector('.extraValue input').value = value;
            extraTotal += value;
        });

        document.getElementById('finalTotalDisplay').textContent = baseTotal + extraTotal;
        document.getElementById('finalTotal').value = baseTotal + extraTotal;
    }

    function updateTotals() {
        document.getElementById('totalColor').textContent = document.getElementById('color').value || '-';
        const packSelect = document.getElementById('packSelect');
        if (packSelect && packSelect.selectedIndex > 0) {
            document.getElementById('totalPack').textContent = packSelect.options[packSelect.selectedIndex].text;
        } else {
            document.getElementById('totalPack').textContent = '-';
        }
    }

    // Auto-run updates on page load for edit mode
    document.addEventListener('DOMContentLoaded', function () {
        updateRatios();
        updateFinalTotal();
        updateTotals();
    });
</script>
@endsection
