@extends('layouts.app')
@section('title', 'Page Title')

@section('content')
    <div class="col-md-3">
        <form class="mt-5" action="{{ route('google-grid.post') }}" method="POST">
            <div class="form-group">
                @csrf
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
@endsection
