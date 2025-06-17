<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import YouTubeHeader from '@/Components/YouTube/YouTubeHeader.vue';

const videos = ref([]);
const loading = ref(false);
const message = ref('');
const error = ref('');
const analyzing = ref({});
const analyzed = ref({});
const extracting = ref({});
const keywords = ref({});

const fetchVideos = async () => {
    loading.value = true;
    message.value = '';
    error.value = '';
    try {
        const res = await axios.get('/api/my-videos');
        videos.value = res.data.videos;
        // 既存のキーワードをセット
        for (const v of res.data.videos) {
            if (v.keywords) keywords.value[v.id] = v.keywords;
        }
    } catch (e) {
        error.value = '動画一覧の取得に失敗しました';
    } finally {
        loading.value = false;
    }
};

const analyzeComments = async (video) => {
    analyzing.value[video.id] = true;
    message.value = '';
    error.value = '';
    try {
        const res = await axios.post(`/api/videos/${video.id}/analyze-comments`);
        message.value = res.data.message;
        analyzed.value[video.id] = true;
        await fetchVideos();
    } catch (e) {
        error.value = e.response?.data?.message || '感情分析に失敗しました';
    } finally {
        analyzing.value[video.id] = false;
    }
};

const extractKeywords = async (video) => {
    extracting.value[video.id] = true;
    message.value = '';
    error.value = '';
    try {
        const res = await axios.post(`/api/videos/${video.id}/extract-keywords`);
        message.value = res.data.message;
        keywords.value[video.id] = res.data.keywords;
    } catch (e) {
        error.value = e.response?.data?.message || 'キーワード抽出に失敗しました';
    } finally {
        extracting.value[video.id] = false;
    }
};

onMounted(fetchVideos);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <YouTubeHeader />
        <div class="max-w-5xl mx-auto px-4 py-12">
            <h2 class="text-2xl font-bold mb-6">あなたの保存動画一覧</h2>
            <div v-if="loading" class="text-center py-8">動画を読み込み中...</div>
            <div v-if="error" class="text-red-600 text-center mb-4">{{ error }}</div>
            <div v-if="message" class="text-green-600 text-center mb-4">{{ message }}</div>
            <div v-if="videos.length === 0 && !loading" class="text-gray-500 text-center">保存された動画がありません</div>
            <div v-else class="space-y-6">
                <div v-for="video in videos" :key="video.id" class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row gap-6 items-center">
                    <img :src="video.thumbnail_url || video.thumbnail_medium_url || video.thumbnail_high_url" alt="thumbnail" class="w-40 h-24 object-cover rounded-lg border" />
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-lg text-gray-900 truncate">{{ video.title }}</div>
                        <div class="text-gray-600 text-sm mb-1">by {{ video.channel_title }}</div>
                        <div class="text-gray-500 text-xs">投稿日: {{ video.published_at ? new Date(video.published_at).toLocaleString() : '-' }}</div>
                        <div class="text-gray-500 text-xs">コメント数: {{ video.comment_count }}</div>
                        <div class="mt-2">
                            <span v-if="video.analyzed_count > 0" class="text-green-600 font-semibold">分析済み: {{ video.analyzed_count }}件</span>
                            <span v-else class="text-gray-400">未分析</span>
                        </div>
                        <div class="mt-2">
                            <button
                                class="px-3 py-1 bg-green-600 text-white rounded font-semibold hover:bg-green-700 disabled:opacity-50 mr-2"
                                :disabled="extracting[video.id]"
                                @click="extractKeywords(video)"
                            >
                                <span v-if="extracting[video.id]">抽出中...</span>
                                <span v-else>キーワード抽出</span>
                            </button>
                            <span v-if="keywords[video.id] && keywords[video.id].length > 0" class="ml-2 text-sm text-blue-700">
                                キーワード: {{ keywords[video.id].join('、') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 items-center">
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700 disabled:opacity-50"
                            :disabled="analyzing[video.id] || video.analyzed_count === video.comment_count"
                            @click="analyzeComments(video)"
                        >
                            <span v-if="analyzing[video.id]">分析中...</span>
                            <span v-else-if="video.analyzed_count === video.comment_count">全件分析済み</span>
                            <span v-else>コメント感情分析</span>
                        </button>
                        <a :href="video.youtube_url" target="_blank" class="text-xs text-blue-500 underline mt-2">YouTubeで見る</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 