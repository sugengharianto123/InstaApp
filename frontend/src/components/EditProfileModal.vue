<!-- frontend/src/components/EditProfileModal.vue -->
<script setup>
import { ref, watch } from 'vue';
import api from '../api';

const props = defineProps({
  isOpen: Boolean,
  user: Object
});

const emit = defineEmits(['close', 'updated']);

const name = ref('');
const bio = ref('');
const avatarPreview = ref('');
const avatarFile = ref(null);
const loading = ref(false);

// Isi form saat modal dibuka
watch(() => props.isOpen, (newVal) => {
  if (newVal && props.user) {
    name.value = props.user.name || '';
    bio.value = props.user.bio || '';
    avatarPreview.value = props.user.avatar || 'https://i.pravatar.cc/300?img=12';
    avatarFile.value = null;
  }
});

const handleAvatarChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
};

const handleSubmit = async () => {
  loading.value = true;
  
  try {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('bio', bio.value);
    if (avatarFile.value) {
      formData.append('avatar', avatarFile.value);
    }

    const response = await api.put('/users/me', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    emit('updated', response.data.user);
    emit('close');
    alert('Profil berhasil diperbarui!');
  } catch (error) {
    console.error(error);
    alert('Gagal memperbarui profil.');
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  emit('close');
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click.self="closeModal">
    <div class="bg-white rounded-lg overflow-hidden max-w-md w-full">
      
      <!-- Header -->
      <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-lg">Edit Profile</h3>
        <button @click="closeModal" class="text-gray-600 hover:text-gray-900">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <div class="p-6 space-y-4">
        
        <!-- Avatar Upload -->
        <div class="flex items-center space-x-4">
          <img :src="avatarPreview" class="w-20 h-20 rounded-full object-cover border border-gray-200" alt="avatar">
          <div class="flex-1">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Ubah Foto Profil</span>
              <input 
                type="file" 
                accept="image/*"
                @change="handleAvatarChange"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mt-2"
              >
            </label>
          </div>
        </div>

        <!-- Nama -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
          <input 
            v-model="name"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>

        <!-- Bio -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Bio</label>
          <textarea 
            v-model="bio"
            rows="3"
            maxlength="160"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
            placeholder="Tulis sesuatu tentang diri Anda..."
          ></textarea>
          <p class="text-xs text-gray-500 text-right mt-1">{{ bio.length }}/160</p>
        </div>

      </div>

      <!-- Footer Buttons -->
      <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
        <button 
          @click="closeModal"
          class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition"
        >
          Batal
        </button>
        <button 
          @click="handleSubmit"
          :disabled="loading"
          class="px-4 py-2 text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </div>

    </div>
  </div>
</template>