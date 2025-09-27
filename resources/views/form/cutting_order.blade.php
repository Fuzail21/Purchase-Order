<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cutting Order Program</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }
        .input-text {
            @apply border border-gray-300 rounded-lg p-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200;
        }
        .table-input {
            transition: all 0.2s;
        }
        .table-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }
        .btn {
            @apply px-4 py-2 rounded-lg font-semibold shadow transition-transform transform hover:scale-105;
        }
        .btn-primary {
            @apply bg-indigo-600 text-white hover:bg-indigo-700;
        }
        .btn-danger {
            @apply bg-red-500 text-white hover:bg-red-600;
        }
        .btn-secondary {
            @apply bg-gray-600 text-white hover:bg-gray-700;
        }
        [type="date"]::-webkit-calendar-picker-indicator {
            background: transparent;
            color: transparent;
            cursor: pointer;
        }
        [type="date"] {
            position: relative;
        }
        [type="date"]::before {
            content: attr(placeholder);
            color: #9ca3af;
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }
        [type="date"]:valid::before {
            content: '';
        }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold text-center text-gray-800">Cutting Order Program</h1>
        <form action="{{ isset($order) ? route('orders.update', $order->id) : route('orders.store') }}" 
              method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($order))
                @method('PUT')
            @endif
            <!-- Purchase Order Details Section -->
            <section class="bg-white p-6 rounded-xl shadow-lg space-y-6">
                <!-- Title -->
                <h2 class="text-xl font-semibold border-b pb-2 text-gray-700">Purchase Order Details</h2>

                <!-- Grid Fields -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Job No -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Job #</label>
                        <input type="text" name="job_no"
                               value="{{ old('job_no', $order->job_no ?? '') }}"
                               placeholder="Enter Job #" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Style No -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Style #</label>
                        <input type="text" name="style_no"
                               value="{{ old('style_no', $order->style_no ?? '') }}"
                               placeholder="Enter Style #" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- PO Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">PO Date</label>
                        <input type="text" name="po_date"
                               value="{{ old('po_date', $order->po_date ?? '') }}"
                               placeholder="Select PO Date" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
                    </div>

                    <!-- Ship Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Ship Date</label>
                        <input type="text" name="ship_date" required
                               value="{{ old('ship_date', $order->ship_date ?? '') }}"
                               placeholder="Select Ship Date" 
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
                    </div>

                    <!-- Fabrics -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Fabrics</label>
                        <input type="text" name="fabrics" required
                               value="{{ old('fabrics', $order->fabrics ?? '') }}"
                               placeholder="Enter Fabrics" 
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- GSM -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">GSM</label>
                        <input type="number" name="gsm" required
                               value="{{ old('gsm', $order->gsm ?? '') }}"
                               placeholder="Enter GSM" 
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Buyer -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Buyer</label>
                        <input type="text" name="buyer" required
                               value="{{ old('buyer', $order->buyer ?? '') }}"
                               placeholder="Enter Buyer" 
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Order Qty -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Order Qty</label>
                        <input type="number" name="order_qty" id="orderQty"
                               value="{{ old('order_qty', $order->order_qty ?? '') }}"
                               placeholder="Enter Order Quantity" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               oninput="updateAllTotals()">
                    </div>

                    <!-- Title (full width) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Title</label>
                        <input type="text" name="title"
                               value="{{ old('title', $order->title ?? '') }}"
                               placeholder="Enter Title" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Description (full width) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Description</label>
                        <input type="text" name="description"
                               value="{{ old('description', $order->description ?? '') }}"
                               placeholder="Enter Description" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>


                    <!-- PO Label -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">PO Label</label>
                        <input type="text" name="po_label"
                               value="{{ old('po_label', $order->po_label ?? '') }}"
                               placeholder="Enter PO Label" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Care Label -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Care Label</label>
                        <input type="text" name="care_label"
                               value="{{ old('care_label', $order->care_label ?? '') }}"
                               placeholder="Enter Care Label" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Store Name</label>
                        <input type="text" name="store_name"
                               value="{{ old('store_name', $order->store_name ?? '') }}"
                               placeholder="Enter Store Name" required
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- File Upload (full width) -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Upload File</label>
                        <input type="file" 
                               name="file"
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               accept="image/jpeg, image/jpg, image/png, image/webp">

                        @if(isset($order) && $order->file_path)
                            <a href="{{ asset('storage/'.$order->file_path) }}" target="_blank" class="text-blue-600 text-sm mt-2 inline-block">
                                View Current File
                            </a>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">PO</label>
                        <input type="file" 
                               name="po_file"
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               accept="image/jpeg, image/jpg, image/png, image/webp, application/pdf">

                        @if(isset($order) && $order->po_file)
                            <a href="{{ asset('storage/'.$order->po_file) }}" target="_blank" class="text-blue-600 text-sm mt-2 inline-block">
                                View PO File
                            </a>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Tag Pack</label>
                        <input type="file" 
                               name="tag_pack"
                               class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500"
                               accept="image/jpeg, image/jpg, image/png, image/webp, application/pdf">

                        @if(isset($order) && $order->tag_pack)
                            <a href="{{ asset('storage/'.$order->tag_pack) }}" target="_blank" class="text-blue-600 text-sm mt-2 inline-block">
                                View Tag Pack
                            </a>
                        @endif
                    </div>
                </div>

            </section>


            <!-- Overall Totals Display -->
            <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center flex-wrap gap-4">
                <div class="flex flex-col items-center">
                    <span class="text-xs font-medium text-gray-500">Order Quantity</span>
                    <span id="totalOrderQty" class="text-xl font-bold text-gray-900">0</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-xs font-medium text-gray-500">Actual Total</span>
                    <span id="actualTotal" class="text-xl font-bold text-gray-900">0</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-xs font-medium text-gray-500">Extra Usage</span>
                    <span id="totalExtraUsage" class="text-xl font-bold text-red-600">0</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-xs font-medium text-gray-500">Final Total</span>
                    <span id="finalTotalDisplay" class="text-xl font-bold text-indigo-600">0</span>
                    <input type="hidden" name="finalTotal" id="finalTotal" value="0">
                </div>
            </div>

            
            <!-- Dynamic Packs Container -->
            <div id="packsContainer" class="space-y-8">
                <!-- Pack blocks will be dynamically added here -->
            </div>

            <!-- Limit Message -->
                <div id="limitReachedMsg" 
                     class="p-2 bg-red-100 text-red-700 rounded-lg text-sm text-center font-semibold hidden">
                    Order quantity limit reached. Cannot add more items.
                </div>

            <!-- Add Pack Section -->
            <div class="bg-white p-6 rounded-xl shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-gray-700">Add a New Pack</h2>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <select id="sizeGroupSelector" class="w-full md:w-36 p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options for size groups -->
                    </select>
                    <select id="packSelector" class="w-full md:w-48 p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    <button type="button" id="addPackBtn" class="btn btn-primary w-full md:w-auto">Add Pack</button>
                </div>
            </div>

            <button type="submit" id="formSubmit" 
                class="px-6 py-2 font-semibold text-sm rounded-xl shadow-md 
                       focus:ring-2 focus:ring-offset-2 transition-all duration-300 ease-in-out w-full md:w-auto" disabled>
            Save Order</button>
        <form>
    </div>

    <script>
