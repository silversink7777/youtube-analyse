<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import YouTubeHeader from '@/Components/YouTube/YouTubeHeader.vue';
import WordFrequencyChart from '@/Components/WordFrequencyChart.vue';

const videos = ref([]);
const loading = ref(false);
const message = ref('');
const error = ref('');
const analyzing = ref({});
const analyzed = ref({});
const extracting = ref({});
const keywords = ref({});
const wordFrequency = ref({});
const analyzingWordFreq = ref({});

// CSRFトークンを取得する関数
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

// axiosのデフォルト設定
const setupAxios = () => {
    const token = getCsrfToken();
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.withCredentials = true;
};

const fetchVideos = async () => {
    loading.value = true;
    message.value = '';
    error.value = '';
    try {
        setupAxios();
        const res = await axios.get('/api/my-videos');
        videos.value = res.data.videos;
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
        setupAxios();
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
        setupAxios();
        const res = await axios.post(`/api/videos/${video.id}/analyze-word-frequency`);
        message.value = res.data.message;
        wordFrequency.value[video.id] = res.data.wordFrequency;
    } catch (e) {
        error.value = e.response?.data?.message || '単語頻度分析に失敗しました';
    } finally {
        extracting.value[video.id] = false;
    }
};

onMounted(fetchVideos);
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <YouTubeHeader />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">あなたの保存動画一覧</h1>
                <p class="text-gray-600">保存したYouTube動画の分析と管理を行えます</p>
            </div>

            <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ error }}</p>
                    </div>
                </div>
            </div>

            <div v-if="message" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ message }}</p>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">動画を読み込み中...</span>
            </div>

            <div v-else-if="videos.length === 0" class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">保存された動画がありません</h3>
                <p class="text-gray-500">YouTube動画を保存すると、ここに表示されます</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="video in videos" :key="video.id" class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <div class="relative">
                        <img 
                            :src="video.thumbnail_url || video.thumbnail_medium_url || video.thumbnail_high_url" 
                            :alt="video.title"
                            class="w-full h-48 object-cover"
                        />
                        <div class="absolute top-2 right-2">
                            <span v-if="video.analyzed_count > 0" 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                分析済み
                            </span>
                            <span v-else 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                未分析
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ video.title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">by {{ video.channel_title }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                {{ video.published_at ? new Date(video.published_at).toLocaleDateString() : '-' }}
                            </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                </svg>
                                コメント数: {{ video.comment_count }}
                            </div>
                            <div v-if="video.analyzed_count > 0" class="flex items-center text-xs text-green-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                分析済み: {{ video.analyzed_count }}件
                            </div>
                        </div>

                        <!-- 単語頻度チャート表示 -->
                        <div v-if="wordFrequency[video.id] && wordFrequency[video.id].length > 0" class="mb-4">
                            <div class="text-xs font-medium text-gray-700 mb-2">よく使われている単語:</div>
                            <WordFrequencyChart 
                                :word-frequency="wordFrequency[video.id]" 
                                :max-words="8"
                            />
                        </div>

                        <!-- キーワード表示 -->
                        <div v-if="keywords[video.id] && keywords[video.id].length > 0" class="mb-4">
                            <div class="text-xs font-medium text-gray-700 mb-2">キーワード:</div>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="keyword in keywords[video.id].slice(0, 3)" :key="keyword" 
                                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ keyword }}
                                </span>
                                <span v-if="keywords[video.id].length > 3" 
                                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    +{{ keywords[video.id].length - 3 }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button
                                class="w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-medium hover:from-purple-700 hover:to-purple-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center"
                                :disabled="extracting[video.id]"
                                @click="extractKeywords(video)"
                            >
                                <svg v-if="extracting[video.id]" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span v-if="extracting[video.id]">分析中...</span>
                                <span v-else>単語頻度分析</span>
                            </button>

                            <button
                                class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center"
                                :disabled="analyzing[video.id] || video.analyzed_count === video.comment_count"
                                @click="analyzeComments(video)"
                            >
                                <svg v-if="analyzing[video.id]" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                </svg>
                                <span v-if="analyzing[video.id]">分析中...</span>
                                <span v-else-if="video.analyzed_count === video.comment_count">全件分析済み</span>
                                <span v-else>コメント感情分析</span>
                            </button>

                            <a :href="video.youtube_url" 
                               target="_blank" 
                               class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                YouTubeで見る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style> 