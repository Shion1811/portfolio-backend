<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Url;

class UrlsController extends Controller
{

    /** URL一覧を取得する関数*/
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Url::all(),
        ]);
    }

    /** URLを追加する関数*/
    public function store(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'url' => ['required', 'url', 'max:255'],
        ], [
            // バリデーションエラー
            'url.required' => 'URLは必須です',
            'url.url' => '有効なURLを入力してください',
            'url.max' => 'URLは255文字以内で入力してください',
        ]);

        // バリデーションの失敗
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = Url::create([
            'url' => $request->input('url'),
        ]);

        // 成功
        return response()->json([
            'success' => true,
            'message' => 'URLを追加しました',
            'data' => $url,
        ], 201);
    }
}
