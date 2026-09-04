import './bootstrap';

import Alpine from 'alpinejs';
import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import ViewerInventoryDashboard from './components/ViewerInventoryDashboard';

window.Alpine = Alpine;

Alpine.start();

const viewerDashboard = document.getElementById('viewer-inventory-dashboard');

if (viewerDashboard) {
    const data = JSON.parse(document.getElementById('viewer-inventory-data').textContent);
    createRoot(viewerDashboard).render(createElement(ViewerInventoryDashboard, { data }));
}
