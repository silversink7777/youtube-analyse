<script setup>
import { ref } from 'vue';
import axios from 'axios';
import YouTubeHeader from '@/Components/YouTube/YouTubeHeader.vue';
import YouTubeHero from '@/Components/YouTube/YouTubeHero.vue';
import YouTubeUrlForm from '@/Components/YouTube/YouTubeUrlForm.vue';
import FeatureSection from '@/Components/YouTube/FeatureSection.vue';

const processing = ref(false);
const message = ref('');
const error = ref('');

const handleUrlSubmitted = async (data) => {
    processing.value = true;
    message.value = '';
    error.value = '';
    
    try {
        // CSRFトークンを取得
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await axios.post('/api/videos', {
            video_url: data.video_url
        }, {
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            withCredentials: true
        });
        
        message.value = response.data.message || '動画情報を保存しました';
    } catch (e) {
        error.value = e.response?.data?.message || '動画情報の保存に失敗しました';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- YouTube風ヘッダー -->
        <YouTubeHeader />
        
        <!-- ヒーローセクション -->
        <YouTubeHero />
        
        <!-- メインコンテンツ -->
        <div class="max-w-4xl mx-auto px-4 py-12">
            <!-- URL入力フォーム -->
            <YouTubeUrlForm 
                :processing="processing"
                @url-submitted="handleUrlSubmitted"
            />
            <div v-if="message" class="mt-6 text-green-600 font-semibold text-center">{{ message }}</div>
            <div v-if="error" class="mt-6 text-red-600 font-semibold text-center">{{ error }}</div>
            <!-- 機能説明セクション -->
            <FeatureSection />
        </div>
    </div>
</template>
