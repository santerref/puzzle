import 'vite/modulepreload-polyfill'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import Builder from '@/js/components/Builder.vue'
import Wysiwyg from '@/js/components/fields/Wysiwyg.vue'
import InputText from '@/js/components/fields/InputText.vue'
import Dropdown from '@/js/components/fields/Dropdown.vue'

const pinia = createPinia()
const app = createApp(Builder)

app.use(pinia)
app.component('Wysiwyg', Wysiwyg)
app.component('InputText', InputText)
app.component('Dropdown', Dropdown)
app.mount('#puzzle')
