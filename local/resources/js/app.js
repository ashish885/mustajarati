 
require('./bootstrap');

import  {createApp} from 'vue';
const app =createApp({});
import chat from './components/ExampleComponent.vue';
app.component('chat',chat);
app.mount("#map")
 