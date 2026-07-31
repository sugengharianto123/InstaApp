<!-- frontend/src/views/HomeView.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '../components/AppLayout.vue';
import PostCard from '../components/PostCard.vue';
import CommentModal from '../components/CommentModal.vue';
import StoriesBar from '../components/StoriesBar.vue';
import RightSidebar from '../components/RightSidebar.vue';
import api from '../api';

const posts = ref([]);
const loading = ref(true);
const selectedPost = ref(null);
const isCommentModalOpen = ref(false);

const fetchPosts = async () => {
  try {
    loading.value = true;
    const response = await api.get('/posts');
    posts.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil posts:', error);
  } finally {
    loading.value = false;
  }
};

const addNewPost = (newPost) => {
  posts.value.unshift(newPost);
};

const openCommentModal = (post) => {
  selectedPost.value = post;
  isCommentModalOpen.value = true;
};

const closeCommentModal = () => {
  isCommentModalOpen.value = false;
  selectedPost.value = null;
};

onMounted(() => {
  fetchPosts();
});
</script>

<template>
  <AppLayout @post-created="addNewPost">
    <!-- Layout 2 Kolom: Feed + Sidebar -->
    <div class="flex w-full">
      
      <!-- Kolom Kiri: Feed -->
      <div class="flex-1 max-w-xl">
        
        <!-- Stories Bar -->
        <StoriesBar />

        <!-- Loading State -->
        <div v-if="loading" class="bg-white border border-gray-200 rounded-lg p-10 text-center">
          <p class="text-gray-500">Memuat postingan...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="posts.length === 0" class="bg-white border border-gray-200 rounded-lg p-16 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="text-2xl text-gray-800 font-light mb-2">Belum Ada Postingan</h3>
          <p class="text-gray-500">Jadilah yang pertama membuat postingan!</p>
        </div>

        <!-- List Posts -->
        <PostCard 
          v-else
          v-for="post in posts" 
          :key="post.id" 
          :post="post"
          @open-comments="openCommentModal"
        />
      </div>

      <!-- Kolom Kanan: Sidebar -->
      <RightSidebar />

    </div>

    <!-- Comment Modal -->
    <CommentModal 
      :is-open="isCommentModalOpen"
      :post="selectedPost"
      @close="closeCommentModal"
    />
  </AppLayout>
</template>