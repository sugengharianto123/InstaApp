// frontend/src/mockData.js
export const mockPosts = [
  {
    id: 1,
    user: { id: 101, username: 'shariant', avatar: 'https://i.pravatar.cc/150?img=11' },
    image: 'https://picsum.photos/seed/post1/600/600',
    caption: 'Menikmati kopi pagi sambil coding Vue.js ☕💻 #developer #vuejs',
    likes_count: 124,
    is_liked: false,
    created_at: '2 jam yang lalu',
    comments: [
      { id: 1, user: { username: 'janedoe' }, text: 'Keren banget setup-nya!' },
      { id: 2, user: { username: 'devmaster' }, text: 'Laravel + Vue memang the best 🔥' }
    ]
  },
  {
    id: 2,
    user: { id: 102, username: 'jakitravel', avatar: 'https://i.pravatar.cc/150?img=5' },
    image: 'https://picsum.photos/seed/post2/600/750',
    caption: 'Sunset di Bali tidak pernah mengecewakan 🌅✨ #bali #travel',
    likes_count: 892,
    is_liked: true, // Sudah di-like
    created_at: '5 jam yang lalu',
    comments: [
      { id: 3, user: { username: 'wanderlust' }, text: 'Mau ke sana lagi kapan nih?' }
    ]
  }
];

export const currentUser = {
  id: 99,
  username: 'current_user',
  avatar: 'https://i.pravatar.cc/150?img=12'
};  