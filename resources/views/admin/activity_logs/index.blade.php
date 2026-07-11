<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Time</th>
                                    <th class="py-2 px-4 border-b text-left">User</th>
                                    <th class="py-2 px-4 border-b text-left">Action</th>
                                    <th class="py-2 px-4 border-b text-left">Description</th>
                                    <th class="py-2 px-4 border-b text-left">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                <tr>
                                    <td class="py-2 px-4 border-b text-sm text-gray-700 whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-700">
                                        {{ $log->user ? $log->user->name : 'System/Guest' }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-700">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">{{ $log->action }}</span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-700">
                                        {{ $log->description }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-700">
                                        {{ $log->ip_address }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">No activity logs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>