<!-- frontend/src/views/LoginView.vue -->
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const username = ref('');
const password = ref('');

const handleLogin = (e) => {
  e.preventDefault();
  // Nanti di sini kita akan panggil API Laravel
  alert(`Login dengan: ${username.value}`);
  router.push('/'); // Redirect ke home setelah login
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <!-- Container dengan Grid 3 Kolom -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-[1000px] w-full items-center">
      
      <!-- Kolom 1: Teks dengan Gradasi & Gambar (Kiri) -->
      <div class="hidden md:flex flex-col items-center justify-center px-8 text-center">
        <h2 class="text-4xl lg:text-5xl text-gray-800 font-serif leading-snug mb-8 text-left">
        <div class="italic">
            See everyday moments from your
        </div>

        <div class="bg-gradient-to-r from-pink-500 via-purple-500 to-orange-400 bg-clip-text text-transparent font-bold not-italic">
            close friends.
        </div>
        </h2>
        
        <!-- Gambar Ilustrasi -->
        <img 
          src="/login-illustration.png" 
          alt="Instagram moments" 
          class="w-full max-w-[320px] object-contain drop-shadow-2xl"
        >
      </div>

      <!-- Kolom 2: Spacer (Tengah - Kosong) -->
      <div class="hidden md:block"></div>

      <!-- Kolom 3: Form Login (Kanan) -->
      <div class="w-full max-w-[350px] mx-auto flex flex-col space-y-3 md:col-start-3">
        <!-- Card Utama -->
        <div class="bg-white border border-gray-300 rounded-sm p-8 flex flex-col items-center">
          <!-- Logo -->
            <h1
            class="text-5xl font-black mb-8 leading-[1.3] pb-2 bg-gradient-to-r from-[#4FACFE] via-[#0095F6] to-[#0057D9] bg-clip-text text-transparent"
            style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;"
            >
            InstaApp
            </h1>
          
          <form @submit="handleLogin" class="w-full space-y-2">
            <input 
              v-model="username"
              type="text" 
              placeholder="Phone number, username, or email" 
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
            <button 
              type="submit"
              class="w-full bg-[#0095F6] text-white font-semibold py-1.5 rounded text-sm mt-4 hover:bg-[#1877F2] transition disabled:opacity-50"
              :disabled="!username || !password"
            >
              Log in
            </button>
          </form>

          <!-- Divider OR -->
          <div class="flex items-center w-full my-5">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="px-4 text-gray-500 text-xs font-semibold">OR</span>
            <div class="flex-grow border-t border-gray-300"></div>
          </div>

          <!-- Login dengan Facebook (Dummy) -->
          <button class="flex items-center justify-center space-x-2 text-[#385185] font-semibold text-sm w-full mb-4">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
            <span>Log in with Facebook</span>
          </button>

          <router-link to="/forgot-password" class="text-xs text-[#00376b] hover:underline">Forgot password?</router-link>
        </div>

        <!-- Card Sign Up -->
        <div class="bg-white border border-gray-300 rounded-sm p-5 text-center text-sm">
          <p class="text-gray-800">
            Don't have an account? 
            <router-link to="/register" class="text-[#0095F6] font-semibold hover:text-[#00376b]">Sign up</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>