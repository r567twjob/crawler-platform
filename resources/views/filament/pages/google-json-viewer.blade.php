<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center space-x-2">
            <label for="selectedFile" class="text-gray-900 dark:text-gray-100">選擇：</label>
            <select wire:model="selectedFile" id="selectedFile" wire:change="loadJsonData" class="w-full max-w-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-500">
                @foreach ($availableFiles as $file)
                    <option value="{{ $file->id }}" class="text-dark dark:text-dark">
                        {{ $file->district->name }} - {{ $file->id }}
                    </option>
                @endforeach
            </select>

            <input type="button" value="下載此 JSON 檔案"
                class="bg-blue-500 text-dark dark:text-white px-4 py-2 rounded hover:bg-blue-600 {{ count($jsonData) === 0 ? 'opacity-50 cursor-not-allowed hover:cursor-not-allowed' : 'cursor-pointer' }}"
                style="{{ count($jsonData) === 0 ? 'cursor:not-allowed;' : '' }}"
                wire:click="downloadJson"
                @disabled(count($jsonData) === 0)
            />
        </div>

        @if (count($jsonData) === 0)
            <div class="overflow-x-auto">
                <p>尚未下載</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300 dark:border-white">
                    <thead>
                        <tr
                            class="border-b border-gray-300 dark:border-white bg-gray-800 text-gray-900 dark:text-gray-100 dark:bg-gray-900">
                            <th class="px-4 py-2 font-semibold text-left border-r border-gray-300 dark:border-white">名稱</th>
                            <th class="px-4 py-2 font-semibold text-left">評分</th>
                            <th class="px-4 py-2 font-semibold text-left border-r border-gray-300 dark:border-white">地址</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jsonData as $key => $value)
                            <tr class="border-b border-gray-300 dark:border-white">
                                <td class="px-4 py-2 font-semibold border-r border-gray-300 dark:border-white">
                                    <a href="{{ $value->googleMapsUri }}" target="_blank">{{ Str::limit($value->displayName->text, 20, '...') }}</a>
                                </td>
                                <td class="px-4 py-2 font-semibold">{{ $value->rating ?? 0.0 }}</td>
                                <td class="px-4 py-2 font-semibold border-r border-gray-300 dark:border-white">{{ Str::limit($value->formattedAddress, 50, '...') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
