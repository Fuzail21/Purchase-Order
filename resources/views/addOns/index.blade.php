@extends('layouts.app')
@section('content')
            
                <div class="max-w-5xl mx-auto">
        <div class="flex justify-between mb-6">
            <h1 class="text-3xl font-bold">Add Ons List</h1>
            <!-- Button to open modal -->
            <button onclick="openModal()"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + New Add-On
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full text-left border-collapse">
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="border px-4 py-3">Name</th>
                        <th class="border px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($addOns as $addOn)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-3">{{ $addOn->name }}</td>
                            <td class="border px-4 py-3">
                                <form action="{{ route('addOn.destroy', $addOn->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No Size Group found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $addOns->links('pagination::tailwind') }}
        </div>
    </div>


    <!-- Modal -->
    <div id="packModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Add New Add On</h2>
            
            <form action="{{ route('addOn.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Add-On Name"
                       class="w-full border rounded-lg p-2" required>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endsection

    @section('scripts')
        <script>
            function openModal() {
                document.getElementById('packModal').classList.remove('hidden');
            }
            function closeModal() {
                document.getElementById('packModal').classList.add('hidden');
            }
        </script>
    @endsection
