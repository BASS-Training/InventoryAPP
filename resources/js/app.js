import './bootstrap';

import Alpine from 'alpinejs';
import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import ViewerInventoryDashboard from './components/ViewerInventoryDashboard';
import OperationsDashboard from './components/OperationsDashboard';

window.Alpine = Alpine;

Alpine.start();

const operationsDashboard = document.getElementById('operations-dashboard');
if (operationsDashboard) {
    createRoot(operationsDashboard).render(createElement(OperationsDashboard, {
        data: JSON.parse(document.getElementById('operations-data').textContent),
    }));
}

const viewerDashboard = document.getElementById('viewer-inventory-dashboard');

if (viewerDashboard) {
    const data = JSON.parse(document.getElementById('viewer-inventory-data').textContent);
    createRoot(viewerDashboard).render(createElement(ViewerInventoryDashboard, { data }));
}
