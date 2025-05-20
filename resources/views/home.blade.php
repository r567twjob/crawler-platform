@extends('layouts.app')
@section('title', 'Page Title')

@section('content')
    <div class="col-md-3">
        <form class="mt-5" action="/start-nearby-search" method="POST">
            <div class="form-group">
                @csrf
                <label for="district">選擇地區</label>
                <select class="form-control" name="district" id="district">
                    @foreach ($districts as $district => $value)
                        @if ($value['processed'] == false)
                            <option class="form-control" value="{{ $district }}">{{ $value['name'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="mt-3 btn btn-primary">使用 Google 爬蟲</button>
        </form>
    </div>
@endsection
