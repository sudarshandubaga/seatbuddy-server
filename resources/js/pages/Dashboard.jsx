import React, { useEffect, useState } from 'react';
import api from '../lib/axios';
import { Library, Users, UserCheck, DollarSign } from 'lucide-react';

export default function Dashboard() {
    const [data, setData] = useState({ stats: null, recent_libraries: [] });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchDashboardData = async () => {
            try {
                const response = await api.get('/admin/dashboard');
                setData(response.data);
            } catch (error) {
                console.error("Error fetching dashboard data", error);
            } finally {
                setLoading(false);
            }
        };

        fetchDashboardData();
    }, []);

    if (loading) {
        return <div className="p-6 text-gray-500">Loading Dashboard...</div>;
    }

    const { stats, recent_libraries } = data;

    const statCards = [
        { title: 'Total Libraries', value: stats?.total_libraries || 0, icon: Library, color: 'bg-blue-500' },
        { title: 'Total Users', value: stats?.total_users || 0, icon: Users, color: 'bg-purple-500' },
        { title: 'Total Students', value: stats?.total_students || 0, icon: UserCheck, color: 'bg-green-500' },
        { title: 'Total Revenue', value: `₹${stats?.total_revenue?.toLocaleString() || 0}`, icon: DollarSign, color: 'bg-amber-500' },
    ];

    return (
        <div className="p-2">
            <h1 className="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h1>
            
            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {statCards.map((stat, index) => (
                    <div key={index} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                        <div className={`p-4 rounded-xl ${stat.color} text-white mr-4`}>
                            <stat.icon className="w-6 h-6" />
                        </div>
                        <div>
                            <h3 className="text-gray-500 text-sm font-medium mb-1">{stat.title}</h3>
                            <p className="text-2xl font-bold text-gray-800">{stat.value}</p>
                        </div>
                    </div>
                ))}
            </div>

            {/* Recent Libraries */}
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 className="text-lg font-bold text-gray-800">Recently Added Libraries</h3>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="bg-white border-b border-gray-100">
                            <tr>
                                <th className="px-6 py-4 text-sm font-medium text-gray-500">Library Name</th>
                                <th className="px-6 py-4 text-sm font-medium text-gray-500">Code</th>
                                <th className="px-6 py-4 text-sm font-medium text-gray-500">City</th>
                                <th className="px-6 py-4 text-sm font-medium text-gray-500">Manager</th>
                                <th className="px-6 py-4 text-sm font-medium text-gray-500">Added On</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 bg-white">
                            {recent_libraries.map(library => (
                                <tr key={library.id} className="hover:bg-gray-50 transition-colors">
                                    <td className="px-6 py-4 font-medium text-gray-900">{library.name}</td>
                                    <td className="px-6 py-4">
                                        <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{library.code}</span>
                                    </td>
                                    <td className="px-6 py-4 text-gray-600">{library.city || '-'}</td>
                                    <td className="px-6 py-4 text-gray-600">{library.user?.name || '-'}</td>
                                    <td className="px-6 py-4 text-gray-500 text-sm">{new Date(library.created_at).toLocaleDateString()}</td>
                                </tr>
                            ))}
                            {recent_libraries.length === 0 && (
                                <tr>
                                    <td colSpan="5" className="px-6 py-8 text-center text-gray-500">No libraries added yet.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

