import React, { useEffect, useState } from 'react';
import api from '../../lib/axios';
import ViewLibrary from './components/ViewLibrary';
import AddLibrary from './components/AddLibrary';
import LibraryDetails from './components/LibraryDetails';
import QrModal from './components/QrModal';

export default function LibraryStore() {
    const [libraries, setLibraries] = useState([]);
    const [loading, setLoading] = useState(true);
    const [users, setUsers] = useState([]);
    const [showAddModal, setShowAddModal] = useState(false);
    const [showDetailsModal, setShowDetailsModal] = useState(false);
    const [showQrModal, setShowQrModal] = useState(false);
    const [selectedLibrary, setSelectedLibrary] = useState(null);
    const [editingLibrary, setEditingLibrary] = useState(null);
    
    const [filters, setFilters] = useState({ search: '', city: '' });
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });

    useEffect(() => {
        fetchLibraries(1);
        fetchUsers();
    }, []);

    const fetchLibraries = async (page = 1) => {
        setLoading(true);
        try {
            const response = await api.get('/libraries', { params: { page, ...filters } });
            setLibraries(response.data.data);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total
            });
        } catch (error) {
            console.error('Failed to fetch libraries', error);
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (e) => {
        setFilters({ ...filters, [e.target.name]: e.target.value });
    };

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        fetchLibraries(1);
    };

    const handlePageChange = (page) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchLibraries(page);
        }
    };

    const fetchUsers = async () => {
        try {
            const response = await api.get('/users?role=library&per_page=1000');
            setUsers(response.data.data || []);
        } catch (error) {
            console.error('Failed to fetch users', error);
        }
    };

    const handleEdit = (library) => {
        setEditingLibrary(library);
        setShowAddModal(true);
    };

    const handleAddClick = () => {
        setEditingLibrary(null);
        setShowAddModal(true);
    };

    const handleViewDetails = (library) => {
        setSelectedLibrary(library);
        setShowDetailsModal(true);
    };

    const handleShowQr = (library) => {
        setSelectedLibrary(library);
        setShowQrModal(true);
    };

    const handleDelete = async (id) => {
        if (!confirm('Are you sure you want to delete this library?')) return;
        try {
            await api.delete(`/libraries/${id}`);
            fetchLibraries();
        } catch (error) {
            console.error('Delete failed', error);
        }
    };

    return (
        <div className="p-6">
            <form onSubmit={handleFilterSubmit} className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
                <div className="flex-1 min-w-[200px]">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value={filters.search} onChange={handleFilterChange} placeholder="Name, Address, Code, Phone, Email" className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div className="w-48">
                    <label className="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value={filters.city} onChange={handleFilterChange} placeholder="Filter by city..." className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div className="w-40">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" value={filters.status || ''} onChange={handleFilterChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <button type="submit" className="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                    Filter
                </button>
            </form>

            <ViewLibrary
                libraries={libraries}
                loading={loading}
                onEdit={handleEdit}
                onDelete={handleDelete}
                onViewDetails={handleViewDetails}
                onShowQr={handleShowQr}
                onAddLibrary={handleAddClick}
            />

            {pagination.total > 0 && (
                <div className="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100 mt-6">
                    <span className="text-sm text-gray-600">Showing page {pagination.current_page} of {pagination.last_page} ({pagination.total} total)</span>
                    <div className="flex space-x-2">
                        <button disabled={pagination.current_page <= 1} onClick={() => handlePageChange(pagination.current_page - 1)} className="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Previous</button>
                        <button disabled={pagination.current_page >= pagination.last_page} onClick={() => handlePageChange(pagination.current_page + 1)} className="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Next</button>
                    </div>
                </div>
            )}

            <AddLibrary
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                editingLibrary={editingLibrary}
                users={users}
                onSuccess={fetchLibraries}
            />

            <LibraryDetails
                isOpen={showDetailsModal}
                library={selectedLibrary}
                users={users}
                onClose={() => setShowDetailsModal(false)}
            />

            <QrModal
                isOpen={showQrModal}
                library={selectedLibrary}
                onClose={() => setShowQrModal(false)}
            />
        </div>
    );
}
