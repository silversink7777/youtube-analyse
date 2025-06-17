<script setup>
import YouTubeHeader from '@/Components/YouTube/YouTubeHeader.vue';
import YouTubeUrlInput from '@/Components/YouTubeUrlInput.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <YouTubeHeader />
        <div class="max-w-4xl mx-auto px-4 pt-8">
            <YouTubeUrlInput />
        </div>
    </div>
</template>
