<!-- frontend/src/views/ProfileView.vue -->
<script setup>
import AppLayout from '../components/AppLayout.vue';
import { ref } from 'vue';
import { currentUser, mockPosts } from '../mockData.js';

// Filter post milik user yang sedang login
const myPosts = mockPosts.filter(p => p.user.id === currentUser.id);

const activeTab = ref('posts');
</script>

<template>
  <AppLayout>
    <!-- Profile Header -->
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-start space-x-12 px-8 py-8">
        <!-- Avatar -->
        <div class="flex-shrink-0">
          <img 
            :src="currentUser.avatar" 
            class="w-36 h-36 rounded-full object-cover border border-gray-200" 
            alt="profile"
          >
        </div>

        <!-- Profile Info -->
        <div class="flex-1">
          <!-- Username & Actions -->
          <div class="flex items-center space-x-6 mb-4">
            <h2 class="text-xl text-gray-800 font-light">{{ currentUser.username }}</h2>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-4 py-1.5 rounded text-sm">
              Edit profile
            </button>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-4 py-1.5 rounded text-sm">
              View archive
            </button>
            <button class="text-gray-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>

          <!-- Stats -->
          <div class="flex space-x-10 mb-4">
            <span class="text-gray-800"><strong>{{ myPosts.length }}</strong> posts</span>
            <span class="text-gray-800"><strong>1.2K</strong> followers</span>
            <span class="text-gray-800"><strong>350</strong> following</span>
          </div>

          <!-- Bio -->
          <div class="text-sm">
            <p class="text-gray-800 font-semibold">{{ currentUser.username }}</p>
            <p class="text-gray-700">Digital Creator 📸</p>
            <p class="text-gray-700">Living life one photo at a time ✨</p>
            <p class="text-gray-700">📍 Jakarta, Indonesia</p>
            <a href="#" class="text-blue-900 font-semibold">github.com/{{ currentUser.username }}</a>
          </div>
        </div>
      </div>

      <!-- Story Highlights (Optional) -->
      <div class="flex space-x-6 px-8 pb-6 border-t border-gray-200 pt-6">
        <div class="flex flex-col items-center space-y-1">
          <div class="w-16 h-16 rounded-full border border-gray-300 p-1">
            <div class="w-full h-full rounded-full bg-gray-200"></div>
          </div>
          <span class="text-xs text-gray-800">New</span>
        </div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-t border-gray-200">
      <div class="flex justify-center space-x-12">
        <button 
          @click="activeTab = 'posts'"
          :class="['flex items-center space-x-2 py-4 text-xs font-semibold uppercase tracking-wider', 
                   activeTab === 'posts' ? 'text-gray-800 border-t border-gray-800 -mt-px' : 'text-gray-400']"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          <span>Posts</span>
        </button>
        
        <button 
          @click="activeTab = 'saved'"
          :class="['flex items-center space-x-2 py-4 text-xs font-semibold uppercase tracking-wider', 
                   activeTab === 'saved' ? 'text-gray-800 border-t border-gray-800 -mt-px' : 'text-gray-400']"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
          </svg>
          <span>Saved</span>
        </button>
        
        <button 
          @click="activeTab = 'tagged'"
          :class="['flex items-center space-x-2 py-4 text-xs font-semibold uppercase tracking-wider', 
                   activeTab === 'tagged' ? 'text-gray-800 border-t border-gray-800 -mt-px' : 'text-gray-400']"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>Tagged</span>
        </button>
      </div>
    </div>

    <!-- Posts Grid -->
    <div v-if="activeTab === 'posts'" class="grid grid-cols-3 gap-1 mt-1">
      <div 
        v-for="post in myPosts" 
        :key="post.id" 
        class="aspect-square bg-gray-100 relative group cursor-pointer overflow-hidden"
      >
        <img :src="post.image" class="w-full h-full object-cover" alt="post">
        
        <!-- Hover Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
          <div class="flex space-x-6 text-white font-semibold">
            <span class="flex items-center space-x-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
              <span>{{ post.likes_count }}</span>
            </span>
            <span class="flex items-center space-x-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
              </svg>
              <span>{{ post.comments.length }}</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="myPosts.length === 0" class="col-span-3 text-center py-16">
        <div class="flex justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-2xl text-gray-800 font-light mb-2">No Posts Yet</h3>
      </div>
    </div>

    <!-- Saved & Tagged Empty State -->
    <div v-if="activeTab !== 'posts'" class="text-center py-16">
      <p class="text-gray-500">This tab will be available soon.</p>
    </div>
  </AppLayout>
</template>