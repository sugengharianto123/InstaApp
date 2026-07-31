<!-- frontend/src/components/CommentModal.vue -->
<script setup>
import { ref, watch } from 'vue';
import api from '../api';

const props = defineProps({
  isOpen: Boolean,
  post: Object
});

const emit = defineEmits(['close']);

const comments = ref([]);
const newComment = ref('');
const loadingComment = ref(false);

// Load comments saat modal dibuka
watch(() => props.isOpen, async (newVal) => {
  if (newVal && props.post) {
    await fetchComments();
  }
});

const fetchComments = async () => {
  try {
    // Ambil data post terbaru dengan comments
    const response = await api.get(`/posts/${props.post.id}`);
    comments.value = response.data.comments || [];
  } catch (error) {
    console.error('Gagal memuat komentar:', error);
  }
};

const submitComment = async () => {
  if (!newComment.value.trim()) return;
  
  loadingComment.value = true;
  
  try {
    const response = await api.post(`/posts/${props.post.id}/comments`, {
      content: newComment.value
    });
    
    comments.value.push(response.data.comment);
    newComment.value = '';
  } catch (error) {
    console.error('Gagal menambahkan komentar:', error);
    alert('Gagal menambahkan komentar.');
  } finally {
    loadingComment.value = false;
  }
};

const deleteComment = async (commentId) => {
  if (!confirm('Yakin ingin menghapus komentar ini?')) return;
  
  try {
    await api.delete(`/comments/${commentId}`);
    comments.value = comments.value.filter(c => c.id !== commentId);
  } catch (error) {
    console.error('Gagal menghapus komentar:', error);
  }
};

const closeModal = () => {
  emit('close');
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click.self="closeModal">
    <div class="bg-white rounded-lg overflow-hidden max-w-4xl w-full max-h-[90vh] flex flex-col md:flex-row">
      
      <!-- Sisi Kiri: Gambar Postingan -->
      <div class="md:w-1/2 bg-black flex items-center justify-center">
        <img 
          :src="post?.image" 
          class="max-w-full max-h-[90vh] object-contain" 
          alt="post image"
        >
      </div>

      <!-- Sisi Kanan: Caption & Komentar -->
      <div class="md:w-1/2 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <img 
              :src="post?.user?.avatar || 'https://i.pravatar.cc/150?img=12'" 
              class="w-8 h-8 rounded-full object-cover" 
              alt="avatar"
            >
            <span class="font-semibold text-sm">{{ post?.user?.name }}</span>
          </div>
          <button @click="closeModal" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Caption & Comments List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
          
          <!-- Caption -->
          <div class="flex space-x-3 pb-3 border-b border-gray-100">
            <img 
              :src="post?.user?.avatar || 'https://i.pravatar.cc/150?img=12'" 
              class="w-8 h-8 rounded-full object-cover flex-shrink-0" 
              alt="avatar"
            >
            <div class="text-sm">
              <span class="font-semibold mr-2">{{ post?.user?.name }}</span>
              {{ post?.caption }}
            </div>
          </div>

          <!-- Comments -->
          <div v-for="comment in comments" :key="comment.id" class="flex space-x-3 group">
            <img 
              :src="comment.user?.avatar || 'https://i.pravatar.cc/150?img=12'" 
              class="w-8 h-8 rounded-full object-cover flex-shrink-0" 
              alt="avatar"
            >
            <div class="flex-1 text-sm">
              <span class="font-semibold mr-2">{{ comment.user?.name }}</span>
              {{ comment.content }}
            </div>
            <button 
              v-if="comment.user?.id === 1" 
              @click="deleteComment(comment.id)"
              class="text-xs text-red-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
            >
              Hapus
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="comments.length === 0" class="text-center text-gray-400 text-sm py-8">
            Belum ada komentar. Jadilah yang pertama!
          </div>
        </div>

        <!-- Input Komentar (Fixed di bawah) -->
        <div class="p-4 border-t border-gray-200">
          <div class="flex items-center space-x-3">
            <input 
              v-model="newComment" 
              @keyup.enter="submitComment"
              type="text" 
              placeholder="Tambahkan komentar..." 
              class="flex-1 text-sm outline-none bg-transparent placeholder-gray-400"
              :disabled="loadingComment"
            >
            <button 
              v-if="newComment.trim()" 
              @click="submitComment"
              :disabled="loadingComment"
              class="text-sm font-semibold text-blue-500 hover:text-blue-700 disabled:opacity-50"
            >
              {{ loadingComment ? 'Mengirim...' : 'Kirim' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>