<script setup>
// YouTube風のヘッダーコンポーネント
import { Link, router, usePage } from '@inertiajs/vue3';

// ページデータの取得
const page = usePage();

// ログアウト機能
const logout = () => {
    router.post(route('logout'));
};

// 認証状態の確認
const isAuthenticated = page.props.auth?.user;
</script>

<template>
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- YouTube風ロゴ -->
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        <span class="ml-2 text-xl font-semibold text-gray-900">YouTube</span>
                    </div>
                    <span class="text-gray-400">|</span>
                    <span class="text-lg font-medium text-gray-700">コメント分析。</span>
                </div>

                <!-- ナビゲーション -->
                <nav class="flex items-center space-x-6">
                    <!-- 認証済みの場合のみ表示 -->
                    <template v-if="isAuthenticated">
                        <Link href="/dashboard" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
                            ホーム
                        </Link>
                        <Link href="/my-videos" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
                            保存動画一覧
                        </Link>
                        <a href="#" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
                            履歴
                        </a>
                        <a href="#" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
                            設定
                        </a>
                        
                        <!-- ログアウトボタン -->
                        <button 
                            @click="logout"
                            class="text-gray-700 hover:text-red-600 transition-colors font-medium flex items-center space-x-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>ログアウト</span>
                        </button>
                    </template>
                    
                    <!-- 未認証の場合 -->
                    <template v-else>
                        <Link href="/login" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
                            ログイン
                        </Link>
                        <Link href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                            新規登録
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template> 