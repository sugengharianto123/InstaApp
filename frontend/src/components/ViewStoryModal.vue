<!-- frontend/src/components/ViewStoryModal.vue -->
<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import api from '../api';

const props = defineProps({
  isOpen: Boolean,
  stories: Array, // Array of stories dari user tertentu
  initialIndex: Number
});

const emit = defineEmits(['close']);

const currentIndex = ref(props.initialIndex || 0);
const progress = ref(0);
const progressInterval = ref(null);
const currentStory = ref(null);

const STORY_DURATION = 5000; // 5 detik per story

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    currentIndex.value = props.initialIndex || 0;
    loadStory();
  } else {
    clearProgress();
  }
});

const loadStory = () => {
  if (props.stories && props.stories[currentIndex.value]) {
    currentStory.value = props.stories[currentIndex.value];
    startProgress();
  }
};

const startProgress = () => {
  clearProgress();
  progress.value = 0;
  
  const interval = 50; // Update setiap 50ms
  const increment = 100 / (STORY_DURATION / interval);
  
  progressInterval.value = setInterval(() => {
    progress.value += increment;
    
    if (progress.value >= 100) {
      nextStory();
    }
  }, interval);
};

const clearProgress = () => {
  if (progressInterval.value) {
    clearInterval(progressInterval.value);
    progressInterval.value = null;
  }
};

const nextStory = () => {
  if (currentIndex.value < props.stories.length - 1) {
    currentIndex.value++;
    loadStory();
  } else {
    closeModal();
  }
};

const prevStory = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
    loadStory();
  }
};

const closeModal = () => {
  clearProgress();
  emit('close');
};

// Keyboard navigation
const handleKeydown = (e) => {
  if (e.key === 'ArrowRight') nextStory();
  if (e.key === 'ArrowLeft') prevStory();
  if (e.key === 'Escape') closeModal();
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  clearProgress();
});
</script>

<template>
  <div v-if="isOpen && currentStory" class="fixed inset-0 bg-black z-50 flex items-center justify-center" @click.self="closeModal">
    
    <!-- Progress Bar -->
    <div class="absolute top-0 left-0 right-0 p-2 z-10">
      <div class="flex space-x-1">
        <div 
          v-for="(story, index) in stories" 
          :key="story.id"
          class="flex-1 h-1 bg-gray-600 rounded-full overflow-hidden"
        >
          <div 
            class="h-full bg-white transition-all duration-100"
            :style="{ 
              width: index < currentIndex ? '100%' : index === currentIndex ? progress + '%' : '0%' 
            }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Header: User Info -->
    <div class="absolute top-8 left-0 right-0 p-4 flex items-center justify-between z-10">
      <div class="flex items-center space-x-3">
        <img 
          :src="currentStory.user?.avatar || 'https://i.pravatar.cc/150?img=12'" 
          class="w-10 h-10 rounded-full object-cover border-2 border-white"
          alt="avatar"
        >
        <div>
          <p class="text-white font-semibold text-sm">{{ currentStory.user?.username }}</p>
          <p class="text-gray-300 text-xs">{{ currentStory.time_ago }}</p>
        </div>
      </div>
      <button @click="closeModal" class="text-white hover:text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Story Image -->
    <div class="relative w-full h-full flex items-center justify-center">
      <img 
        :src="currentStory.image" 
        class="max-w-full max-h-full object-contain"
        alt="story"
      >

      <!-- Caption -->
      <div v-if="currentStory.caption" class="absolute bottom-20 left-0 right-0 p-4 text-center">
        <p class="text-white text-lg font-semibold bg-black bg-opacity-50 inline-block px-4 py-2 rounded-lg">
          {{ currentStory.caption }}
        </p>
      </div>
    </div>

    <!-- Navigation Areas -->
    <div class="absolute inset-0 flex">
      <div class="w-1/3 h-full cursor-pointer" @click="prevStory"></div>
      <div class="w-1/3 h-full"></div>
      <div class="w-1/3 h-full cursor-pointer" @click="nextStory"></div>
    </div>

  </div>
</template>