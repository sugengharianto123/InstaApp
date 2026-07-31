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
const currentPost = ref(null);

// State untuk Reply
const replyingTo = ref(null); 
const replyText = ref('');

watch(() => props.isOpen, async (newVal) => {
  if (newVal && props.post) {
    currentPost.value = { ...props.post };
    await fetchComments();
  }
});

const fetchComments = async () => {
  try {
    const response = await api.get(`/posts/${props.post.id}`);
    comments.value = response.data.comments || [];
    currentPost.value = response.data;
  } catch (error) {
    console.error('Gagal memuat komentar:', error);
  }
};

const submitComment = async (parentId = null) => {
  const text = parentId ? replyText.value : newComment.value;
  if (!text.trim()) return;
  
  loadingComment.value = true;
  
  try {
    const response = await api.post(`/posts/${props.post.id}/comments`, {
      content: text,
      parent_id: parentId
    });
    
    const newCommentData = response.data.comment;
    
    if (parentId) {
      // Cari komentar induk dan tambahkan balasan ke dalamnya
      const parentComment = comments.value.find(c => c.id === parentId);
      if (parentComment) {
        if (!parentComment.replies) parentComment.replies = [];
        parentComment.replies.push(newCommentData);
        parentComment.replies_count = (parentComment.replies_count || 0) + 1;
        // Otomatis buka balasan agar user melihat komentarnya
        parentComment.show_replies = true; 
      }
      replyText.value = '';
      replyingTo.value = null;
    } else {
      comments.value.push(newCommentData);
      newComment.value = '';
    }
  } catch (error) {
    alert('Gagal menambahkan komentar.');
  } finally {
    loadingComment.value = false;
  }
};

const toggleCommentLike = async (comment) => {
  try {
    const response = await api.post(`/comments/${comment.id}/like`);
    comment.is_liked = response.data.is_liked;
    comment.likes_count = response.data.likes_count;
  } catch (error) {
    console.error('Gagal like komentar:', error);
  }
};

const deleteComment = async (commentId, isReply = false, parentId = null) => {
  if (!confirm('Hapus komentar ini?')) return;
  try {
    await api.delete(`/comments/${commentId}`);
    
    if (isReply && parentId) {
      // Hapus dari array replies
      const parentComment = comments.value.find(c => c.id === parentId);
      if (parentComment) {
        parentComment.replies = parentComment.replies.filter(c => c.id !== commentId);
        parentComment.replies_count = parentComment.replies.length;
      }
    } else {
      // Hapus komentar utama
      comments.value = comments.value.filter(c => c.id !== commentId);
    }
  } catch (error) {
    alert('Gagal menghapus komentar.');
  }
};

const toggleReplies = (comment) => {
  comment.show_replies = !comment.show_replies;
};

