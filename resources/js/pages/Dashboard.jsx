import React from 'react';

export default function Dashboard() {
    return (
        <div>
            <h1 className="text-3xl font-bold text-gray-800 mb-8">Dashboard</h1>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 className="text-gray-500 text-sm font-medium">Total Libraries</h3>
                    <p className="text-3xl font-bold text-gray-800 mt-2">12</p>
                </div>
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 className="text-gray-500 text-sm font-medium">Active Subscriptions</h3>
                    <p className="text-3xl font-bold text-gray-800 mt-2">45</p>
                </div>
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 className="text-gray-500 text-sm font-medium">Revenue (Monthly)</h3>
                    <p className="text-3xl font-bold text-gray-800 mt-2">$2,450</p>
                </div>
            </div>
        </div>
    );
}
