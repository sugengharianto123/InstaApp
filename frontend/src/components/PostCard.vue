<!-- frontend/src/components/PostCard.vue -->
<script setup>
import { ref } from 'vue';

const props = defineProps({
  post: Object
});

const isLiked = ref(props.post.is_liked);
const likesCount = ref(props.post.likes_count);
const newComment = ref('');

const toggleLike = () => {
  isLiked.value = !isLiked.value;
  likesCount.value += isLiked.value ? 1 : -1;
  // Nantinya di sini kita akan panggil API Laravel
};
</script>

<template>
  <article class="bg-white border border-gray-200 rounded-lg mb-6 overflow-hidden">
    <!-- Header Post -->
    <header class="flex items-center justify-between p-3">
      <div class="flex items-center space-x-3">
        <img :src="post.user.avatar" class="w-8 h-8 rounded-full object-cover" alt="avatar">
        <span class="font-semibold text-sm text-gray-800">{{ post.user.username }}</span>
      </div>
      <button class="text-gray-600 hover:text-gray-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>
      </button>
    </header>

    <!-- Gambar Post -->
    <div class="w-full bg-gray-100">
      <img :src="post.image" class="w-full object-cover max-h-[600px]" alt="post image">
    </div>

    <!-- Aksi (Like, Comment) -->
    <div class="p-3">
      <div class="flex items-center space-x-4 mb-2">
        <button @click="toggleLike" class="focus:outline-none transition-transform active:scale-125">
          <svg v-if="isLiked" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-800 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
        </button>
        <button class="focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-800 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        </button>
      </div>

      <!-- Likes Count -->
      <p class="font-semibold text-sm text-gray-800 mb-1">{{ likesCount }} suka</p>

      <!-- Caption -->
      <p class="text-sm text-gray-800 mb-2">
        <span class="font-semibold mr-2">{{ post.user.username }}</span>
        {{ post.caption }}
      </p>

      <!-- Comments -->
      <div class="space-y-1 mb-2">
        <p v-for="comment in post.comments" :key="comment.id" class="text-sm text-gray-800">
          <span class="font-semibold mr-2">{{ comment.user.username }}</span>
          {{ comment.text }}
        </p>
      </div>

      <!-- Timestamp -->
      <p class="text-xs text-gray-400 uppercase mb-3">{{ post.created_at }}</p>

      <!-- Add Comment Input -->
      <div class="flex items-center border-t border-gray-100 pt-3">
        <input 
          v-model="newComment" 
          type="text" 
          placeholder="Tambahkan komentar..." 
          class="flex-1 text-sm outline-none bg-transparent placeholder-gray-400"
        >
        <button 
          v-if="newComment" 
          class="text-sm font-semibold text-blue-500 hover:text-blue-700"
        >
          Kirim
        </button>
      </div>
    </div>
  </article>
</template>