const simulatedDB = @json($simulatedDB);

const packsContainer = document.getElementById('packsContainer');
const sizeGroupSelector = document.getElementById('sizeGroupSelector');
const packSelector = document.getElementById('packSelector');
const addPackBtn = document.getElementById('addPackBtn');

let packIndex = 0;


// Save order qty input in variable
const orderQtyInput = document.getElementById("orderQty");


// The form submit button
const formSubmit = document.getElementById("formSubmit");


// Validation message placeholder
let errorMsg = document.createElement("p");
errorMsg.className = "text-red-600 text-sm mt-1 hidden";
errorMsg.id = "qtyErrorMsg";
packsContainer.parentNode.appendChild(errorMsg);

// Function to calculate total color qty
function calculateTotalColorQty() {
    let total = 0;
    const qtyInputs = document.querySelectorAll(
        'input[name*="[qty]"]'
    );
    qtyInputs.forEach(input => {
        const val = parseInt(input.value) || 0;
        total += val;
    });
    return total;
}

// Function to validate against order qty
function validateTotalQty() {
    const orderQty = parseInt(orderQtyInput.value) || 0;
    const totalQty = calculateTotalColorQty();


    if (totalQty > orderQty) {
        errorMsg.textContent = `❌ Total Color quantity (${totalQty}) cannot exceed Order Qty (${orderQty}).`;
        errorMsg.classList.remove("hidden");
        return false;
    } else {
        errorMsg.classList.add("hidden");
        return true;
    }
}

