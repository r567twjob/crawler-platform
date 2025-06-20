<x-filament-panels::page>
<h1>NearSearch 爬蟲工具 (使用 CSV)</h1>

<form action="{{ route('csv.import') }}" method="post" enctype="multipart/form-data">
    <label for="">請上傳 CSV 檔</label>
    @csrf
    <input type="file" name="csv" id="" accept=".csv">
    <button>開始匯入資料</button>
</form>

</x-filament-panels::page>
