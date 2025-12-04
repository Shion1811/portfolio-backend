<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
class AdminController extends Controller
{
    private function checkAuth()
    {
        $user = request()->user();
        $MyEmail = env("EMAIL");
        
        // ユーザーが認証されていない、またはemailが一致しない場合
        if (!$user || $user->email !== $MyEmail) {
            return redirect('/');
        }
        
        return null;
    }
    public function adminLogin(Request $request)
    {
      
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }
            
            return redirect()->route('admin-list-get');
    }

    public function admin(Request $request)
    {
        // imput: リクエストからデータを取得する
        // file: ファイルを取得する
        // all: 全てのデータを取得する
        // has: 値が存在するか
        // hasFile: ファイルが存在するか
        // query: クエリパラメータを取得する
        // route: ルートパラメータを取得する
        // etc...   
        $title = $request->input('title');
        $explanation = $request->input('explanation');
        $images = $request->file('images');
        $tags = $request->input('tags');
        $url = $request->input('url');

        $imagePaths = [];
        if ($images) {
            foreach ($images as $image) {
                // 画像が破損していないかチェック
                if ($image && $image->isValid()) {
                    // 画像を保存してパスを取得
                    $imagePath = $image->store('images', 'public');
                    // 保存した画像を配列に追加
                    $imagePaths[] = $imagePath;
                }
            }
        }
        $images = !empty($imagePaths) ? json_encode($imagePaths) : null;

        $shion = request()->user();

        $shion->addPortfolio()->create([
            "title" => $title,
            "explanation" => $explanation,
            "images" => $images,
            "tags" => $tags,
            "url" => $url,
        ]);
        return response()->json([
            "message" => "追加成功",
            "data" => [
                "title" => $title,
                "explanation" => $explanation,
                "images" => $images,
                "tags" => $tags,
                "url" => $url,
            ],
        ], 200);
    }
    public function adminCreate()
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }
        
        $tags = ["figma", "html", "css", "JavaScript", "PHP", "Laravel", "MySQL", "GitHub", "Docker", "React", "Next.js", "TypeScript"];
        
        return view('admin', compact('tags'));
    }
    public function adminList()
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }
        $portfolios = admin::all();
        return view('admin-list', compact('portfolios'));
    }
    public function adminListPost(Request $request) {
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
    
    $images = !empty($imagePaths) ? json_encode($imagePaths) : '';

    // タグの処理（配列として送信される）
    $tagsArray = [];
    if (is_array($tagsInput) && !empty($tagsInput)) {
        // 配列の場合、空の要素を削除
        $tagsArray = array_filter($tagsInput);
        $tagsArray = array_values($tagsArray);
    }
    // タグが選択されていない場合でも空のJSON文字列を保存（NOT NULL制約を満たすため）
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
    public function adminListDelete(Request $request) {
        $id = $request->input('id');
        admin::find($id)->delete();
        return redirect()->route('admin-list-get');
    }
}