function checkFormValidity() {
    let hasValidPack = false;
    let hasValidColor = false;

    document.querySelectorAll('[id^="packBlock_"]').forEach(packBlock => {
        const colorTableBody = packBlock.querySelector("tbody");
        if (colorTableBody && colorTableBody.children.length > 0) {
            hasValidPack = true;
            if (colorTableBody.children.length > 0) {
                hasValidColor = true;
            }
        }
    });

    const qtyValid = validateTotalQty(); // run your qty check

    if (hasValidPack && hasValidColor && qtyValid) {
        formSubmit.disabled = false;
        formSubmit.classList.remove('bg-black', 'text-white', 'hover:bg-gray-900', 'focus:ring-gray-500');
        formSubmit.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700', 'focus:ring-green-500');
    } else {
        formSubmit.disabled = true;
        formSubmit.classList.remove('bg-green-600', 'text-white', 'hover:bg-green-700', 'focus:ring-green-500');
        formSubmit.classList.add('bg-black', 'text-white', 'hover:bg-gray-900', 'focus:ring-gray-500');
    }
}


// Attach event listeners to dynamically added qty fields
function attachQtyValidation() {
    const qtyInputs = document.querySelectorAll('input[name*="[qty]"]');
    qtyInputs.forEach(input => {
        input.removeEventListener("input", validateTotalQty); // prevent duplicate
        input.addEventListener("input", validateTotalQty);
    });
}

// Update totals when order qty changes
orderQtyInput.addEventListener("input", validateTotalQty);


// Initialize size group dropdown
simulatedDB.sizeGroups.forEach(group => {
    const option = document.createElement('option');
    option.value = group.id;
    option.textContent = group.name;
    sizeGroupSelector.appendChild(option);
});

// Populate packs based on size group
function populatePackSelector() {
    const selectedGroup = sizeGroupSelector.value;
    packSelector.innerHTML = '';

    const filteredPacks = simulatedDB.packs.filter(
        pack => String(pack.group) === String(selectedGroup)
    );

    filteredPacks.forEach(pack => {
        const option = document.createElement('option');
        option.value = pack.id;
        option.textContent = pack.name;
        packSelector.appendChild(option);
    });
}

sizeGroupSelector.addEventListener('change', populatePackSelector);

// Add Pack
addPackBtn.addEventListener('click', () => {
    const selectedPackId = packSelector.value;
    const packData = simulatedDB.packs.find(
        p => String(p.id) === String(selectedPackId)
    );
    if (packData) {
        addPack(packData);
        // This is the crucial change. Add a color row only after the button is clicked.
        addColor(packIndex - 1);
    }
    checkFormValidity();
});

// Initialize with first group
if (sizeGroupSelector.options.length > 0) {
    sizeGroupSelector.value = simulatedDB.sizeGroups[0].id;
    populatePackSelector();
}

// Add Pack block with size/ratio columns
function addPack(packData) {
    const totalRatio = Object.values(packData.ratios).reduce((a, b) => a + b, 0);

    const ratioHeaderHTML = Object.entries(packData.ratios)
        .map(([sizeId, ratio]) => {
            const size = simulatedDB.sizes.find(s => String(s.id) === String(sizeId));
            const sizeName = size ? size.name : `Size#${sizeId}`;
            return `<th class="px-2 py-1">${sizeName} (${ratio})</th>`;
        })
        .join('');

    const packBlock = document.createElement('div');
    packBlock.id = `packBlock_${packIndex}`;
    packBlock.dataset.packIndex = packIndex;
    packBlock.dataset.packId = packData.id;
    packBlock.dataset.totalRatio = totalRatio;
    packBlock.className = 'bg-white p-6 rounded-xl shadow-lg space-y-6';

    const selectedSizeGroupId = sizeGroupSelector.value;

    packBlock.innerHTML = `
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h2 class="text-xl font-bold text-gray-800">Pack ${packData.name}</h2>
            <input type="hidden" name="packs[${packIndex}][sizeGroup_id]" value="${selectedSizeGroupId}">
            <input type="hidden" name="packs[${packIndex}][pack_id]" value="${packData.id}">
            <button type="button" class="btn btn-danger btn-sm" onclick="removePack(${packIndex})">Remove Pack</button>
        </div>
        <div class="mt-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="w-full text-sm text-center">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th>Color</th>
                            <th>Pack Qty</th>
                            <th>Extra %</th>
                            ${ratioHeaderHTML}
                            <th>Total</th>
                            <th>Add-on</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="colorTable_${packIndex}"></tbody>
                    <tfoot class="bg-gray-200 font-bold">
                        <tr>
                            <td class="text-right px-2">Pack Total</td>
                            <td class="pack-qty-total">0</td>
                            <td></td>
                            <td colspan="${Object.keys(packData.ratios).length}"></td>
                            <td class="cutting-total-display">0</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" class="btn btn-primary" onclick="addColor(${packIndex})">Add Color</button>
            </div>
        </div>
    `;
    packsContainer.appendChild(packBlock);
    
    // We only increment the global index here.
    packIndex++;
    
    checkFormValidity();
}