const closeModal = () => emit('close');
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click.self="closeModal">
    <div class="bg-white rounded-lg overflow-hidden max-w-4xl w-full max-h-[90vh] flex flex-col md:flex-row">
      
      <!-- Sisi Kiri: Gambar -->
      <div class="md:w-1/2 bg-black flex items-center justify-center">
        <img v-if="currentPost?.image" :src="currentPost.image" class="max-w-full max-h-[90vh] object-contain" alt="post">
      </div>

      <!-- Sisi Kanan: Komentar -->
      <div class="md:w-1/2 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center space-x-3">
            <img :src="currentPost?.user?.avatar || 'https://i.pravatar.cc/150?img=12'" class="w-8 h-8 rounded-full object-cover" alt="avatar">
            <span class="font-semibold text-sm">{{ currentPost?.user?.username }}</span>
          </div>
          <button @click="closeModal" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- List Komentar (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          
          <!-- Caption Utama -->
          <div v-if="currentPost?.caption" class="flex space-x-3">
            <img :src="currentPost?.user?.avatar || 'https://i.pravatar.cc/150?img=12'" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="avatar">
            <div class="text-sm">
              <span class="font-semibold mr-2">{{ currentPost?.user?.username }}</span>
              {{ currentPost?.caption }}
              <p class="text-xs text-gray-400 mt-1">{{ currentPost?.time_ago || 'Baru saja' }}</p>
            </div>
          </div>

          <!-- Loop Komentar Utama -->
          <div v-for="comment in comments" :key="comment.id" class="group">
            <div class="flex space-x-3">
              <img :src="comment.user?.avatar || 'https://i.pravatar.cc/150?img=12'" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="avatar">
              
              <div class="flex-1">
                <div class="text-sm">
                  <span class="font-semibold mr-2">{{ comment.user?.username }}</span>
                  {{ comment.content }}
                </div>
                
                <!-- Meta: Waktu, Like, Reply, Delete -->
                <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                  <span>{{ comment.time_ago || 'Baru saja' }}</span>
                  
                  <button @click="toggleCommentLike(comment)" class="hover:text-gray-800 flex items-center space-x-1">
                    <svg v-if="comment.is_liked" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    <span v-if="comment.likes_count > 0">{{ comment.likes_count }}</span>
                  </button>

                  <button @click="replyingTo = comment.id" class="hover:text-gray-800 font-semibold">Balas</button>


                <button v-if="comment.user?.id === 1" @click="deleteComment(comment.id, false, null)" class="text-gray-500 hover:text-red-500 transition-colors text-xs font-semibold">Hapus</button>
                </div>

                <!-- Tombol Show Replies (Muncul saat balasan tersembunyi) -->
                <button 
                    v-if="comment.replies_count > 0 && !comment.show_replies" 
                    @click="toggleReplies(comment)"
                    class="mt-2 text-xs text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                    Show {{ comment.replies_count }} replies
                </button>

                <!-- Input Reply (Muncul jika diklik Balas) -->
                <div v-if="replyingTo === comment.id" class="mt-3 flex items-center space-x-2">
                  <input 
                    v-model="replyText" 
                    @keyup.enter="submitComment(comment.id)"
                    type="text" 
                    :placeholder="`Balas ke ${comment.user?.username}...`" 
                    class="flex-1 text-sm border-b border-gray-300 focus:border-gray-800 outline-none py-1 bg-transparent"
                    autofocus
                  >
                  <button @click="submitComment(comment.id)" class="text-sm font-semibold text-blue-500">Kirim</button>
                </div>

                <!-- DAFTAR BALASAN (Nested / Menjorok) -->
                                <!-- DAFTAR BALASAN (Nested / Menjorok) -->
                <div v-if="comment.show_replies && comment.replies && comment.replies.length > 0" class="mt-3 space-y-3">
                  <div v-for="reply in comment.replies" :key="reply.id" class="flex space-x-3 group/reply pl-0 relative">
                    
                    <!-- Garis vertikal tipis untuk efek nested -->
                    <div class="absolute left-2 top-0 bottom-0 w-px bg-gray-200 -z-10"></div>
                    
                    <!-- PERBAIKAN: Gunakan reply.user?.avatar dan ukuran w-8 h-8 (sama persis dengan komentar utama) -->
                    <img 
                      :src="reply.user?.avatar || 'https://i.pravatar.cc/150?img=12'" 
                      class="w-8 h-8 rounded-full object-cover flex-shrink-0" 
                      alt="avatar"
                    >
                    
                    <div class="flex-1">
                      <div class="text-sm">
                        <span class="font-semibold mr-2">{{ reply.user?.username }}</span>
                        {{ reply.content }}
                      </div>
                      
                      <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                        <span>{{ reply.time_ago || 'Baru saja' }}</span>
                        
                        <button @click="toggleCommentLike(reply)" class="hover:text-gray-800 flex items-center space-x-1">
                          <svg v-if="reply.is_liked" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                          <span v-if="reply.likes_count > 0">{{ reply.likes_count }}</span>
                        </button>

                        <button v-if="reply.user?.id === 1" @click="deleteComment(reply.id, true, comment.id)" class="text-gray-500 hover:text-red-500 transition-colors text-xs font-semibold">Hapus</button>
                      </div>
                    </div>
                  </div>
                </div>
                <button 
                    v-if="comment.replies_count > 0 && comment.show_replies" 
                    @click="toggleReplies(comment)"
                    class="mt-2 text-xs text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                    Hide replies
                </button>

              </div>
            </div>
          </div>

          <div v-if="comments.length === 0 && !currentPost?.caption" class="text-center text-gray-400 text-sm py-8">
            Belum ada komentar.
          </div>
        </div>

        <!-- Input Komentar Utama (Fixed di bawah) -->
        <div class="p-4 border-t border-gray-200 flex-shrink-0">
          <div class="flex items-center space-x-3">
            <input 
              v-model="newComment" 
              @keyup.enter="submitComment()"
              type="text" 
              placeholder="Tambahkan komentar..." 
              class="flex-1 text-sm outline-none bg-transparent placeholder-gray-400"
              :disabled="loadingComment"
            >
            <button v-if="newComment.trim()" @click="submitComment()" :disabled="loadingComment" class="text-sm font-semibold text-blue-500 hover:text-blue-700 disabled:opacity-50">
              {{ loadingComment ? '...' : 'Kirim' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>