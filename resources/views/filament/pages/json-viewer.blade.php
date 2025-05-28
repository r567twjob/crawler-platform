<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center space-x-2">
            <label for="selectedFile" class="text-gray-900 dark:text-gray-100">選擇 JSON 檔：</label>
            <select wire:model="selectedFile" id="selectedFile"
                class="border rounded px-2 py-1 text-black dark:text-white dark:bg-gray-800 dark:border-gray-600"
                wire:change="loadJsonData">
                @foreach ($availableFiles as $file)
                    <option value="{{ $file->id }}" class="text-black dark:text-white dark:bg-gray-800">
                        {{ $file->id }}</option>
                @endforeach
            </select>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-300 dark:border-white">
                <thead>
                    <tr
                        class="border-b border-gray-300 dark:border-white bg-gray-800 text-gray-900 dark:text-gray-100 dark:bg-gray-900">
                        <th class="px-4 py-2 font-semibold text-left border-r border-gray-300 dark:border-white">ID</th>
                        <th class="px-4 py-2 font-semibold text-left border-r border-gray-300 dark:border-white">
                            Formatted Address</th>
                        <th class="px-4 py-2 font-semibold text-left border-r border-gray-300 dark:border-white">Display
                            Name</th>
                        <th class="px-4 py-2 font-semibold text-left">Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jsonData as $key => $value)
                        <tr class="border-b border-gray-300 dark:border-white">
                            <td class="px-4 py-2 font-semibold border-r border-gray-300 dark:border-white">
                                {{ Str::limit($value->id, 5, '...') }}</td>
                            <td class="px-4 py-2 font-semibold border-r border-gray-300 dark:border-white">
                                {{ Str::limit($value->formattedAddress, 25, '...') }}
                            </td>
                            <td class="px-4 py-2 font-semibold border-r border-gray-300 dark:border-white">
                                {{ Str::limit($value->displayName->text, 20, '...') }}
                            </td>
                            <td class="px-4 py-2 font-semibold">{{ $value->rating ?? 0.0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</x-filament-panels::page>
