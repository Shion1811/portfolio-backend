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
         // 環境変数と一致しない場合エラー
            if (!($email) || !($password)) {
                return response()->json([
                    "message" => "メールアドレスまたはパスワードが正しくありません"
                ], 401);
            }
            // メールアドレスかパスワードが一致しない場合
            if ($email !== $MyEmail || $password !== $MyPassword) {
                return response()->json([
                    "message" => "メールアドレスまたはパスワードが正しくありません"
                ], 401);
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
             return response()->json([
                "message" => "ユーザーが見つかりません"
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
                "message" => "ログイン処理中にエラーが発生しました",
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ], 500);
        }
    }

    /** ログインする関数*/
    public function apiLogin(Request $request)
    {
        try {
            $MyEmail = env("EMAIL");
            $MyPassword = env("PASSWORD");
            $email = $request->input("email");
            $password = $request->input("password");
            
            if (!($email) || !($password)) {
                return response()->json([
                    "success" => false,
                    "message" => "メールアドレスとパスワードを入力してください"
                ], 401);
            }
            
            if ($email !== $MyEmail || $password !== $MyPassword) {
                return response()->json([
                    "success" => false,
                    "message" => "メールアドレスまたはパスワードが正しくありません"
                ], 401);
            }
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    "success" => false,
                    "message" => "ユーザーが見つかりません"
                ], 401);
            }

            // セッションにログイン状態を保存
            Auth::login($user);
            $token = $user->createToken('token')->plainTextToken;
            
            return response()->json([
                "success" => true,
                "message" => "ログイン成功",
                "token" => $token,
                "user" => [
                    "id" => $user->id,
                    "email" => $user->email,
                ]
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "ログイン処理中にエラーが発生しました",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /** ログアウトする関数*/
    public function apiLogout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            // トークンを削除
            $user->tokens()->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json([
            "success" => true,
            "message" => "ログアウトしました"
        ]);
    }

    /** 認証状態確認する関数*/
    public function apiUser(Request $request)
    {
        $user = $request->user();
        
        // ユーザーが見つからない場合エラー
        if (!$user) {
            return response()->json([
                "success" => false,
                "authenticated" => false
            ], 401);
        }
        
        return response()->json([

            "success" => true,
            "authenticated" => true,
            "user" => [
                "id" => $user->id,
                "email" => $user->email,
            ]
        ]);
    }
}
