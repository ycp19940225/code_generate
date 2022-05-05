import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
      vue(),
  ],
  // 反向代理配置 - 可解决跨域问题
  server:{
    proxy: {
      '/api': {
        target: "http://127.0.0.1:8888/",
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  }
})
