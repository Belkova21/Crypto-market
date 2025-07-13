import './bootstrap';


import { createApp } from 'vue';
import { router } from './src/router';
import vuetify from "./src/vuetify.js";
import '@mdi/font/css/materialdesignicons.css'
import App from './src/App.vue';

const importIcons=import.meta.glob('./src/components/**/*.vue')

function registerComponents(app){
    for(const filePath of Object.keys(importIcons)){
        const componentName=filePath.split('/')?.pop()?.replace('.vue','')

        importIcons[filePath]().then(function(data){

            app.component(componentName,data?.default)
        }).catch((error)=>console.log(error?.message))
    }
}

const app=createApp(App)

app.use(router).use(vuetify)

registerComponents(app)
app.mount('#app')
