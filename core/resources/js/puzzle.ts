import 'vite/modulepreload-polyfill'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import Builder from '@/components/Builder.vue'
import Wysiwyg from "@/components/fields/Wysiwyg.vue"
import Text from "@/components/fields/Text.vue"
import Dropdown from "@/components/fields/Dropdown.vue"

const pinia = createPinia()
const app = createApp(Builder)

app.use(pinia)
app.component('wysiwyg', Wysiwyg)
app.component('text', Text)
app.component('dropdown', Dropdown)
app.mount('#puzzle')
