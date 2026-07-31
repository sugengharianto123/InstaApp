<!-- frontend/src/components/StoriesBar.vue -->
<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../api';
import CreateStoryModal from './CreateStoryModal.vue';
import ViewStoryModal from './ViewStoryModal.vue';

const stories = ref([]);
const currentUser = ref(null);
const isCreateModalOpen = ref(false);
const isViewModalOpen = ref(false);
const selectedUserStories = ref([]);
const initialStoryIndex = ref(0);

onMounted(async () => {
  try {
    const userRes = await api.get('/users/me');
    currentUser.value = userRes.data;
    await fetchStories();
  } catch (error) {
    console.error('Gagal memuat stories:', error);
  }
});

const fetchStories = async () => {
  try {
    const response = await api.get('/stories');
    stories.value = response.data;
  } catch (error) {
    console.error('Gagal memuat stories:', error);
  }
};

// PERBAIKAN: Filter hanya story user LAIN (bukan user yang login)
const otherUserStories = computed(() => {
  if (!currentUser.value) return [];
  return stories.value.filter(s => s.user.id !== currentUser.value.id);
});

// Cek apakah user sudah punya story aktif
const userHasStory = computed(() => {
  if (!currentUser.value) return false;
  return stories.value.some(s => s.user.id === currentUser.value.id);
});

// Dapatkan story user saat ini (jika ada)
const userStoryGroup = computed(() => {
  if (!currentUser.value) return null;
  return stories.value.find(s => s.user.id === currentUser.value.id);
});

const handleStoryCreated = (newStory) => {
  const existingUserIndex = stories.value.findIndex(s => s.user.id === currentUser.value.id);
  
  if (existingUserIndex !== -1) {
    stories.value[existingUserIndex].stories.unshift(newStory);
    stories.value[existingUserIndex].count++;
  } else {
    stories.value.unshift({
      user: currentUser.value,
      stories: [newStory],
      count: 1
    });
  }
};

const openStory = (userStories, index = 0) => {
  selectedUserStories.value = userStories.stories;
  initialStoryIndex.value = index;
  isViewModalOpen.value = true;
};

const openCreateStory = () => {
  isCreateModalOpen.value = true;
};

const openYourStory = () => {
  if (userStoryGroup.value) {
    openStory(userStoryGroup.value);
  } else {
    openCreateStory();
  }
};

// Expose untuk dipanggil dari HomeView
defineExpose({
  addNewStory: handleStoryCreated
});
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
    <div class="flex space-x-4 overflow-x-auto">
      
      <!-- Story Anda Sendiri -->
      <div v-if="currentUser" class="flex flex-col items-center space-y-1 cursor-pointer flex-shrink-0" @click="openYourStory">
        <div class="relative">
          <!-- Jika user BELUM punya story: tampilkan dengan icon + -->
          <img 
            v-if="!userHasStory"
            :src="currentUser.avatar || 'https://i.pravatar.cc/150?img=12'" 
            class="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
            alt="your story"
          >
          <div v-if="!userHasStory" class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold border-2 border-white">
            +
          </div>
          
          <!-- Jika user SUDAH punya story: tampilkan dengan border gradasi -->
          <div v-else class="p-0.5 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600">
            <img 
              :src="currentUser.avatar || 'https://i.pravatar.cc/150?img=12'" 
              class="w-16 h-16 rounded-full object-cover border-2 border-white"
              alt="your story"
            >
          </div>
        </div>
        <span class="text-xs text-gray-600 truncate w-16 text-center">
          {{ userHasStory ? currentUser.username : 'Your story' }}
        </span>
      </div>

      <!-- Story User Lain (SUDAH DIFILTER, tidak termasuk user yang login) -->
      <div 
        v-for="storyGroup in otherUserStories" 
        :key="storyGroup.user.id" 
        class="flex flex-col items-center space-y-1 cursor-pointer flex-shrink-0"
        @click="openStory(storyGroup)"
      >
        <div class="p-0.5 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600">
          <img 
            :src="storyGroup.user.avatar || 'https://i.pravatar.cc/150?img=' + storyGroup.user.id" 
            class="w-16 h-16 rounded-full object-cover border-2 border-white"
            :alt="storyGroup.user.username"
          >
        </div>
        <span class="text-xs text-gray-600 truncate w-16 text-center">{{ storyGroup.user.username }}</span>
      </div>

    </div>

    <!-- Create Story Modal -->
    <CreateStoryModal 
      :is-open="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @created="handleStoryCreated"
    />

    <!-- View Story Modal -->
    <ViewStoryModal 
      :is-open="isViewModalOpen"
      :stories="selectedUserStories"
      :initial-index="initialStoryIndex"
      @close="isViewModalOpen = false"
    />
  </div>
</template>