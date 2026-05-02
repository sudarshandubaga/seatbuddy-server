import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import LibraryStore from './pages/Library/index';
import SubscriptionPlans from './pages/SubscriptionPlans';
import PaymentHistory from './pages/PaymentHistory';
import Users from './pages/Users';
import SendNotifications from './pages/SendNotifications';
import LegalPages from './pages/LegalPages';
import AdminLayout from './layouts/AdminLayout';

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/admin/login" element={<Login />} />
                <Route path="/admin" element={<AdminLayout />}>
                    <Route index element={<Dashboard />} />
                    <Route path="libraries" element={<LibraryStore />} />
                    <Route path="subscriptions" element={<SubscriptionPlans />} />
                    <Route path="payment-history" element={<PaymentHistory />} />
                    <Route path="users" element={<Users />} />
                    <Route path="notifications" element={<SendNotifications />} />
                    <Route path="app-settings" element={<LegalPages />} />
                </Route>
            </Routes>
        </BrowserRouter>
    );
}

ReactDOM.createRoot(document.getElementById('app')).render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
