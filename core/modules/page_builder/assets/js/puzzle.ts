import 'vite/modulepreload-polyfill'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import Builder from '@modules/page_builder/assets/js/components/Builder.vue'
import Wysiwyg from '@modules/page_builder/assets/js/components/fields/Wysiwyg.vue'
import InputText from '@modules/page_builder/assets/js/components/fields/InputText.vue'
import Dropdown from '@modules/page_builder/assets/js/components/fields/Dropdown.vue'

const pinia = createPinia()
const app = createApp(Builder)

app.use(pinia)
app.component('Wysiwyg', Wysiwyg)
app.component('InputText', InputText)
app.component('Dropdown', Dropdown)
app.mount('#puzzle')
