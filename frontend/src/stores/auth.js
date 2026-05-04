import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    isAuthenticated: !!localStorage.getItem('user')
  }),
  actions: {
    login(userData) {
      this.user = userData;
      this.isAuthenticated = true;
      localStorage.setItem('user', JSON.stringify(userData));
    },
    logout() {
      this.user = null;
      this.isAuthenticated = false;
      localStorage.removeItem('user');
    }
  }
})