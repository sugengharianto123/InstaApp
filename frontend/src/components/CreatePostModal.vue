<!-- frontend/src/components/CreatePostModal.vue -->
<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'submit']);

const step = ref(1); // 1 = Pilih Foto, 2 = Preview & Caption
const caption = ref('');
const imageFile = ref(null);
const imagePreview = ref(null);

// Reset saat modal ditutup
watch(() => props.isOpen, (newVal) => {
  if (!newVal) {
    setTimeout(() => {
      step.value = 1;
      caption.value = '';
      imageFile.value = null;
      imagePreview.value = null;
    }, 200);
  }
});

const handleImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
    step.value = 2;
  }
};

const handleSubmit = () => {
  emit('submit', {
    caption: caption.value,
    image: imageFile.value
  });
  emit('close');
};
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4"
      @click.self="emit('close')"
    >
      <button
        @click="emit('close')"
        class="absolute top-6 right-6 text-white hover:text-gray-300 transition"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="flex items-center justify-center py-3.5 border-b border-gray-100">
          <h2 class="text-base font-semibold text-gray-900">Create new post</h2>
        </div>

        <div v-if="step === 1" class="py-20 px-4">
          <div class="flex flex-col items-center justify-center">
            <div class="mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <rect x="2" y="4" width="13" height="13" rx="2" stroke="currentColor" stroke-width="1.3"/>
                <circle cx="6.2" cy="8.2" r="1.2" fill="currentColor"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M4 14.5l3-3 2.5 2.5 2-2 3.5 3.5"/>
                <rect x="10.5" y="10.5" width="11" height="11" rx="2" fill="white" stroke="currentColor" stroke-width="1.3"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M14.5 13.2v5l4-2.5-4-2.5z" fill="currentColor"/>
              </svg>
            </div>

            <p class="text-xl text-gray-800 mb-6 font-light">Drag photos and videos here</p>

            <label class="bg-[#4A3AFF] hover:bg-[#3c2fe0] text-white font-semibold py-2 px-5 rounded-lg cursor-pointer transition text-sm">
              Select from computer
              <input type="file" accept="image/*" class="hidden" @change="handleImageChange">
            </label>
          </div>
        </div>

        <div v-if="step === 2" class="flex flex-col md:flex-row max-h-[70vh]">
          <div class="w-full md:w-1/2 bg-black flex items-center justify-center">
            <img :src="imagePreview" class="max-w-full max-h-[400px] object-contain" alt="preview">
          </div>

          <div class="w-full md:w-1/2 flex flex-col p-4 bg-white">
            <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-gray-100">
              <img src="https://i.pravatar.cc/150?img=12" class="w-7 h-7 rounded-full object-cover" alt="avatar">
              <span class="font-semibold text-sm text-gray-800">current_user</span>
            </div>

            <textarea
              v-model="caption"
              placeholder="Write a caption..."
              class="flex-1 w-full resize-none outline-none text-sm text-gray-800 placeholder-gray-400 min-h-[100px]"
              maxlength="2200"
            ></textarea>

            <div class="flex items-center justify-between pt-3 mt-2">
              <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
              <span class="text-xs text-gray-400">{{ caption.length }}/2,200</span>
            </div>
          </div>
        </div>

        <div v-if="step === 2" class="border-t border-gray-200 p-3 flex justify-end">
          <button
            @click="handleSubmit"
            class="bg-[#4A3AFF] hover:bg-[#3c2fe0] text-white font-semibold px-4 py-1.5 rounded text-sm transition"
          >
            Share
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>