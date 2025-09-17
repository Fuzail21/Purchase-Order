@extends('layouts.app')
@section('content')

    <h1 class="text-3xl font-bold text-center">
        {{ isset($order) ? 'Edit Order' : 'Order Entry Form' }}
    </h1>

    {{-- Form for creating or updating an order. It supports file uploads. --}}
    <form action="{{ isset($order) ? route('orders.update', $order->id) : route('orders.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($order))
            @method('POST') {{-- because you defined update route as POST --}}
        @endif

        {{-- Section for basic order details like job number, style number, etc. --}}
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
                       
                {{-- Order Quantity input that triggers all calculations on change --}}
                <input type="number" name="order_qty" id="orderQty"
                       value="{{ old('order_qty', $order->order_qty ?? '') }}"
                       placeholder="Order Qty" class="border rounded-lg p-2" oninput="updateAllTotals()">


                <input type="text" name="title"
                       value="{{ old('title', $order->title ?? '') }}"
                       placeholder="Title" class="border rounded-lg p-2 col-span-full">

                <textarea name="description" placeholder="Description"
                          class="border rounded-lg p-2 col-span-full"
                          rows="3">{{ old('description', $order->description ?? '') }}</textarea>

                <input type="text" name="po_label"
                       value="{{ old('po_label', $order->po_label ?? '') }}"
                       placeholder="PO Label" class="border rounded-lg p-2">

                <input type="text" name="care_label"
                       value="{{ old('care_label', $order->care_label ?? '') }}"
                       placeholder="Care Label" class="border rounded-lg p-2">

                <input type="file" name="file" class="border rounded-lg p-2 col-span-full">
                @if(isset($order) && $order->file_path)
                    <a href="{{ asset('storage/'.$order->file_path) }}" target="_blank" class="text-blue-600">
                        View Current File
                    </a>
                @endif
            </div>
        </section>


        {{-- Section for managing colors, packs, and ratios --}}
        <section class="space-y-4">
            <h2 class="text-xl font-semibold border-b pb-2">Colors, Packs & Ratios</h2>
            <button type="button" onclick="addColor()" class="bg-black text-white px-3 py-1 rounded-lg" id="addColorBtn">+ Add Color</button>
            <div id="colorContainer" class="space-y-4 mt-4">
                {{-- Loop through existing colors and packs for editing mode --}}
                @if(isset($order) && $order->colors->count() > 0)
                    @foreach($order->colors as $color)
                        <div class="border p-4 rounded-lg space-y-3" data-color-index="{{ $loop->index }}">
                            <div class="flex gap-2 items-center" data-color-index="{{ $loop->index }}">
                                <input type="text" name="colors[{{ $loop->index }}][color_name]" placeholder="Color"
                                       value="{{ old('colors.'.$loop->index.'.color_name', $color->color_name) }}"
                                       class="border p-2 rounded w-1/2">
                                <select name="colors[{{ $loop->index }}][size_group]" class="border p-2 rounded w-1/2">
                                    @foreach($sizeGroups as $group)
                                        <option value="{{ $group->id }}" @if($color->size_group_id == $group->id) selected @endif>
                                            {{ $group->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                        onclick="this.closest('[data-color-index]').remove(); updateAllTotals();"
                                        class="bg-red-500 text-white px-2 py-1 rounded">
                                    Remove
                                </button>
                            </div>

                            <div class="space-y-2">
                                <button type="button" onclick="addPack({{ $loop->index }})"
                                        class="bg-blue-600 text-white px-3 py-1 rounded-lg">+ Add Pack</button>
                                <div id="packContainer-{{ $loop->index }}" class="space-y-3">
                                    @foreach($color->packs as $pack)
                                        <div class="border p-3 rounded-lg space-y-2">
                                            <div class="flex gap-2 items-center border p-2 rounded">
                                                <select name="colors[{{ $loop->parent->index }}][packs][{{ $loop->index }}][pack_name]"
                                                        class="border p-2 rounded w-1/2">
                                                    @foreach($packs as $p)
                                                        <option value="{{ $p->id }}" @if($pack->pack_id == $p->id) selected @endif>
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input type="number"
                                                       name="colors[{{ $loop->parent->index }}][packs][{{ $loop->index }}][pack_qty]"
                                                       placeholder="Pack Qty"
                                                       value="{{ old('colors.'.$loop->parent->index.'.packs.'.$loop->index.'.pack_qty', $pack->pack_qty) }}"
                                                       class="border p-2 rounded w-1/2"
                                                       oninput="updateAllTotals()">

                                                <button type="button"
                                                        onclick="this.closest('div.border').remove(); updateAllTotals();"
                                                        class="bg-red-500 text-white px-2 py-1 rounded">
                                                    Remove
                                                </button>
                                            </div>
                                            <div class="space-y-2">
                                                <button type="button" onclick="addRatio({{ $loop->parent->index }}, {{ $loop->index }})" class="bg-green-600 text-white px-3 py-1 rounded-lg">+ Add Ratio</button>
                                                <table class="min-w-full border mt-2 text-center">
                                                    <thead class="bg-gray-200">
                                                        <tr>
                                                            <th class="border px-2">Size</th>
                                                            <th class="border px-2">Ratio</th>
                                                            <th class="border px-2">Actual Qty</th>
                                                            <th class="border px-2">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="ratioContainer-{{ $loop->parent->index }}-{{ $loop->index }}">
                                                        @foreach($pack->ratios as $ratio)
                                                            <tr>
                                                                <td class="border"><input type="text" name="colors[{{ $loop->parent->parent->index }}][packs][{{ $loop->parent->index }}][ratios][{{ $loop->index }}][size_name]"
                                                                                          value="{{ old('colors.'.$loop->parent->parent->index.'.packs.'.$loop->parent->index.'.ratios.'.$loop->index.'.size_name', $ratio->size_name) }}"
                                                                                          class="w-full p-1 border rounded"></td>
                                                                <td class="border"><input type="number" name="colors[{{ $loop->parent->parent->index }}][packs][{{ $loop->parent->index }}][ratios][{{ $loop->index }}][ratio]"
                                                                                          value="{{ old('colors.'.$loop->parent->parent->index.'.packs.'.$loop->parent->index.'.ratios.'.$loop->index.'.ratio', $ratio->ratio) }}"
                                                                                          class="w-full p-1 border rounded" oninput="updateAllTotals()"></td>
                                                                <td class="border"><input type="number" name="colors[{{ $loop->parent->parent->index }}][packs][{{ $loop->parent->index }}][ratios][{{ $loop->index }}][actual_qty]"
                                                                                          value="{{ old('colors.'.$loop->parent->parent->index.'.packs.'.$loop->parent->index.'.ratios.'.$loop->index.'.actual_qty', $ratio->actual_qty) }}"
                                                                                          readonly class="w-full p-1 border rounded bg-gray-100"></td>
                                                                <td class="border"><button type="button" onclick="this.closest('tr').remove(); updateAllTotals();" class="bg-red-500 text-white px-2 py-1 rounded">X</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="space-y-2">
                                                <h4 class="font-semibold text-sm">Pack Extra Usage</h4>
                                                <div class="flex gap-2">
                                                     <input type="number" name="colors[{{ $loop->parent->index }}][packs][{{ $loop->index }}][pack_extra_percent]" 
                                                           placeholder="Extra %" min="0" value="{{ old('colors.'.$loop->parent->index.'.packs.'.$loop->index.'.pack_extra_percent', $pack->pack_extra_percent ?? 0) }}" 
                                                           class="border p-2 rounded w-1/2 percentInput" oninput="updateAllTotals()">
                                                    <input type="number" name="colors[{{ $loop->parent->index }}][packs][{{ $loop->index }}][pack_extra_qty]" 
                                                           placeholder="Extra Qty" value="{{ old('colors.'.$loop->parent->index.'.packs.'.($loop->index).'.pack_extra_qty', $pack->pack_extra_qty ?? 0) }}" 
                                                           readonly class="border p-2 rounded w-1/2 bg-gray-100 extraValue">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <p id="limitReachedMsg" class="text-red-500 font-semibold hidden">Order quantity limit reached. Cannot add more packs.</p>
        </section>

        {{-- Section for overall extra usage --}}
        {{-- This section is commented out but left in place --}}
        {{-- <section class="space-y-4">
            <h2 class="text-xl font-semibold border-b pb-2">Extra Usage (Overall Order)</h2>
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
                                       class="p-1 border rounded extraName">
                            </td>
                            <td class="border">
                                <input type="number" name="extras[{{ $extraIndex }}][percent]" min="0"
                                       value="{{ $extra['percent'] ?? 0 }}"
                                       class="p-1 border rounded overallPercentInput" oninput="updateAllTotals()">
                            </td>
                            <td class="border overallExtraValue">
                                <input type="number" name="extras[{{ $extraIndex }}][value]"
                                       value="{{ $extra['value'] ?? 0 }}"
                                       readonly class="p-1 border rounded bg-gray-100 w-full">
                            </td>
                            <td class="border">
                                <button type="button" onclick="this.closest('tr').remove(); updateAllTotals();" class="text-red-500">Remove</button>
                            </td>
                        </tr>
                        @php $extraIndex++; @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section> --}}

        {{-- Submit button --}}
        <div class="flex justify-end pt-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                {{ isset($order) ? 'Update' : 'Submit' }}
            </button>
        </div>

        {{-- Totals display section --}}
        <section>
            <h2 class="text-xl font-semibold border-b pb-2">Totals</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border text-center" id="totalTable">
                    <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-1">Order Qty</th>
                        <th class="border px-1">Actual Total</th>
                        <th class="border px-1">Extra Usage</th>
                        <th class="border px-1">Final Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border" id="totalOrderQty">0</td>
                        <td class="border" id="actualTotal">0</td>
                        <td class="border" id="totalExtraUsage">0</td>
                        <td class="border font-bold" id="finalTotalDisplay">0</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        {{-- Hidden input to store the final total value for form submission --}}
        <input type="hidden" id="finalTotal" name="finalTotal"
               value="{{ old('finalTotal', $order->finalTotal ?? 0) }}">
    </form>
</div>


@endsection

@section('scripts')
<script>
    // Set the initial index based on the number of existing colors
    let colorIndex = {{ isset($order) ? $order->colors->count() : 0 }};
    let extraIndex = {{ $extraIndex ?? 0 }};

    // Add new color block dynamically to the DOM
    function addColor() {
        const orderQty = parseInt(document.getElementById('orderQty').value) || 0;
        const currentTotalPackQty = getCurrentTotalPackQty();
        if (orderQty > 0 && currentTotalPackQty >= orderQty) {
            document.getElementById('limitReachedMsg').classList.remove('hidden');
            return;
        }

        const colorContainer = document.getElementById('colorContainer');
        const colorBlock = document.createElement('div');
        colorBlock.classList.add('border', 'p-4', 'rounded-lg', 'space-y-3');
        colorBlock.setAttribute('data-color-index', colorIndex);

        colorBlock.innerHTML = `
            <div class="flex gap-2 items-center" data-color-index="${colorIndex}">
                <input type="text" name="colors[${colorIndex}][color_name]" placeholder="Color" 
                       class="border p-2 rounded w-1/2">

                <select name="colors[${colorIndex}][size_group]" class="border p-2 rounded w-1/2">
                    @foreach($sizeGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                    @endforeach
                </select>

                <button type="button" 
                        onclick="this.closest('[data-color-index]').remove(); updateAllTotals();" 
                        class="bg-red-500 text-white px-2 py-1 rounded">
                    Remove
                </button>
            </div>

            <div class="space-y-2">
                <button type="button" onclick="addPack(${colorIndex})" 
                        class="bg-blue-600 text-white px-3 py-1 rounded-lg">+ Add Pack</button>
                <div id="packContainer-${colorIndex}" class="space-y-3"></div>
            </div>
        `;


        colorContainer.appendChild(colorBlock);
        colorIndex++;
    }

    // Add pack block under a specific color dynamically
    function addPack(colorIndex) {
        const orderQty = parseInt(document.getElementById('orderQty').value) || 0;
        const currentTotalPackQty = getCurrentTotalPackQty();
        if (orderQty > 0 && currentTotalPackQty >= orderQty) {
            document.getElementById('limitReachedMsg').classList.remove('hidden');
            return;
        }

        const packContainer = document.getElementById(`packContainer-${colorIndex}`);
        const packIndex = packContainer.children.length;

        const packBlock = document.createElement('div');
        packBlock.classList.add('border', 'p-3', 'rounded-lg', 'space-y-2');
        packBlock.innerHTML = `
            <div class="flex gap-2 items-center border p-2 rounded">
                <select name="colors[${colorIndex}][packs][${packIndex}][pack_name]" 
                        class="border p-2 rounded w-1/2">
                    @foreach($packs as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->name }}</option>
                    @endforeach
                </select>
            
                <input type="number" 
                       name="colors[${colorIndex}][packs][${packIndex}][pack_qty]" 
                       placeholder="Pack Qty" 
                       class="border p-2 rounded w-1/2" 
                       oninput="updateAllTotals()">
            
                <button type="button" 
                        onclick="this.closest('div.border').remove(); updateAllTotals();" 
                        class="bg-red-500 text-white px-2 py-1 rounded">
                    Remove
                </button>
            </div>


            <div class="space-y-2">
                <button type="button" onclick="addRatio(${colorIndex}, ${packIndex})" class="bg-green-600 text-white px-3 py-1 rounded-lg">+ Add Ratio</button>
                <table class="min-w-full border mt-2 text-center">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-2">Size</th>
                            <th class="border px-2">Ratio</th>
                            <th class="border px-2">Actual Qty</th>
                            <th class="border px-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ratioContainer-${colorIndex}-${packIndex}"></tbody>
                </table>
            </div>

            <div class="space-y-2">
                <h4 class="font-semibold text-sm">Pack Extra Usage</h4>
                <div class="flex gap-2">
                    <input type="number" name="colors[${colorIndex}][packs][${packIndex}][pack_extra_percent]" 
                           placeholder="Extra %" min="0" value="0" 
                           class="border p-2 rounded w-1/2 percentInput" oninput="updateAllTotals()">
                    <input type="number" name="colors[${colorIndex}][packs][${packIndex}][pack_extra_qty]" 
                           placeholder="Extra Qty" value="0" 
                           readonly class="border p-2 rounded w-1/2 bg-gray-100 extraValue">
                </div>
            </div>
        `;

        packContainer.appendChild(packBlock);
    }

    // Add a new ratio row under a specific pack
    function addRatio(colorIndex, packIndex) {
        const ratioContainer = document.getElementById(`ratioContainer-${colorIndex}-${packIndex}`);
        const ratioCount = ratioContainer.children.length;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border">
                <input type="text" 
                       name="colors[${colorIndex}][packs][${packIndex}][ratios][${ratioCount}][size_name]" 
                       class="w-full p-1 border rounded"
                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); }">
            </td>

            <td class="border">
                <input type="number" 
                       name="colors[${colorIndex}][packs][${packIndex}][ratios][${ratioCount}][ratio]" 
                       class="w-full p-1 border rounded" 
                       oninput="updateAllTotals()" 
                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addRatio(${colorIndex}, ${packIndex}); }">
            </td>

            <td class="border"><input type="number" name="colors[${colorIndex}][packs][${packIndex}][ratios][${ratioCount}][actual_qty]" value="0" readonly class="w-full p-1 border rounded bg-gray-100"></td>
            <td class="border"><button type="button" onclick="this.closest('tr').remove(); updateAllTotals();" class="bg-red-500 text-white px-2 py-1 rounded">X</button></td>
        `;
        ratioContainer.appendChild(row);
    }

    // This is the commented-out function for adding an overall extra row.
    /*function addExtraRow() {
        const table = document.getElementById('extraTable').querySelector('tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border">
                <input type="text" name="extras[${extraIndex}][name]" placeholder="Name" class="p-1 border rounded extraName">
            </td>
            <td class="border">
                <input type="number" name="extras[${extraIndex}][percent]" min="0" value="0"
                       class="p-1 border rounded overallPercentInput" oninput="updateAllTotals()">
            </td>
            <td class="border overallExtraValue">
                <input type="number" name="extras[${extraIndex}][value]" value="0" readonly class="p-1 border rounded bg-gray-100 w-full">
            </td>
            <td class="border">
                <button type="button" onclick="this.closest('tr').remove(); updateAllTotals();" class="text-red-500">Remove</button>
            </td>
        `;
        table.appendChild(row);
        extraIndex++;
        updateAllTotals();
    } */

    // Helper function to get the sum of all pack quantities
    function getCurrentTotalPackQty() {
        let totalPackQty = 0;
        document.querySelectorAll('[name^="colors"][name$="[pack_qty]"]').forEach(input => {
            totalPackQty += parseInt(input.value) || 0;
        });
        return totalPackQty;
    }


    // Main function to update all calculations on the page
    function updateAllTotals() {
        const orderQtyInput = document.getElementById('orderQty');
        const orderQty = parseInt(orderQtyInput.value) || 0;
        let totalPackQty = 0;
        let totalActualQty = 0;
        let totalExtraUsage = 0;

        // Iterate through each color block
        document.querySelectorAll('[data-color-index]').forEach(colorBlock => {
            // Iterate through each pack block within the color
            colorBlock.querySelectorAll('div.border.p-3.rounded-lg').forEach(packBlock => {
                let packQtyInput = packBlock.querySelector('[name^="colors"][name$="[pack_qty]"]');
                let packQty = parseInt(packQtyInput.value) || 0;
                
                const currentTotalExcludingThis = getCurrentTotalPackQty() - packQty;
                if (orderQty > 0 && currentTotalExcludingThis + packQty > orderQty) {
                    packQty = orderQty - currentTotalExcludingThis;
                    packQtyInput.value = packQty;
                }
                
                totalPackQty += packQty;

                let packRatioSum = 0;
                let ratioInputs = packBlock.querySelectorAll('[name^="colors"][name$="[ratio]"]');

                // Sum the ratios for this pack first
                ratioInputs.forEach(ratioInput => {
                    packRatioSum += parseInt(ratioInput.value) || 0;
                });

                // Calculate the pack-specific extra quantity
                const packPercent = parseFloat(packBlock.querySelector('[name$="[pack_extra_percent]"]').value) || 0;
                const packExtraQty = Math.floor((packQty * packPercent) / 100);
                packBlock.querySelector('[name$="[pack_extra_qty]"]').value = packExtraQty;
                totalExtraUsage += packExtraQty;

                // The new total quantity for this pack is the pack_qty plus the extra qty
                const totalPackQtyWithExtra = packQty + packExtraQty;

                // Distribute this new total quantity across the ratios
                packBlock.querySelectorAll('tbody tr').forEach(ratioRow => {
                    let ratioValue = parseInt(ratioRow.querySelector('[name^="colors"][name$="[ratio]"]').value) || 0;
                    let actualQty = packRatioSum > 0 ? Math.floor((totalPackQtyWithExtra / packRatioSum) * ratioValue) : 0;
                    
                    ratioRow.querySelector('[name^="colors"][name$="[actual_qty]"]').value = actualQty;
                    
                    totalActualQty += actualQty;
                });
            });
        });
        
        // Update the order quantity display and button status
        document.getElementById('totalOrderQty').textContent = totalPackQty;
        const addColorBtn = document.getElementById('addColorBtn');
        const limitMsg = document.getElementById('limitReachedMsg');

        if (orderQty > 0 && totalPackQty >= orderQty) {
            addColorBtn.disabled = true;
            limitMsg.classList.remove('hidden');
        } else {
            addColorBtn.disabled = false;
            limitMsg.classList.add('hidden');
        }

        // Calculate and sum the overall extra usage from the commented out section
        document.querySelectorAll('#extraTable tbody tr').forEach(row => {
            const percent = parseFloat(row.querySelector('.overallPercentInput').value) || 0;
            const value = Math.floor((totalActualQty * percent) / 100);
            row.querySelector('.overallExtraValue input').value = value;
            totalExtraUsage += value;
        });

        // Calculate and update the final total
        const finalTotal = totalActualQty + totalExtraUsage;
        document.getElementById('actualTotal').textContent = totalActualQty;
        document.getElementById('totalExtraUsage').textContent = totalExtraUsage;
        document.getElementById('finalTotalDisplay').textContent = finalTotal;
        document.getElementById('finalTotal').value = finalTotal;
    }

    // Auto-run updates on page load for edit mode
    document.addEventListener('DOMContentLoaded', function () {
        updateAllTotals();
    });

</script>
@endsection