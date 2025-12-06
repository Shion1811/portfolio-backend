<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
// logを使用するために使用
// use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // ユーザー認証関数
    private function checkAuthApi()
    {
        $user = request()->user();
        $MyEmail = env("EMAIL");
        
        // ユーザーが認証されていない、またはemailが一致しない場合
        if (!$user || $user->email !== $MyEmail) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }
        
        return null;
    }
    // checkAuthApi関数で認証されてない場合はリダイレクトされる
    public function adminListPost(Request $request) {
        // 認証チェックを追加
        $redirect = $this->checkAuthApi();
        if ($redirect) {
            return $redirect;
        }

        // カラムを取得する
        $title = $request->input('title');
        $explanation = $request->input('explanation');
        $images = $request->file('images');
        $tagsInput = $request->input('tags');
        $url = $request->input('url');

        $imagePaths = [];
    if ($images) {
        // $imagesは配列なので、各要素をループ処理
        foreach ($images as $image) {
            if ($image && $image->isValid()) {
                // 画像を保存してパスを取得
                $imagePath = $image->store('images', 'public');
                $imagePaths[] = $imagePath;
            }
        }
    } 
    
    // 画像のパスをjsonに変換し、なければ空文字
    $images = !empty($imagePaths) ? json_encode($imagePaths) : '';

    // タグの処理（配列として送信される）
    $tagsArray = [];
    if (is_array($tagsInput) && !empty($tagsInput)) {
        // 配列の場合、空の要素を削除
        $tagsArray = array_filter($tagsInput);
        $tagsArray = array_values($tagsArray);
    }
    // タグをjsonに変換し、なければ空文字
    $tagsJson = !empty($tagsArray) ? json_encode($tagsArray) : '';

    
    // データを保存
        admin::create([
            "title" => $title,
            "explanation" => $explanation,
            "image" => $images,
            "tags" => $tagsJson,
            "url" => $url,
        ]);
        
        return redirect()->route('admin-list-get');
    }
    // ポートフォリオの削除する関数
    public function adminListDelete(Request $request) {
        // idを取得してそのidを削除する
        $id = $request->input('id');
        admin::find($id)->delete();
        return redirect()->route('admin-list-get');
    }

    /** ポートフォリオ一覧を取得する関数*/
    public function apiList()
    {

        // ポートフォリオ一覧を取得する
        $portfolios = admin::all()->map(function ($portfolio) {
            return [
                // 必要なカラムを取得
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'explanation' => $portfolio->explanation,
                'images' => json_decode($portfolio->image, true) ?? [],
                'tags' => json_decode($portfolio->tags, true) ?? [],
                'urls' => json_decode($portfolio->urls, true) ?? [],
                'created_at' => $portfolio->created_at?->toISOString(),
                'updated_at' => $portfolio->updated_at?->toISOString(),
            ];
        });

        // ちゃんとできてればjsonで返す
        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }

    /** ポートフォリオを作成する関数*/
    public function apiCreate(Request $request)
    {

        // バリデーション
        // バリデーション： 入力項目が正しいのかチェックする
        $validated = $request->validate([
            // required： 必須
            // nullable： 任意

            // タイトル内容が最大50文字
            'title' => 'required|string|max:50',
            // 説明文が最大1000文字
            'explanation' => 'required|string|max:1000',
            // array： 配列
            'tags' => 'required|array',
            // 1048576 = 1GB
            // 画像サイズが最大1GBまで
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic,heif|max:1048576',
            // URLの配列
            'urls' => 'nullable|array',
            // URLが最大255文字
            'urls.*' => 'nullable|url|max:255',
            // 単一URLが最大255文字
            'url' => 'nullable|url|max:255',
        ], [
            // 
            'title.required' => 'タイトルは必須です',
            'explanation.required' => '説明は必須です',
            'tags.required' => 'タグは必須です',
            'tags.array' => 'タグは配列である必要があります',
            'images.*.image' => '画像ファイルを選択してください',
            'images.*.mimes' => '画像はjpeg, png, jpg, gif, heic, heif形式のみ対応しています',
            'images.*.max' => '画像サイズは20MB以下にしてください',
            'urls.array' => 'URLは配列で送信してください',
            'urls.*.url' => '有効なURLを入力してください',
            'url.url' => '有効なURLを入力してください',
        ]);

        // バリデーションが成功時に変数に代入
        $title = $validated['title'];
        $explanation = $validated['explanation'];
        $images = $request->file('images');
        $tagsInput = $validated['tags'];
    
        // urlの単一の時と複数の時を判断し配列に変換
        $urlsInput = $validated['urls'] ?? null;
        if (!$urlsInput && !empty($validated['url'] ?? null)) {
            $urlsInput = [$validated['url']];
        }
        $urlsArray = is_array($urlsInput) ? array_values(array_filter($urlsInput)) : [];

        // 画像処理
        $imagePaths = [];
        if ($images) {
            foreach ($images as $image) {
                if ($image && $image->isValid()) {
                    // image->sotre: 画像を保存
                    // images: 保存するディレクトリ
                    $imagePath = $image->store('images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }
        }
        // 画像のパスをjsonに変換し、なければ空文字
        $imagePathJson = !empty($imagePaths) ? json_encode($imagePaths) : '';

        // jsonに保存
        $tagsArray = [];
        if (is_array($tagsInput) && !empty($tagsInput)) {
            $tagsArray = array_filter($tagsInput);
            $tagsArray = array_values($tagsArray);
        }
        $tagsJson = !empty($tagsArray) ? json_encode($tagsArray) : '';

        // データを保存
        $portfolio = admin::create([
            "title" => $title,
            "explanation" => $explanation,
            "image" => $imagePathJson,
            "tags" => $tagsJson,
            "urls" => !empty($urlsArray) ? json_encode($urlsArray) : null,
        ]);

        // 成功したらjsonで返す
        return response()->json([
            'success' => true,
            'message' => 'ポートフォリオを追加しました',
            'data' => [
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'explanation' => $portfolio->explanation,
                'images' => json_decode($portfolio->image, true) ?? [],
                'tags' => json_decode($portfolio->tags, true) ?? [],
                'urls' => json_decode($portfolio->urls, true) ?? [],
            ]
        ], 201);
    }

    /** ポートフォリオを削除する関数*/
    public function apiDelete($id)
    {
        $portfolio = admin::find($id);
        // ポートフォリオが見つからない場合はエラー
        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'ポートフォリオが見つかりません'
            ], 404);
        }

        // ポートフォリオを削除
        $portfolio->delete();
        // 成功したらjsonで返す

        return response()->json([
            'success' => true,
            'message' => 'ポートフォリオを削除しました'
        ]);
    }
}
