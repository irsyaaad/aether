import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp, router } from '@inertiajs/react';

createInertiaApp({
  resolve: name => {
    // Determine how to resolve pages using Webpack's require
    const pages = require.context('./Pages', true, /\.jsx$/);
    const path = `./${name}.jsx`;
    if (!pages.keys().includes(path)) {
      return pages(`./${name}/index.jsx`).default;
    }
    return pages(path).default;
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />);
  },
});

// Intercept specific sidebar links to prevent full page reload and use Inertia router instead
if (typeof document !== 'undefined') {
    document.addEventListener('click', (e) => {
        // Find if click target or its parent is an anchor with data-inertia="true"
        const link = e.target.closest('a[data-inertia="true"]');
        
        if (link && link.href) {
            // Only intercept if it's the same origin and not opening in new tab
            const url = new URL(link.href);
            if (url.origin === window.location.origin && link.target !== '_blank') {
                e.preventDefault();
                router.visit(link.href);
            }
        }
    });
}
