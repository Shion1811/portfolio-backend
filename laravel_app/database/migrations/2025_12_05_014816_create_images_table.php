<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 既存のデータを保持しながらカラムを変更
        Schema::table('admins', function (Blueprint $table) {
            // 一時カラムを作成
            $table->json('urls')->nullable()->after('url');
        });
        
        // 既存のurlデータをurlsに移行（単一URLを配列に変換）
        DB::table('admins')->get()->each(function ($portfolio) {
            if ($portfolio->url) {
                DB::table('admins')
                    ->where('id', $portfolio->id)
                    ->update(['urls' => json_encode([$portfolio->url])]);
            }
        });
        
        // 古いurlカラムを削除
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('url')->nullable()->after('urls');
        });
        
        // urlsから最初のURLをurlに移行
        DB::table('admins')->get()->each(function ($portfolio) {
            if ($portfolio->urls) {
                $urls = json_decode($portfolio->urls, true);
                if (is_array($urls) && !empty($urls)) {
                    DB::table('admins')
                        ->where('id', $portfolio->id)
                        ->update(['url' => $urls[0]]);
                }
            }
        });
        
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('urls');
        });
    }
};