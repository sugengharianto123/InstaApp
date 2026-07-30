<!-- frontend/src/components/AppLayout.vue -->
<script setup>
import { ref } from 'vue';
import CreatePostModal from './CreatePostModal.vue';

const isCreateModalOpen = ref(false);

const handlePostSubmit = (postData) => {
  console.log('Post data:', postData);
  alert('Post berhasil dibuat!');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar Kiri (Fixed) -->
    
    <aside class="w-64 bg-white border-r border-gray-200 p-6 hidden md:flex flex-col fixed top-0 left-0 h-screen">
      <h1 class="text-2xl font-bold mb-10 text-gray-800">InstaApp</h1>
      <nav class="flex flex-col space-y-2">
        <router-link 
          to="/" 
          class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200"
          :class="$route.path === '/' ? 'text-gray-800 font-semibold' : 'text-gray-600 hover:bg-gray-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="text-base">Home</span>
        </router-link>
        
        <router-link 
          to="/profile" 
          class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200"
          :class="$route.path === '/profile' ? 'text-gray-800 font-semibold' : 'text-gray-600 hover:bg-gray-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="text-base">Profile</span>
        </router-link>
        
        <button 
          @click="isCreateModalOpen = true" 
          class="flex items-center space-x-3 text-gray-600 p-3 rounded-lg hover:bg-gray-100 text-left transition-all duration-200 w-full"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span class="text-base">Create</span>
        </button>
      </nav>
    </aside>

    <!-- Konten Utama (Dengan Margin Kiri) -->
    <main class="flex-1 flex justify-center p-4 md:p-8 md:ml-64">
      <div class="w-full max-w-xl">
        <slot></slot>
      </div>
    </main>

    <!-- Modal Create Post -->
    <CreatePostModal 
      :is-open="isCreateModalOpen" 
      @close="isCreateModalOpen = false"
      @submit="handlePostSubmit"
    />
  </div>
</template>