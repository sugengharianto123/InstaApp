<!-- frontend/src/components/StoriesBar.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const stories = ref([]);
const currentUser = ref(null);

onMounted(async () => {
  try {
    const userRes = await api.get('/users/me');
    currentUser.value = userRes.data;

    const suggestedRes = await api.get('/users/suggested');
    stories.value = suggestedRes.data;
  } catch (error) {
    console.error('Gagal memuat stories:', error);
  }
});
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
    <div class="flex space-x-4 overflow-x-auto">
      
      <!-- Story Anda Sendiri -->
      <div v-if="currentUser" class="flex flex-col items-center space-y-1 cursor-pointer flex-shrink-0">
        <div class="relative">
          <img 
            :src="currentUser.avatar || 'https://i.pravatar.cc/150?img=12'" 
            class="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
            alt="your story"
          >
          <div class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold border-2 border-white">
            +
          </div>
        </div>
        <span class="text-xs text-gray-600">Your story</span>
      </div>

      <!-- Story User Lain -->
      <div 
        v-for="user in stories" 
        :key="user.id" 
        class="flex flex-col items-center space-y-1 cursor-pointer flex-shrink-0"
      >
        <div class="p-0.5 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600">
          <img 
            :src="user.avatar || 'https://i.pravatar.cc/150?img=' + user.id" 
            class="w-16 h-16 rounded-full object-cover border-2 border-white"
            :alt="user.name"
          >
        </div>
        <span class="text-xs text-gray-600">{{ user.name.split(' ')[0] }}</span>
      </div>

    </div>
  </div>
</template>