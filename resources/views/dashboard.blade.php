<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex justify-between items-center">
            {{ __('Dashboard') }}
            <form action="{{ route('dashboard.store') }}"
                class="flex gap-4 p-2 shadow-sm rounded-lg border border-gray-200" method="POST"
                enctype="multipart/form-data">
                @csrf
                <label for="document"
                    class="flex flex-col items-center justify-center w-[200px] h-12 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100"
                    title="Upload Document" id="upload-label">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                    </div>
                </label>
                <input id="document" type="file" class="hidden" name="document[]" multiple required />
                <label id="fileNames" class="flex flex-wrap gap-2 max-w-md hidden cursor-pointer"
                    for="document"></label>
                <div class="relative max-w-sm flex">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input datepicker id="default-datepicker" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                        placeholder="Select date" name="date" required />
                </div>


                <x-primary-button class="h-12" type="submit">
                    {{ __('Post') }}
                </x-primary-button>
            </form>
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('dashboard.search') }}" class="w-auto flex gap-4 p-2 items-center justify-end"
                method="POST">
                @csrf
                <x-text-input name="search" type="search" placeholder="Search By User" />
                <x-primary-button class="h-10" type="submit">
                    {{ __('Search') }}
                </x-primary-button>
            </form>
        </div>
    </div>
    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-[#e3e3e0]">
                    <thead class="bg-[#FDFDFC]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] uppercase tracking-wider">
                                Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] uppercase tracking-wider">
                                Date(y-m-d)</th>
                            @if (!Auth::user()->hasRole('user'))
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] uppercase tracking-wider">
                                    Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#e3e3e0]">
                        @foreach ($documents as $document)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href=" {{ asset('storage/' . ltrim($document->path, '/')) }}" target="_blank">
                                        <x-secondary-button>File</x-secondary-button>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $document->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $document->date }}
                                </td>
                                @if (!Auth::user()->hasRole('user'))
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this document?')">
                                                Delete
                                            </x-danger-button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('document').addEventListener('change', function(e) {
            const input = document.getElementById('upload-label');
            input.classList.add('hidden'); // Hide the input field
            const container = document.getElementById('fileNames');
            container.classList.remove('hidden');
            container.innerHTML = '';

            Array.from(this.files).forEach(file => {
                const badge = document.createElement('div');
                badge.className =
                    'bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-700 flex items-center gap-2';
                badge.innerHTML = `
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 18a.969.969 0 0 0 .933 1h12.134A.97.97 0 0 0 15 18M1 7V5.828a2 2 0 0 1 .586-1.414l2.828-2.828A2 2 0 0 1 5.828 1h8.239A.97.97 0 0 1 15 2v5M6 1v4a1 1 0 0 1-1 1H1m0 6h15"/>
            </svg>
            ${file.name}
        `;
                container.appendChild(badge);
            });
        });
    </script>
</x-app-layout>
