<!-- frontend/src/views/RegisterView.vue -->
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api'; // <-- IMPORT API CONFIG

const router = useRouter();
const email = ref('');
const fullName = ref('');
const username = ref('');
const password = ref('');
const loading = ref(false);       // <-- STATE UNTUK LOADING
const errorMessage = ref('');     // <-- STATE UNTUK PESAN ERROR

const handleRegister = async (e) => {
  e.preventDefault();
  loading.value = true;
  errorMessage.value = '';

  try {
    // Kirim request ke Laravel API
    // Catatan: Backend kita mengharapkan 'name', jadi kita map fullName.value ke name
    const response = await api.post('/register', {
      name: fullName.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value, // Wajib sama dengan password untuk validasi Laravel
    });
    
    // Simpan token & data user ke localStorage browser
    localStorage.setItem('token', response.data.token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    // Redirect ke halaman Home jika berhasil
    router.push('/'); 
  } catch (error) {
    // Tangkap error validasi dari Laravel (misal: email sudah terdaftar)
    if (error.response && error.response.data.errors) {
      const errors = error.response.data.errors;
      // Ambil pesan error pertama yang muncul (misal: "The email has already been taken.")
      errorMessage.value = Object.values(errors)[0][0];
    } else if (error.response && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan koneksi. Pastikan server Laravel berjalan di port 8000.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-[350px] flex flex-col space-y-3">
      
      <!-- Card Utama -->
      <div class="bg-white border border-gray-300 rounded-sm p-8 flex flex-col items-center">
        <!-- Logo Custom Anda -->
        <h1
          class="text-5xl font-black mb-8 leading-[1.3] pb-2 bg-gradient-to-r from-[#4FACFE] via-[#0095F6] to-[#0057D9] bg-clip-text text-transparent"
          style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;"
        >
          InstaApp
        </h1>
        <p class="text-gray-500 font-semibold text-sm text-center mb-6">Sign up to see photos and videos from your friends.</p>
        
        <button class="w-full bg-[#0095F6] text-white font-semibold py-1.5 rounded text-sm mb-4 hover:bg-[#1877F2] transition flex items-center justify-center space-x-2">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
          <span>Log in with Facebook</span>
        </button>

        <!-- Divider OR -->
        <div class="flex items-center w-full my-4">
          <div class="flex-grow border-t border-gray-300"></div>
          <span class="px-4 text-gray-500 text-xs font-semibold">OR</span>
          <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <form @submit="handleRegister" class="w-full space-y-2">
          <input 
            v-model="email"
            type="email" 
            placeholder="Mobile Number or Email" 
            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-gray-400 placeholder-gray-500"
            required
          >
          <input 
            v-model="fullName"
            type="text" 
            placeholder="Full Name" 
            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-gray-400 placeholder-gray-500"
            required
          >
          <input 
            v-model="username"
            type="text" 
            placeholder="Username" 
            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-gray-400 placeholder-gray-500"
            required
          >
          <input 
            v-model="password"
            type="password" 
            placeholder="Password" 
            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-gray-400 placeholder-gray-500"
            required
          >
          
          <p class="text-xs text-gray-500 text-center mt-4 mb-2">
            People who use our service may have uploaded your contact information to Instagram. <a href="#" class="text-[#00376b]">Learn More</a>
          </p>
          <p class="text-xs text-gray-500 text-center mb-4">
            By signing up, you agree to our <a href="#" class="text-[#00376b]">Terms</a>, <a href="#" class="text-[#00376b]">Privacy Policy</a> and <a href="#" class="text-[#00376b]">Cookies Policy</a>.
          </p>

          <!-- PESAN ERROR (Muncul jika registrasi gagal, misal email sudah dipakai) -->
          <p v-if="errorMessage" class="text-red-500 text-xs text-center mb-2 font-medium">
            {{ errorMessage }}
          </p>

          <button 
            type="submit"
            class="w-full bg-[#0095F6] text-white font-semibold py-1.5 rounded text-sm hover:bg-[#1877F2] transition disabled:opacity-50 flex justify-center items-center"
            :disabled="!email || !fullName || !username || !password || loading"
          >
            <!-- Teks berubah jadi Loading... saat proses berjalan -->
            <span v-if="loading">Loading...</span>
            <span v-else>Sign up</span>
          </button>
        </form>
      </div>

      <!-- Card Log In -->
      <div class="bg-white border border-gray-300 rounded-sm p-5 text-center text-sm">
        <p class="text-gray-800">
          Have an account? 
          <router-link to="/login" class="text-[#0095F6] font-semibold hover:text-[#00376b]">Log in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>