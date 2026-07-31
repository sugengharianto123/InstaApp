<!-- frontend/src/components/CreateStoryModal.vue -->
<script setup>
import { ref } from 'vue';
import api from '../api';

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'created']);

const imageFile = ref(null);
const imagePreview = ref('');
const caption = ref('');
const loading = ref(false);

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const handleSubmit = async () => {
  if (!imageFile.value) {
    alert('Pilih gambar terlebih dahulu');
    return;
  }

  loading.value = true;

  try {
    const formData = new FormData();
    formData.append('image', imageFile.value);
    formData.append('caption', caption.value);

    const response = await api.post('/stories', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    emit('created', response.data.story);
    resetForm();
    emit('close');
    alert('Story berhasil ditambahkan!');
  } catch (error) {
    console.error(error);
    alert('Gagal menambahkan story.');
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  imageFile.value = null;
  imagePreview.value = '';
  caption.value = '';
};

const closeModal = () => {
  resetForm();
  emit('close');
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click.self="closeModal">
    <!-- Tambahkan relative dan z-50 di sini -->
    <div class="bg-white rounded-lg overflow-hidden max-w-md w-full relative z-50">
      
      <!-- Header -->
      <div class="p-4 border-b border-gray-200 flex items-center justify-between relative z-50">
        <h3 class="font-semibold text-lg">Buat Story Baru</h3>
        <button @click="closeModal" class="text-gray-600 hover:text-gray-900">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <div class="p-6 space-y-4 relative z-40">
        
        <!-- Upload Gambar (PENTING: DITAMBAHKAN 'relative' DI SINI) -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center relative hover:bg-gray-50 transition cursor-pointer">
          
          <img v-if="imagePreview" :src="imagePreview" class="max-h-64 mx-auto rounded-lg pointer-events-none" alt="preview">
          
          <div v-else class="space-y-2 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm text-gray-500">Klik area ini untuk memilih gambar</p>
          </div>
          
          <!-- Input file dengan z-10 agar HANYA aktif di dalam kotak ini -->
          <input 
            type="file" 
            accept="image/*"
            @change="handleImageChange"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
          >
        </div>

        <!-- Caption -->
        <div class="relative z-30">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Caption (Opsional)</label>
          <textarea 
            v-model="caption"
            rows="2"
            maxlength="200"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
            placeholder="Tulis sesuatu tentang story Anda..."
          ></textarea>
          <p class="text-xs text-gray-500 text-right mt-1">{{ caption.length }}/200</p>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 relative z-30">
          <p class="text-xs text-blue-700">
            ℹ️ Story akan otomatis hilang setelah 24 jam
          </p>
        </div>

      </div>

      <!-- Footer Buttons (PENTING: DITAMBAHKAN 'relative z-50' AGAR TIDAK TERTUTUP INPUT FILE) -->
      <div class="p-4 border-t border-gray-200 flex justify-end space-x-3 relative z-50 bg-white">
        <button 
          @click="closeModal"
          class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition"
        >
          Batal
        </button>
        <button 
          @click="handleSubmit"
          :disabled="loading || !imageFile"
          class="px-4 py-2 text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? 'Mengunggah...' : 'Bagikan Story' }}
        </button>
      </div>

    </div>
  </div>
</template>