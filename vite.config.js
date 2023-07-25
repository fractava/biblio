import { resolve } from 'path'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [vue()],
    build: {
        outDir: "js",
        lib: {
            entry: resolve(__dirname, 'src/main.js'),
            name: 'biblio',
            fileName: 'biblio-main',
        },
    //rollupOptions: {
        // make sure to externalize deps that shouldn't be bundled
        // into your library
        //external: ['vue'],
        //output: {
            // Provide global variables to use in the UMD build
            // for externalized deps
            //globals: {
                //  vue: 'Vue',
            //},
        //},
    //},
  },
})