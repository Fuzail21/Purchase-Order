<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(isset($pack))
            Edit Pack
        @else
            Create New Pack
        @endif
    </title>
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
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold text-center text-gray-800">
            @if(isset($pack))
                Edit Pack: {{ $pack->name }}
            @else
                Create New Pack
            @endif
        </h1>

        <!-- Form container -->
        <form id="packForm" action="@if(isset($pack)){{ route('packs.update', $pack->id) }}@else{{ route('packs.store') }}@endif" method="POST" class="space-y-8">
            @csrf
            @if(isset($pack))
                @method('PUT')
            @endif
            
            <!-- Pack Details Section -->
            <section class="bg-white p-6 rounded-xl shadow-lg space-y-6">
                <h2 class="text-xl font-semibold border-b pb-2 text-gray-700">Pack Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Size Group Dropdown -->
                    <div>
                        <label for="sizeGroupSelector" class="block text-gray-700 font-medium mb-1">Size Group</label>
                        <select id="sizeGroupSelector" name="size_group_id" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" required>
                            @foreach($sizeGroups as $group)
                                <option value="{{ $group->id }}" @if(isset($pack) && $group->id == $pack->size_group_id) selected @endif>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Pack Name Input -->
                    <div>
                        <label for="packNameInput" class="block text-gray-700 font-medium mb-1">Pack Name</label>
                        <input type="text" id="packNameInput" name="name" value="@if(isset($pack)){{ $pack->name }}@endif" placeholder="Enter pack name" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </section>

            <!-- Ratios Section -->
            <div class="bg-white p-6 rounded-xl shadow-lg space-y-4">
                <div class="flex items-center justify-between border-b pb-2">
                    <h2 class="text-xl font-semibold text-gray-700">Ratios</h2>
                    <button type="button" id="addRatioBtn" class="btn btn-primary w-full md:w-auto">Add Ratio Row</button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-inner">
                        <thead class="bg-gray-100 text-gray-800">
                            <tr>
                                <th class="py-2 px-4 text-left font-medium">Size Name</th>
                                <th class="py-2 px-4 text-left font-medium">Ratio Quantity</th>
                                <th class="py-2 px-4 text-left font-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody id="ratiosContainer">
                            @if(isset($pack) && $pack->sizes->count() > 0)
                                @foreach($pack->sizes as $size)
                                <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                                    <td class="p-2 w-1/2">
                                        <input type="hidden" name="ratios[{{ $loop->index }}][id]" value="{{ $size->id }}">
                                        <input type="text" required name="ratios[{{ $loop->index }}][size_name]" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" placeholder="Size (e.g., L)" value="{{ $size->size_name }}">
                                    </td>
                                    <td class="p-2 w-1/2">
                                        <input type="number" required name="ratios[{{ $loop->index }}][ratio]" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" placeholder="Ratio (e.g., 1)" value="{{ $size->ratio }}">
                                    </td>
                                    <td class="p-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-ratio-btn">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" 
                    class="px-6 py-2 bg-black text-white font-semibold text-sm 
                           rounded-xl shadow-md hover:bg-gray-900 
                           focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                           transition-all duration-300 ease-in-out w-full md:w-auto">
                @if(isset($pack))
                    💾 Save Changes
                @else
                    💾 Save Pack
                @endif
            </button>
            
        </form>
    </div>

    <script>
        const addRatioBtn = document.getElementById('addRatioBtn');
        const ratiosContainer = document.getElementById('ratiosContainer');
        let ratioIndex = {{ isset($pack) ? $pack->sizes->count() : 0 }};

        function addRatioRow(size = '', qty = '') {
            const row = document.createElement('tr');
            row.classList.add('bg-gray-50', 'text-gray-700', 'border-b', 'border-gray-200');
            row.innerHTML = `
                <td class="p-2 w-1/2">
                    <input type="text" name="ratios[${ratioIndex}][size_name]" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" placeholder="Size (e.g., L)" value="${size}">
                </td>
                <td class="p-2 w-1/2">
                    <input type="number" name="ratios[${ratioIndex}][ratio]" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" placeholder="Ratio (e.g., 1)" value="${qty}">
                </td>
                <td class="p-2">
                    <button type="button" class="btn btn-danger btn-sm remove-ratio-btn">Remove</button>
                </td>
            `;
            ratiosContainer.appendChild(row);
            ratioIndex++;
        }

        addRatioBtn.addEventListener('click', () => {
            addRatioRow();
        });

        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-ratio-btn')) {
                const row = e.target.closest('tr');
                if (row) {
                    row.remove();
                    // Re-index the remaining rows
                    const remainingRows = ratiosContainer.querySelectorAll('tr');
                    remainingRows.forEach((r, index) => {
                        const sizeInput = r.querySelector('[name*="size_name"]');
                        const ratioInput = r.querySelector('[name*="ratio"]');
                        const hiddenIdInput = r.querySelector('[name*="id"]');

                        if (sizeInput) sizeInput.name = `ratios[${index}][size_name]`;
                        if (ratioInput) ratioInput.name = `ratios[${index}][ratio]`;
                        if (hiddenIdInput) hiddenIdInput.name = `ratios[${index}][id]`;
                    });
                    ratioIndex = remainingRows.length;
                }
            }
        });

        // Add an initial row for create mode if no pack data is present
        @if(!isset($pack) || $pack->sizes->count() == 0)
            document.addEventListener('DOMContentLoaded', () => {
                addRatioRow();
            });
        @endif
    </script>
</body>
</html>
