@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>區域處理進度</h1>
        <ul id="district-list" style="list-style: none; padding: 0;">
            @foreach ($districts as $key => $district)
                <li data-key="{{ $key }}" data-processed="{{ $district['processed'] }}" style="margin-bottom: 20px;">
                    <strong>{{ $district['name'] }}</strong>
                    <div class="progress-container"
                        style="margin-top: 5px; width: 300px; height: 20px; background: #eee; border-radius: 10px; overflow: hidden;">
                        <div class="progress-bar"
                            style="width: 0%; height: 100%; background-color: #4caf50; transition: width 0.3s;"></div>
                    </div>
                    <small class="progress-text">載入中...</small>
                </li>
            @endforeach
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const listItems = document.querySelectorAll('#district-list li');

            function fetchProgress() {
                listItems.forEach(item => {
                    const key = item.dataset.key;
                    const processed = item.dataset.processed;

                    // console.log(`Processing ${key}: ${processed}`);
                    if (processed !== '1') {
                        item.querySelector('.progress-text').textContent = '⚠️ 尚未開始處理';
                        return;
                    }

                    fetch(`/progress?district=${key}`)
                        .then(response => response.json())
                        .then(data => {
                            const percent = data.total > 0 ? Math.round((data.done / data.total) *
                                100) : 0;

                            const bar = item.querySelector('.progress-bar');
                            const text = item.querySelector('.progress-text');

                            bar.style.width = percent + '%';
                            text.textContent = `${data.done} / ${data.total} (${percent}%)`;
                        })
                        .catch(error => {
                            item.querySelector('.progress-text').textContent = '⚠️ 無法取得狀態';
                        });
                });
            }

            // 初始化呼叫一次
            fetchProgress();

            // 每 5 秒呼叫一次
            setInterval(fetchProgress, 5000);
        });
    </script>
@endsection
