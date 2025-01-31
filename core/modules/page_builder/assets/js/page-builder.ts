import 'vite/modulepreload-polyfill'
import {type Component, createApp, defineAsyncComponent} from 'vue'
import {createPinia} from 'pinia'
import Builder from '@modules/page_builder/assets/js/components/Builder.vue'
import Children from '@modules/page_builder/assets/js/components/Children.vue'

const fields = import.meta.glob('@modules/page_builder/assets/js/components/fields/*.vue') as Record<string, () => Promise<{
    default: Component
}>>

const pinia = createPinia()
const app = createApp(Builder)

app.use(pinia)
app.component('Children', Children)

for (const path in fields) {
    const componentName = path.split('/').pop()?.replace('.vue', '')
    if (componentName) {
        app.component(componentName, defineAsyncComponent(fields[path]))
    }
}

app.mount('#puzzle')
