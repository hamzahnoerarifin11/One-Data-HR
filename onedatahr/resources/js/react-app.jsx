import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import KpiItemForm from './components/KpiItemForm';

const kpiFormRoot = document.getElementById('react-kpi-item-form');
if (kpiFormRoot) {
    const root = ReactDOM.createRoot(kpiFormRoot);
    // Get props from data attributes if needed
    const kpiId = kpiFormRoot.dataset.kpiId;
    const perspectives = JSON.parse(kpiFormRoot.dataset.perspectives || '[]');

    root.render(
        <React.StrictMode>
            <KpiItemForm kpiId={kpiId} perspectives={perspectives} />
        </React.StrictMode>
    );
}
