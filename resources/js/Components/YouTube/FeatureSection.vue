<script setup>
import FeatureCard from './FeatureCard.vue';

const features = [
    {
        icon: 'chart',
        title: '感情分析',
        description: 'コメントの感情を自動で分析し、ポジティブ・ネガティブ・ニュートラルに分類',
        color: 'blue'
    },
    {
        icon: 'hashtag',
        title: 'キーワード抽出',
        description: '頻出キーワードを自動抽出し、クラウド表示で可視化',
        color: 'green'
    },
    {
        icon: 'analytics',
        title: '統計分析',
        description: 'コメント数、いいね数、投稿時間などの詳細な統計情報',
        color: 'purple'
    }
];
</script>

<template>
    <div class="mt-12">
        <div class="text-center mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                分析機能
            </h3>
            <p class="text-gray-600">
                以下の機能で動画コメントを詳細に分析できます
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <FeatureCard
                v-for="feature in features"
                :key="feature.title"
                :icon="feature.icon"
                :title="feature.title"
                :description="feature.description"
                :color="feature.color"
            />
        </div>
    </div>
</template> 