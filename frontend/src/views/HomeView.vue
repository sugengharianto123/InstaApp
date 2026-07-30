<!-- frontend/src/views/HomeView.vue -->
<script setup>
import AppLayout from '../components/AppLayout.vue';
import PostCard from '../components/PostCard.vue';
import { mockPosts } from '../mockData.js';
</script>

<template>
  <AppLayout>
    <!-- Looping data palsu untuk menampilkan feed -->
    <PostCard 
      v-for="post in mockPosts" 
      :key="post.id" 
      :post="post" 
    />
  </AppLayout>
</template>