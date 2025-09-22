@extends('layouts.app')
@section('content')
            
                <div class="max-w-5xl mx-auto">
        <div class="flex justify-between mb-6">
            <h1 class="text-3xl font-bold">Packs List</h1>
            <!-- Button to open modal -->
            <a href="{{ route('packs.create') }}">
            <button
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + New Pack
            </button></a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full text-left border-collapse">
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="border px-4 py-3">Name</th>
                        <th class="border px-4 py-3">SizeGroup</th>
                        <th class="border px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packs as $pack)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-3">{{ $pack->name }}</td>
                            <td class="border px-4 py-3">{{ $pack->sizeGroup->name }}</td>
                            
                            <td class="border px-4 py-3">
                                <a href="{{ route('packs.edit', $pack->id) }}">
                                <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded shadow">
                                    Edit
                                </button></a>
                                <form action="{{ route('packs.destroy', $pack->id) }}" method="POST"
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
                            <td colspan="3" class="text-center py-4 text-gray-500">No packs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $packs->links('pagination::tailwind') }}
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
