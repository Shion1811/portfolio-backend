<?php

namespace App\Http\Controllers;

// httpリクエストを受け取る
use Illuminate\Http\Request;
// ユーザーモデルを使用する
use App\Models\User;
// 例外を使用する
use Exception;
// レスポンスを返す
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login(Request $request) {

        try {
            $MyEmail = env("EMAIL");
            $MyPassword = env("PASSWORD");
            $email = $request->input("email");
            $password = $request->input("password");
            // メールアドレスかパスワードが空の場合
            if (!($email) || !($password)) {
                return response()->json([
                    "message" => "違うよばーか"
                ], 401);
            }
            // メールアドレスかパスワードが一致しない場合
            if ($email !== $MyEmail || $password !== $MyPassword) {
                return response()->json([
                    "message" => "違うよばーか1"
                ], 401);
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
             return response()->json([
                "message" => "ユーザーが存在しなーーーーーーーいですよんお馬鹿さん"
             ], 401);
            }

            // トークンを生成
            // トーク生成時にpersonal_access_tokens テーブルにデータが追加される
            // Laravel Sanctumの初期状態ではvenderディレクトリにあるためそれを追加する必要がある
            //  1.docker compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
            
            Auth::login($user);
            $token = $user->createToken('token')->plainTextToken;
            // login成功時にlogin.blade.phpを表示する
            return redirect()->route('admin-get')->with([
                "message" => "ログイン成功",
                "token" => $token,
            ]);
            // Exception: 例外を使用する
            // $eはExceptionの変数名
            
        } catch (Exception $e) {
            return response()->json([
                "message" => "違うよばーか3",
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ], 500);
        }
    }
}
