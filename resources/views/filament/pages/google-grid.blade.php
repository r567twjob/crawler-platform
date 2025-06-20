<x-filament-panels::page>
<h1>單一 NearSearch 爬蟲工具</h1>
    {{-- <div class="col-md-3">
        <form class="form-inline d-flex align-items-center mb-3" onsubmit="return false;">
            <input class="form-control me-2" type="text" name="" id="grid-id" placeholder="輸入 Grid ID" style="max-width: 150px;">
            <button type="button" class="btn btn-info" onclick="getGrid()">載入</button>
        </form>
    </div> --}}
    <div class="col-md-3">
        <form class="mt-5" action="{{ route('google-grid.post') }}" method="POST">
            <div class="form">
                @csrf
                <label for="name">名稱</label>
                <input type="text" name="name" id="name">

                <label for="lng">經度 lng</label>
                <input id="lng" type="number" class="form-control" name="lng" placeholder="請輸入經度" step="0.001" required>

                <label for="lat">緯度 lat</label>
                <input id="lat" type="number" class="form-control" name="lat" placeholder="請輸入緯度" step="0.001" required>

                <label for="radius">半徑 radius</label>
                <input id="radius" type="number" class="form-control" name="radius" placeholder="請輸入半徑" value="500" required>
            </div>
            <button type="submit" class="mt-3 btn btn-primary">查詢 Google 爬蟲 資料(nearbySearch)</button>
        </form>
    </div>

{{--
@section('scripts')
    <script>
        function getGrid() {
            const gridId = document.getElementById('grid-id').value;
            if (!gridId) {
                alert('請輸入 Grid ID');
                return;
            }
            fetch(`/api/grid/${gridId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data && data.lng && data.lat) {
                        document.getElementById('lng').value = data.lng;
                        document.getElementById('lat').value = data.lat;
                    } else {
                        alert('無法找到該 Grid ID 的經緯度');
                    }
                })
                .catch(error => console.error('Error fetching grid data:', error));
        }
    </script>
@endsection --}}
</x-filament-panels::page>