// Add Color row
    function addColor(packIndex, colorData) {
        // Find the pack block and pack data
        const packBlock = document.getElementById(`packBlock_${packIndex}`);
        if (!packBlock) {
            console.error(`Pack block with index ${packIndex} not found.`);
            return;
        }

        const packId = packBlock.dataset.packId;
        const packData = simulatedDB.packs.find(p => String(p.id) === String(packId));
        if (!packData) {
            console.error(`Pack data with ID ${packId} not found in simulatedDB.`);
            return;
        }

        const colorTableBody = document.getElementById(`colorTable_${packIndex}`);
        const colorRowIndex = colorTableBody.children.length;

        const ratioRowHTML = Object.keys(packData.ratios)
            .map(sizeId => {
                const size = simulatedDB.sizes.find(s => String(s.id) === String(sizeId));
                const sizeName = size ? size.name : `Size#${sizeId}`;
                const calculatedQty = colorData && colorData.ratios ? colorData.ratios[sizeId] || 0 : 0;
                return `
                    <td>
                        <span class="ratio-value-display" data-size-id="${sizeId}">${calculatedQty}</span>
                        <input type="hidden" name="packs[${packIndex}][colors][${colorRowIndex}][ratios][${sizeId}]" value="${calculatedQty}" class="ratio-input-hidden" data-size-id="${sizeId}">
                    </td>
                `;
            })
            .join('');
        
        const addOnOptions = `
            <select name="packs[${packIndex}][colors][${colorRowIndex}][add_on]" class="w-24 p-1 rounded">
                <option value="">Select</option>
                @foreach ($addOns as $addOn)
                    <option value="{{ $addOn->id }}" ${colorData && colorData.add_on == '{{ $addOn->id }}' ? 'selected' : ''}>
                        {{ $addOn->name }}
                    </option>
                @endforeach
            </select>
        `;

        const colorRow = document.createElement('tr');
        colorRow.id = `colorRow_${packIndex}_${colorRowIndex}`;
        colorRow.innerHTML = `
            <td><input type="text" required name="packs[${packIndex}][colors][${colorRowIndex}][color_name]" class="w-24 text-center p-1 rounded" placeholder="Color" value="${colorData ? colorData.color_name : ''}"></td>
            <td><input type="number" required name="packs[${packIndex}][colors][${colorRowIndex}][qty]" class="w-20 text-center p-1 pack-qty-input" value="${colorData ? colorData.qty : ''}"></td> 
            <td><input type="number" name="packs[${packIndex}][colors][${colorRowIndex}][extra_usage]" class="w-20 text-center p-1 extra-usage-input" value="${colorData ? colorData.extra_usage : 0}" min="0"></td>
            ${ratioRowHTML}
            <td><span class="cutting-total-color-display font-bold">${colorData ? (colorData.qty * (1 + (colorData.extra_usage / 100))) : 0}</span></td>
            <td>${addOnOptions}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeColor(${packIndex}, ${colorRowIndex})">Remove</button></td>
        `;

        colorTableBody.appendChild(colorRow);
        checkFormValidity();
        colorRow.querySelector('.pack-qty-input').addEventListener('input', updateAllTotals);
        colorRow.querySelector('.extra-usage-input').addEventListener('input', updateAllTotals);
    }
    
    // Initial Population Script for Editing
    @if(isset($existingPacks))
        document.addEventListener('DOMContentLoaded', () => {
        const existingPacks = @json($existingPacks);
        
        existingPacks.forEach(packData => {
            // First, find the full pack details from the simulated DB
            const fullPack = simulatedDB.packs.find(p => String(p.id) === String(packData.id));
            
            if (fullPack) {
                // Add the pack block
                addPack(fullPack);
                
                // Now, add the colors to this pack block using the existing data
                packData.colors.forEach(colorData => {
                    // We need to use packIndex - 1 because addPack just incremented it
                    addColor(packIndex - 1, colorData);
                });
            } else {
                console.error(`Pack with ID ${packData.id} not found in database.`);
            }
        });

        // Run totals and validation after all elements are added
        updateAllTotals();
        attachQtyValidation();
    });
    @endif

