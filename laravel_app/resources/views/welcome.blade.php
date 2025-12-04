<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/top.css') }}">
    <title>portfolio admin</title>
</head>
<body class="bg-gray-100 w-full h-full">
    <h1>ログイン</h1>
    <form action="{{ route('login-post') }}" method="post">
    @csrf
    <label for="email">メールアドレス</label>
    <input type="email" name="email">
    <label for="password">パスワード</label>
    <input type="password" name="password">
    <label for="password_confirmation">パスワード確認</label>
    <input type="password" name="password_confirmation">
    <button type="submit">ログイン</button>
    </form>
    <!-- isset: 変数が存在するかどうかを確認 -->
    <!-- empty: 変数が空かどうかを確認 -->
    @if(isset($email) && isset($password) && !empty($email) && !empty($password))
    <p>{{ $email }} {{ $password }}</p>
    @endif
</body>
</html>