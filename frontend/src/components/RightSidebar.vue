<!-- frontend/src/components/RightSidebar.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const currentUser = ref(null);
const suggestedUsers = ref([]);

onMounted(async () => {
  try {
    const userRes = await api.get('/users/me');
    currentUser.value = userRes.data;

    const suggestedRes = await api.get('/users/suggested');
    suggestedUsers.value = suggestedRes.data;
  } catch (error) {
    console.error('Gagal memuat sidebar:', error);
  }
});
</script>

<template>
  <aside class="hidden xl:block w-80 pl-8 pt-2">
    
    <!-- Profile Card -->
    <div v-if="currentUser" class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <img 
          :src="currentUser.avatar || 'https://i.pravatar.cc/150?img=12'" 
          class="w-14 h-14 rounded-full object-cover"
          alt="profile"
        >
        <div>
          <p class="font-semibold text-sm text-gray-800">{{ currentUser.username }}</p>
          <p class="text-sm text-gray-500">{{ currentUser.name }}</p>
        </div>
      </div>
      <button class="text-xs font-semibold text-blue-500 hover:text-blue-700">
        Switch
      </button>
    </div>

    <!-- Suggested For You -->
    <div class="mb-4">
      <div class="flex items-center justify-between mb-3">
        <span class="text-sm font-semibold text-gray-500">Suggested for you</span>
        <button class="text-xs font-semibold text-gray-800 hover:text-gray-600">
          See All
        </button>
      </div>

      <div class="space-y-3">
        <div 
          v-for="user in suggestedUsers.slice(0, 5)" 
          :key="user.id" 
          class="flex items-center justify-between"
        >
          <div class="flex items-center space-x-3">
            <img 
              :src="user.avatar || 'https://i.pravatar.cc/150?img=' + user.id" 
              class="w-9 h-9 rounded-full object-cover"
              :alt="user.name"
            >
            <div>
              <p class="text-sm font-semibold text-gray-800">{{ user.username }}</p>
              <p class="text-xs text-gray-500">Suggested for you</p>
            </div>
          </div>
          <button class="text-xs font-semibold text-blue-500 hover:text-blue-700">
            Follow
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Links -->
    <div class="mt-8 text-xs text-gray-400 space-y-2">
      <p class="leading-relaxed">
        About · Help · Press · API · Jobs · Privacy · Terms · Locations · Language
      </p>
      <p>© 2026 INSTAAPP FROM SEVIMA</p>
    </div>

  </aside>
</template>