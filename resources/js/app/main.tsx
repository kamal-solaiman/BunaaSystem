import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { App } from '@/app/App';
import '@/styles/app.css';

/**
 * Browser entry point.
 *
 * Mounts the React 19 application into the Laravel-rendered shell.
 */
const container = document.getElementById('app');

if (!container) {
    throw new Error('Application mount element #app was not found.');
}

createRoot(container).render(
    <StrictMode>
        <App />
    </StrictMode>,
);