// Remove Color
function removeColor(packIndex, colorRowIndex) {
    const row = document.getElementById(`colorRow_${packIndex}_${colorRowIndex}`);
    if (row) row.remove();
    updateAllTotals();
    checkFormValidity();
}

// Remove Pack
function removePack(packIndex) {
    const packBlock = document.getElementById(`packBlock_${packIndex}`);
    if (packBlock) packBlock.remove();
    updateAllTotals();
    checkFormValidity();
}

// Calculation + Totals
function updateAllTotals() {
    let totalExtraUsage = 0; // Declare it here to be a local variable for this function
    let finalTotal = 0;

    document.querySelectorAll('[id^="packBlock_"]').forEach(packBlock => {
        const packId = packBlock.dataset.packId;
        const packData = simulatedDB.packs.find(p => String(p.id) === String(packId));
        const totalRatio = parseFloat(packBlock.dataset.totalRatio);

        let packQtyTotal = 0;
        let packCuttingTotal = 0;
        
        const colorTableBody = packBlock.querySelector(`#colorTable_${packBlock.dataset.packIndex}`);
        colorTableBody.querySelectorAll('tr').forEach(row => {
            const packQty = parseInt(row.querySelector('.pack-qty-input').value) || 0;
            const extraUsage = parseFloat(row.querySelector('.extra-usage-input').value) || 0;
            totalExtraUsage += packQty * (extraUsage / 100);
            
            const qtyWithExtra = packQty * (1 + extraUsage / 100);
            const basePerRatio = qtyWithExtra / totalRatio;

            let totalForColor = 0;
            Object.keys(packData.ratios).forEach(sizeId => {
                const ratio = packData.ratios[sizeId];
                const calculatedQty = Math.round(basePerRatio * ratio);
                totalForColor += calculatedQty;

                const display = row.querySelector(`.ratio-value-display[data-size-id="${sizeId}"]`);
                const hidden = row.querySelector(`.ratio-input-hidden[data-size-id="${sizeId}"]`);
                if (display) display.textContent = calculatedQty;
                if (hidden) hidden.value = calculatedQty;
            });

            row.querySelector('.cutting-total-color-display').textContent = totalForColor;

            packQtyTotal += packQty;
            packCuttingTotal += totalForColor;
        });

        packBlock.querySelector('.pack-qty-total').textContent = packQtyTotal;
        packBlock.querySelector('.cutting-total-display').textContent = packCuttingTotal;
    });

        function calculateTotalQty(e) {
    let total = 0;
    const orderQtyInput = document.getElementById("orderQty");
    const orderQty = parseInt(orderQtyInput.value) || 0;
    const limitReachedMsg = document.getElementById("limitReachedMsg");

    // Get all qty inputs
    document.querySelectorAll(".pack-qty-input").forEach(input => {
        let val = parseFloat(input.value) || 0;
        total += val;
    });

    // Show in #actualTotal
    document.getElementById("actualTotal").textContent = Math.round(total);

    // Validation: actual must not exceed orderQty
    if (total > orderQty) {
        limitReachedMsg.classList.remove("hidden");
        // formSubmit.disabled = true; // disable on error
    } else {
        limitReachedMsg.classList.add("hidden");
        // formSubmit.disabled = false; // enable when valid
    }
    // if (e && e.target.classList.contains("pack-qty-input")) { // e.target.value = "0"; calculateTotalQty(); // re-run after clearing }

}

// Run once on page load
calculateTotalQty();

// Recalculate whenever input changes
document.addEventListener("input", function (e) {
    if (e.target.classList.contains("pack-qty-input")) {
        calculateTotalQty(e);
    }
});


    const finalTotalInput = document.getElementById('finalTotal');
    // Update global totals after all packs have been processed
    const orderQty = parseInt(orderQtyInput.value) || 0;
    const actualQty = orderQty;
    document.getElementById('totalOrderQty').textContent = orderQty;
    // document.getElementById('actualTotal').textContent = Math.round(actualQty);
    document.getElementById('totalExtraUsage').textContent = totalExtraUsage.toFixed(2);
    document.getElementById('finalTotalDisplay').textContent = orderQty + totalExtraUsage;
    finalTotalInput.value = Math.round(orderQty + totalExtraUsage);
    
    checkFormValidity();
}

document.addEventListener('DOMContentLoaded', () => {
    populatePackSelector();
    updateAllTotals();
    checkFormValidity();
    // The event listener is now much simpler.
    orderQtyInput.addEventListener('input', updateAllTotals);
});
</script>

</body>
</html>
