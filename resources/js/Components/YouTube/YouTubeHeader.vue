<script setup>
// YouTube風のヘッダーコンポーネント
import { Link } from '@inertiajs/vue3';
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
                </nav>
            </div>
        </div>
    </div>
</template> 