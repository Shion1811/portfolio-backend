<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>admin</title>
</head>
<body class="bg-[#eee0cc] w-[800px] h-full mx-auto">
    <h1 class="text-2xl font-bold text-center w-full">管理画面</h1>
    <div class="h-20">
        <a href="{{ route('admin-list-get') }}" class="w-fit bg-blue-500 text-white p-2 rounded-md mx-8 p-3">投稿一覧ページ</a>
    </div>
    <!-- enctype="multipart/form-data": ファイルを送信するときの設定 -->
     <!-- imgなどのファイルを送信するときは、enctype="multipart/form-data"を使用する -->
    <form action="{{ route('admin-get') }}" method="post" enctype="multipart/form-data" class="w-full h-full flex flex-col gap-4">
        @csrf
        <div class="flex gap-3 items-center h-[44px]">
            <label for="title" class="text-2xl font-bold w-[200px]">タイトル</label>
            <input type="text" name="title" class="w-full h-full p-2 rounded-md border-2 border-gray-300">
        </div>
        <div class="flex gap-3 items-center">
            <label for="explanation" class="text-2xl font-bold w-[200px]">内容</label>
            <input type="text" name="explanation" class="w-full h-full p-2 rounded-md border-2 border-gray-300">
        </div>
        <div class="flex gap-3 items-center">
            <label for="images" class="text-2xl font-bold w-[200px]">画像</label>
            <input type="file" name="images[]" multiple="image/*">
        </div>
        <!-- <div class="flex gap-3 items-center">
            <label for="tags" class="text-2xl font-bold w-[200px]">タグ</label>
            <input type="text" name="tags" class="w-full h-full p-2 rounded-md border-2 border-gray-300" placeholder="スペース区切りでタグを指定してください">
        </div> -->
        <div class="flex gap-3 items-center">
            <label for="tags" class="text-2xl font-bold w-[200px]">タグ</label>
            <div class="flex flex-col gap-2 w-full">
                <div class="flex flex-wrap gap-2 p-2 border-2 border-gray-300 rounded-md min-h-[60px]">
                    @foreach($tags as $tag)
                        <button type="button" 
                                class="tag-btn px-4 py-2 rounded-md border-2 border-gray-300 bg-white transition-colors"
                                data-tag="{{ $tag }}"
                                onclick="toggleTag(this, '{{ $tag }}')">
                            {{ $tag }}
                        </button>
                    @endforeach
                </div>
                <!-- 選択されたタグをhidden inputとして保存 -->
                <div id="tagsInputs"></div>
            </div>
        </div>
        <div class="flex gap-3 items-center">
            <label for="url" class="text-2xl font-bold w-[200px]">URL</label>
            <input type="text" name="url" class="w-full h-full p-2 rounded-md border-2 border-gray-300">
        </div>
        <!-- formaction: フォームの送信先をadmin-list-postに追加する -->
         <div class="flex justify-end">
            <button type="submit" formaction="{{ route('admin-list-post') }}" class="w-fit bg-blue-500 text-white p-2 rounded-md text-center">追加</button>
        </div>
    </form>

    <script>
        const selectedTags = new Set(); // 選択されたタグを管理
        
        function toggleTag(button, tagValue) {
            const isSelected = selectedTags.has(tagValue);
            const tagsInputsDiv = document.getElementById('tagsInputs');
            
            if (isSelected) {
                // タグを選択解除
                selectedTags.delete(tagValue);
                button.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                button.classList.add('bg-white', 'border-gray-300');
                
                // hidden inputを削除
                const inputElement = document.getElementById(`input-${tagValue}`);
                if (inputElement) {
                    inputElement.remove();
                }
            } else {
                // タグを選択
                selectedTags.add(tagValue);
                button.classList.remove('bg-white', 'border-gray-300');
                button.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                
                // hidden inputを作成
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tags[]';
                hiddenInput.value = tagValue;
                hiddenInput.id = `input-${tagValue}`;
                tagsInputsDiv.appendChild(hiddenInput);
            }
        }
    </script>
</body>
</html>