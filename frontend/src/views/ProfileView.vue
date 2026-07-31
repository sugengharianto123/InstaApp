<!-- frontend/src/views/ProfileView.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '../components/AppLayout.vue';
import CommentModal from '../components/CommentModal.vue';
import EditProfileModal from '../components/EditProfileModal.vue';
import api from '../api';

const user = ref(null);
const posts = ref([]);
const loading = ref(true);

// State untuk Modal
const isEditModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const selectedPost = ref(null);

const fetchProfileData = async () => {
  try {
    loading.value = true;
    
    const userRes = await api.get('/users/me');
    user.value = userRes.data;

    const postsRes = await api.get('/users/me/posts');
    posts.value = postsRes.data;
  } catch (error) {
    console.error('Gagal memuat profil:', error);
  } finally {
    loading.value = false;
  }
};

const handleProfileUpdated = (updatedUser) => {
  user.value = updatedUser;
  // Update juga data user di localStorage agar konsisten
  localStorage.setItem('user', JSON.stringify(updatedUser));
};

const openPostDetail = (post) => {
  selectedPost.value = post;
  isCommentModalOpen.value = true;
};

const closePostDetail = () => {
  isCommentModalOpen.value = false;
  selectedPost.value = null;
};

onMounted(() => {
  fetchProfileData();
});
</script>

<template>
  <AppLayout>
    <div v-if="!loading && user" class="w-full max-w-4xl mx-auto px-4 py-8">
      
      <!-- 1. HEADER PROFIL -->
      <div class="flex flex-col md:flex-row items-center md:items-start mb-12 border-b border-gray-200 pb-10">
        
        <!-- Avatar Besar -->
        <div class="md:w-1/3 flex justify-center md:justify-start mb-6 md:mb-0">
          <div class="relative group cursor-pointer" @click="isEditModalOpen = true">
            <img 
              :src="user.avatar || 'https://i.pravatar.cc/300?img=12'" 
              class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border border-gray-200"
              alt="profile"
            >
            <!-- Overlay saat hover -->
            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
              <span class="text-white text-xs font-semibold">Ubah Foto</span>
            </div>
          </div>
        </div>

        <!-- Info Profil -->
        <div class="md:w-2/3 flex flex-col items-center md:items-start">
          
          <!-- Username & Tombol Edit -->
          <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 mb-6">
            <h2 class="text-xl font-normal text-gray-800">{{ user.username }}</h2>
            <button 
              @click="isEditModalOpen = true"
              class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-sm py-1.5 px-4 rounded-lg transition"
            >
              Edit Profile
            </button>
            <button class="text-gray-800 p-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>

          <!-- Statistik -->
          <div class="flex space-x-10 mb-6 text-base">
            <div><span class="font-semibold">{{ posts.length }}</span> postingan</div>
            <div><span class="font-semibold">0</span> pengikut</div>
            <div><span class="font-semibold">0</span> mengikuti</div>
          </div>

          <!-- Bio -->
          <div class="text-center md:text-left">
            <p class="font-semibold text-sm text-gray-800">{{ user.name }}</p>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ user.bio || 'Belum ada bio. Klik Edit Profile untuk menambahkan.' }}</p>
          </div>
        </div>
      </div>

      <!-- 2. TAB NAVIGASI -->
      <div class="flex justify-center border-t border-gray-200 -mt-10 mb-4">
        <button class="flex items-center space-x-2 py-4 border-t border-gray-800 -mt-px text-xs font-semibold tracking-widest text-gray-800 uppercase">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          <span>Postingan</span>
        </button>
      </div>

      <!-- 3. GRID POSTINGAN (Klik untuk buka popup) -->
      <div v-if="posts.length > 0" class="grid grid-cols-3 gap-1 md:gap-6">
        <div 
          v-for="post in posts" 
          :key="post.id" 
          @click="openPostDetail(post)"
          class="relative aspect-square group cursor-pointer overflow-hidden bg-gray-100"
        >
          <img :src="post.image" class="w-full h-full object-cover" alt="post">
          
          <!-- Overlay Hover -->
          <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <div class="flex items-center space-x-6 text-white font-bold">
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span>{{ post.likes_count }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                <span>{{ post.comments_count || 0 }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <div class="border-2 border-gray-800 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <h3 class="text-2xl font-light text-gray-800 mb-2">Belum Ada Postingan</h3>
        <p class="text-gray-500 text-sm">Mulai bagikan momen Anda!</p>
      </div>

    </div>

    <!-- Loading State -->
    <div v-else class="w-full max-w-4xl mx-auto px-4 py-20 text-center">
      <p class="text-gray-500">Memuat profil...</p>
    </div>

    <!-- Edit Profile Modal -->
    <EditProfileModal 
      :is-open="isEditModalOpen"
      :user="user"
      @close="isEditModalOpen = false"
      @updated="handleProfileUpdated"
    />

    <!-- Comment/Post Detail Modal -->
    <CommentModal 
      :is-open="isCommentModalOpen"
      :post="selectedPost"
      @close="closePostDetail"
    />
  </AppLayout>
</template>