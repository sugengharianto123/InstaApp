<!-- frontend/src/components/AppLayout.vue -->
<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import CreatePostModal from './CreatePostModal.vue';
import api from '../api';

const route = useRoute();
const router = useRouter();
const isCreateModalOpen = ref(false);

const emit = defineEmits(['post-created', 'open-story-modal']);

const handlePostSubmit = async (postData) => {
  try {
    const formData = new FormData();
    formData.append('image', postData.image);
    formData.append('caption', postData.caption);

    const response = await api.post('/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    emit('post-created', response.data.post);
    alert('Postingan berhasil dibuat!');
  } catch (error) {
    console.error(error);
    alert('Gagal membuat postingan.');
  }
};

const handleLogout = () => {
  if (confirm('Yakin ingin logout?')) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  }
};

const openCreateStory = () => {
  emit('open-story-modal');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    
    <!-- Sidebar Collapsible -->
    <aside class="sidebar-collapsible fixed top-0 left-0 h-screen bg-white border-r border-gray-200 z-30 flex flex-col">
      
      <!-- Logo (dengan overflow hidden sendiri agar tidak bocor) -->
      <div class="logo-container px-5 py-6 flex items-center h-20">
        <h1 
          class="sidebar-text text-3xl font-black leading-tight bg-gradient-to-r from-[#4FACFE] via-[#0095F6] to-[#0057D9] bg-clip-text text-transparent whitespace-nowrap"
          style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;"
        >
          InstaApp
        </h1>
      </div>

      <!-- Navigation -->
      <nav class="flex flex-col space-y-1 px-3 flex-1">
        
        <!-- Home -->
        <router-link 
          to="/" 
          class="flex items-center space-x-4 p-3 rounded-lg transition-all duration-200 whitespace-nowrap"
          :class="route.path === '/' ? 'font-semibold text-gray-900' : 'text-gray-700 hover:bg-gray-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="sidebar-text text-base">Home</span>
        </router-link>
        
        <!-- Profile -->
        <router-link 
          to="/profile" 
          class="flex items-center space-x-4 p-3 rounded-lg transition-all duration-200 whitespace-nowrap"
          :class="route.path === '/profile' ? 'font-semibold text-gray-900' : 'text-gray-700 hover:bg-gray-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="sidebar-text text-base">Profile</span>
        </router-link>
        
        <!-- Create dengan Submenu -->
        <div class="create-wrapper group relative">
          <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-100 transition-all duration-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 flex-shrink-0 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="sidebar-text text-base">Create</span>
          </div>
          
          <!-- Submenu (di dalam wrapper, pakai absolute) -->
          <div class="submenu absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 w-48">
            <button 
              @click.stop="isCreateModalOpen = true"
              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-3 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Feed Post</span>
            </button>
            
            <button 
              @click.stop="openCreateStory"
              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-3 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>Story</span>
            </button>
          </div>
        </div>

        <!-- Logout -->
        <button 
          @click="handleLogout"
          class="flex items-center space-x-4 text-gray-700 p-3 rounded-lg hover:bg-gray-100 transition-all duration-200 w-full whitespace-nowrap mt-auto"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span class="sidebar-text text-base">Logout</span>
        </button>
      </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 ml-[72px]">
      <div class="max-w-6xl mx-auto flex justify-center p-4 md:p-8">
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

<style scoped>
/* Sidebar: overflow visible agar submenu bisa keluar */
.sidebar-collapsible {
  width: 72px;
  transition: width 0.3s ease-in-out;
  overflow: visible;
}

.sidebar-collapsible:hover {
  width: 244px;
}

/* Logo container: overflow hidden sendiri agar teks tidak bocor */
.logo-container {
  overflow: hidden;
  flex-shrink: 0;
}

.sidebar-text {
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
  white-space: nowrap;
}

.sidebar-collapsible:hover .sidebar-text {
  opacity: 1;
  transition-delay: 0.1s;
}

/* Wrapper Create: pastikan tidak terpotong */
.create-wrapper {
  position: relative;
  overflow: visible !important;
}

/* Submenu: pastikan di atas semua */
.submenu {
  z-index: 9999;
  overflow: visible !important;
}
</style>