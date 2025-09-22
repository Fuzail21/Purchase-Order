@extends('layouts.app')
@section('content')
            
                <!-- Header with button -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Orders List</h1>
                    <a href="{{ route('orders.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow font-medium">
                        + New Order
                    </a>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <table class="min-w-full text-center border-collapse">
                        <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                            <tr>
                                <th class="border px-4 py-3">Job No</th>
                                <th class="border px-4 py-3">Style No</th>
                                <th class="border px-4 py-3">Buyer</th>
                                <th class="border px-4 py-3">Order Qty</th>
                                <th class="border px-4 py-3">Final Total</th>
                                <th class="border px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="border px-4 py-3 font-medium text-gray-800">{{ $order->job_no }}</td>
                                    <td class="border px-4 py-3">{{ $order->style_no }}</td>
                                    <td class="border px-4 py-3">{{ $order->buyer }}</td>
                                    <td class="border px-4 py-3">{{ $order->order_qty }}</td>
                                    <td class="border px-4 py-3 font-bold text-green-600">{{ $order->final_total }}</td>
                                    <td class="border px-4 py-3 space-x-2">
                                        <a href="{{ route('orders.edit', $order->id) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow">
                                            Edit
                                        </a>
                                        <a href="{{ route('orders.pdf', $order->id) }}" target="_blank" 
                                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded shadow">
                                            PDF
                                        </a>
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" 
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this order?')">
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
                                    <td colspan="8" class="py-6 text-gray-500">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{-- <div class="mt-6">
                    {{ $orders->links('pagination::tailwind') }}
                </div> --}}
    @endsection

