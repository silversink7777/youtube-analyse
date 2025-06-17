<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    processing: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['url-submitted']);

const youtubeUrl = ref('');
const error = ref('');

// YouTube URLの検証
const isValidYoutubeUrl = computed(() => {
    if (!youtubeUrl.value) return false;
    
    const patterns = [
        /^https?:\/\/(www\.)?youtube\.com\/watch\?v=[\w-]+/,
        /^https?:\/\/youtu\.be\/[\w-]+/,
        /^https?:\/\/(www\.)?youtube\.com\/embed\/[\w-]+/
    ];
    
    return patterns.some(pattern => pattern.test(youtubeUrl.value));
});

// 動画IDの抽出
const extractVideoId = (url) => {
    const patterns = [
        /[?&]v=([^&]+)/,
        /youtu\.be\/([^?]+)/,
        /embed\/([^?]+)/
    ];
    
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) return match[1];
    }
    
    return null;
};

// URL検証
const validateUrl = () => {
    error.value = '';
    
    if (!youtubeUrl.value.trim()) {
        error.value = 'YouTube動画のURLを入力してください。';
        return false;
    }
    
    if (!isValidYoutubeUrl.value) {
        error.value = '有効なYouTube動画のURLを入力してください。';
        return false;
    }
    
    return true;
};

// 分析開始
const startAnalysis = () => {
    if (!validateUrl()) return;
    
    const videoId = extractVideoId(youtubeUrl.value);
    if (!videoId) {
        error.value = '動画IDの抽出に失敗しました。';
        return;
    }
    
    // 親コンポーネントにイベントを発火
    emit('url-submitted', {
        video_url: youtubeUrl.value,
        video_id: videoId
    });
};

// プレースホルダーの例
const placeholderExamples = [
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ'
];

const currentPlaceholder = ref(0);

// プレースホルダーのローテーション
setInterval(() => {
    currentPlaceholder.value = (currentPlaceholder.value + 1) % placeholderExamples.length;
}, 3000);
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">
                YouTube動画URLを入力
            </h2>
            <p class="text-gray-600">
                分析したい動画のURLを入力してください
            </p>
        </div>

        <form @submit.prevent="startAnalysis" class="space-y-6">
            <div>
                <InputLabel for="youtube-url" value="YouTube動画URL" class="text-base font-medium text-gray-700" />
                <div class="mt-2 relative">
                    <TextInput
                        id="youtube-url"
                        v-model="youtubeUrl"
                        type="url"
                        class="w-full text-base py-3 px-4 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200"
                        :placeholder="placeholderExamples[currentPlaceholder]"
                        :class="{ 'border-red-500 ring-2 ring-red-200': error && youtubeUrl }"
                        :disabled="processing"
                    />
                    <div v-if="youtubeUrl && isValidYoutubeUrl" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <InputError :message="error" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <PrimaryButton
                    type="submit"
                    class="flex-1 py-3 text-base font-medium bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition-colors rounded-lg"
                    :class="{ 'opacity-50 cursor-not-allowed': processing }"
                    :disabled="processing || !isValidYoutubeUrl"
                >
                    <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ processing ? '分析中...' : '分析を開始' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template> 