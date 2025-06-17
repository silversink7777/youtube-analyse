<script setup>
// YouTube風のヒーローセクション
</script>

<template>
    <div class="bg-gradient-to-br from-red-50 to-white py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                YouTube動画
                <span class="text-red-600">コメント分析</span>
            </h1>
            
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                動画のURLを入力するだけで、コメントの感情分析とキーワード分析を自動で実行。
                <br class="hidden sm:block">
                視聴者の反応を深く理解し、コンテンツ改善に活用しましょう。
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    無料で利用可能
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    リアルタイム分析
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    詳細レポート
                </div>
            </div>
        </div>
    </div>
</template> 