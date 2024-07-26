import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";

const theme = {
    defaultTheme: 'light',
    themes: {
        light: {
            colors: {
                primary: '#9869FE'
            }
        },
        dark: {
            colors: {
                primary: '#9869FE'
            }
        }
    }
};

const vuetify = createVuetify({
    icons: {
        defaultSet: 'mdi'
    },
    theme,
    components: {
        ...components,
        VNumberInput,
    },
    directives: {
        ...directives,
    },
});

const appName = import.meta.env.VITE_APP_NAME || 'GateKeeper';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(vuetify)
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
