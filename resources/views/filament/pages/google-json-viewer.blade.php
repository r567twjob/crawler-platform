<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center space-x-2">
            <label for="selectedFile" class="text-gray-900 dark:text-gray-100">選擇：</label>
            <div
                x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('selectedFile').defer,
                    options: @js($availableFiles->map(fn($f) => [
                        'id' => (string) $f->id,
                        'label' => $f->district->name . ' - ' . $f->id
                    ])),
                    get filtered() {
                        if (!this.search) return this.options;
                        return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    select(id) {
                        this.selected = id;
                        this.open = false;
                        this.search = '';
                        $wire.set('selectedFile', id);
                        $wire.loadJsonData();
                    }
                }" class="relative w-full max-w-xs">
                <div @click="open = !open" class="border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer flex items-center">
                    <span x-text="options.find(o => o.id == String(selected))?.label || '請選擇'"></span>
                    <svg class="ml-auto w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <div x-show="open" @click.away="open = false" class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded mt-1 max-h-60 overflow-auto shadow">
                    <input
                        x-model="search"
                        @keydown.enter.prevent="filtered.length > 0 && select(filtered[0].id)"
                        type="text"
                        placeholder="搜尋..."
                        class="w-full px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 focus:outline-none"
                        autofocus
                    >
                    <template x-for="option in filtered" :key="option.id">
                        <div @click="select(option.id)" class="px-3 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 cursor-pointer"
                            :class="{'bg-blue-50 dark:bg-gray-700': String(option.id) === String(selected)}">
                            <span x-text="option.label"></span>
                        </div>
                    </template>
                    <div x-show="filtered.length === 0" class="px-3 py-2 text-gray-400">無結果</div>
                </div>
            </div>

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
