import React, { useEffect, useState } from 'react';
import api from '../lib/axios';
import { DollarSign, Library, Calendar } from 'lucide-react';

export default function PaymentHistory() {
    const [history, setHistory] = useState([]);
    const [loading, setLoading] = useState(true);
    const [libraries, setLibraries] = useState([]);
    const [filters, setFilters] = useState({ library_id: '' });
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });

    useEffect(() => {
        fetchHistory(1);
        fetchLibraries();
    }, []);

    const fetchHistory = async (page = 1) => {
        setLoading(true);
        try {
            const response = await api.get('/subscription-histories', { params: { page, ...filters } });
            setHistory(response.data.data || response.data); // Support both paginated and unpaginated for now
            if (response.data.current_page) {
                setPagination({
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                });
            }
        } catch (error) {
            console.error('Failed to fetch payment history', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchLibraries = async () => {
        try {
            const response = await api.get('/libraries?per_page=1000');
            setLibraries(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch libraries', error);
        }
    };

    const handleFilterChange = (e) => {
        setFilters({ ...filters, [e.target.name]: e.target.value });
    };

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        fetchHistory(1);
    };

    const handlePageChange = (page) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchHistory(page);
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Payment History</h1>
            </div>

            <form onSubmit={handleFilterSubmit} className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
                <div className="w-64">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Filter by Library</label>
                    <select name="library_id" value={filters.library_id} onChange={handleFilterChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Libraries</option>
                        {libraries.map(lib => (
                            <option key={lib.id} value={lib.id}>{lib.name} ({lib.code})</option>
                        ))}
                    </select>
                </div>
                <button type="submit" className="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                    Filter
                </button>
            </form>

            <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Transaction Date</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Library</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Plan</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Amount</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {loading ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        ) : history.length === 0 ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">No payment history found</td>
                            </tr>
                        ) : (
                            history.map((tx) => (
                                <tr key={tx.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-gray-600">
                                        <div className="flex items-center">
                                            <Calendar className="w-4 h-4 mr-2 text-gray-400" />
                                            {new Date(tx.created_at).toLocaleString()}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 font-medium text-gray-900 flex items-center">
                                        <div className="bg-blue-50 p-2 rounded-full mr-3">
                                            <Library className="w-4 h-4 text-blue-600" />
                                        </div>
                                        {tx.library?.name || '-'}
                                    </td>
                                    <td className="px-6 py-4 text-gray-600">
                                        {tx.plan?.name || '-'}
                                    </td>
                                    <td className="px-6 py-4 font-bold text-gray-800">
                                        <div className="flex items-center">
                                            ₹{tx.amount}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        {tx.is_paid ? (
                                            <span className="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Paid</span>
                                        ) : (
                                            <span className="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Pending</span>
                                        )}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {pagination.total > 0 && (
                <div className="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-8">
                    <span className="text-sm text-gray-600">Showing page {pagination.current_page} of {pagination.last_page} ({pagination.total} total)</span>
                    <div className="flex space-x-2">
                        <button disabled={pagination.current_page <= 1} onClick={() => handlePageChange(pagination.current_page - 1)} className="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Previous</button>
                        <button disabled={pagination.current_page >= pagination.last_page} onClick={() => handlePageChange(pagination.current_page + 1)} className="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Next</button>
                    </div>
                </div>
            )}
        </div>
    );
}
