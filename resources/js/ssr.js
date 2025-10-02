import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { i18nVue, trans } from 'laravel-vue-i18n';
import { createSSRApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AppLayout from './Layouts/AppLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
  createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
      const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
      let resolvedPage = pages[`./Pages/${name}.vue`];
      resolvedPage.default.layout = resolvedPage.default.layout || AppLayout;
      return resolvedPage;
    },
    setup({ App, props, plugin }) {
      const app = createSSRApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, {
          ...page.props.ziggy,
          location: new URL(page.props.ziggy.location),
        })
        .use(i18nVue, {
          lang: 'en',
          resolve: (lang) => {
            const langs = import.meta.glob('../../lang/*.json', { eager: true });
            return langs[`../../lang/${lang}.json`].default;
          },
          fallbackLang: 'en',
        });

      app.config.globalProperties.trans = trans;

      return app;
    },
  })
);
