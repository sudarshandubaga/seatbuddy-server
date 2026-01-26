import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import LibraryStore from './pages/LibraryStore';
import SubscriptionPlans from './pages/SubscriptionPlans';
import Users from './pages/Users';
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
                    <Route path="users" element={<Users />} />
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
