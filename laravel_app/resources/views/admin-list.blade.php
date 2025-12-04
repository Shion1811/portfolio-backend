<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-list.css') }}">
    <title>admin-list</title>
</head>
<body class="bg-[#eee0cc] w-[1580px] h-full mx-auto">
    <h1 class=" text-[#040404] text-4xl font-bold text-center w-full">ポートフォリオ一覧</h1>
    <a href="{{ route('admin-get') }}" class="w-fit bg-blue-500 text-white p-2 rounded-md mx-8">投稿ページへ</a>
    <!-- 保存されたデータを表示する -->
    <div class="w-full h-fit p-4 m-4">
    @foreach($portfolios as $portfolio)
    <div class="w-full h-fit border-2 border-gray-300 rounded-md p-4 my-4">
        <div class="flex gap-3 items-center">
        <h2 class="text-2xl font-bold">タイトル：</h2>
        <p>{{ $portfolio->title }}</p>
        </div>
        <div class="flex gap-3 items-center">
            <h2 class="text-2xl font-bold">説明：</h2>
            <p>{{ $portfolio->explanation }}</p>
        </div>
        <div class="bg-gray-200 rounded-md p-4 flex items-end">
            @if(!empty($portfolio->image))
                @php
                    $imagePaths = json_decode($portfolio->image, true) ?? [];
                    // カンマ区切りの場合のフォールバック
                    if (empty($imagePaths) && is_string($portfolio->image)) {
                        $imagePaths = [$portfolio->image];
                    }
                @endphp
                
                @foreach($imagePaths as $index => $imagePath)
                    @php
                        $imageUrl = asset('storage/' . $imagePath);
                        $imageExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                    @endphp
                    
                    <div class="image-wrapper" 
                        data-image-url="{{ $imageUrl }}" 
                        data-extension="{{ $imageExtension }}"
                        data-id="{{ $portfolio->id }}-{{ $index }}">
                        <img id="img-{{ $portfolio->id }}-{{ $index }}" 
                            src="" 
                            alt="{{ $portfolio->title }}" 
                            width="200" 
                            height="200"
                            style="border: 1px solid #ccc; display: none; max-width: 200px; max-height: 200px; object-fit: contain; margin: 5px;">
                        <p id="loading-{{ $portfolio->id }}-{{ $index }}" style="color: blue;">画像を読み込み中...</p>
                    </div>
        @endforeach
        </div>
        @endif
        <div>

        </div> 
        <div class="mt-4">
            <h2 class="text-xl font-bold mb-2">タグ:</h2>
            @php
                // JSON形式で保存されている場合と単一タグの場合の両方に対応
                $tagsArray = [];
                $decoded = json_decode($portfolio->tags, true);
                if (is_array($decoded) && !empty($decoded)) {
                    // JSON形式の場合
                    $tagsArray = $decoded;
                } elseif (is_string($portfolio->tags) && !empty($portfolio->tags)) {
                    // 単一タグの場合（JSONではない場合）
                    $tagsArray = [$portfolio->tags];
                }
                $tagStyle = 'border-2 border-gray-300 text-gray-700 px-3 py-1 rounded-xs text-sm bg-white';

            @endphp
            
            @if(!empty($tagsArray))
                <div class="flex flex-wrap gap-2">
                    @foreach($tagsArray as $tag)
                        <span class="{{ $tagStyle }}">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
            @if($portfolio->url)
                <p><strong>URL:</strong> <a href="{{ $portfolio->url }}">{{ $portfolio->url }}</a></p>
            @endif
    <form action="{{ route('admin-list-delete') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ $portfolio->id }}">
        <div class="bg-red-500 text-white p-2 rounded-md text-center w-fit">
            <button type="submit">削除</button>
        </div>
    </form>
    </div>
    @endforeach


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.image-wrapper').forEach(wrapper => {
                const imageUrl = wrapper.getAttribute('data-image-url');
                const extension = wrapper.getAttribute('data-extension');
                const portfolioId = wrapper.getAttribute('data-id');
                const imgElement = document.getElementById('img-' + portfolioId);
                const loadingElement = document.getElementById('loading-' + portfolioId);
                
                if (extension === 'heic' || extension === 'heif') {
                    // HEICファイルの場合、JPEGに変換して表示
                    fetch(imageUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('画像の取得に失敗しました: ' + response.status);
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            return heic2any({
                                blob: blob,
                                toType: "image/jpeg",
                                quality: 0.8
                            });
                        })
                        .then(conversionResult => {
                            // conversionResultは配列の場合がある
                            const result = Array.isArray(conversionResult) ? conversionResult[0] : conversionResult;
                            const url = URL.createObjectURL(result);
                            imgElement.src = url;
                            imgElement.style.display = 'block';
                            loadingElement.style.display = 'none';
                        })
                        .catch(error => {
                            console.error('HEIC変換エラー:', error);
                            loadingElement.textContent = '画像の読み込みに失敗しました: ' + error.message;
                        });
                } else {
                       // JPEG/PNGなどの場合はそのまま表示
                       imgElement.src = imageUrl;
                    imgElement.onload = function() {
                        imgElement.style.display = 'block';
                        loadingElement.style.display = 'none';
                    };
                    imgElement.onerror = function() {
                        loadingElement.textContent = '画像の読み込みに失敗しました';
                    };
                }
            });
        });
    </script>
</body>
</html>