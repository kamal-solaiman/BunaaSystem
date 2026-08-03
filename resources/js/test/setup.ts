import '@testing-library/jest-dom/vitest';
import { cleanup } from '@testing-library/react';
import { afterEach } from 'vitest';

/**
 * Shared frontend test setup.
 *
 * Unmounting after every test keeps the DOM and any mounted providers from
 * leaking between cases (AI_DOCS/04_Project_Structure.md §9).
 */
afterEach(() => {
    cleanup();
});
