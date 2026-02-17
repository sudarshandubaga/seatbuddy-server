import React, { useEffect, useState } from 'react';
import api from '../../lib/axios';
import ViewLibrary from './components/ViewLibrary';
import AddLibrary from './components/AddLibrary';
import LibraryDetails from './components/LibraryDetails';

export default function LibraryStore() {
    const [libraries, setLibraries] = useState([]);
    const [loading, setLoading] = useState(true);
    const [users, setUsers] = useState([]);
    const [showAddModal, setShowAddModal] = useState(false);
    const [showDetailsModal, setShowDetailsModal] = useState(false);
    const [selectedLibrary, setSelectedLibrary] = useState(null);
    const [editingLibrary, setEditingLibrary] = useState(null);

    useEffect(() => {
        fetchLibraries();
        fetchUsers();
    }, []);

    const fetchLibraries = async () => {
        try {
            const response = await api.get('/libraries');
            setLibraries(response.data);
        } catch (error) {
            console.error('Failed to fetch libraries', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchUsers = async () => {
        try {
            const response = await api.get('/users?role=library');
            setUsers(response.data);
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
            <ViewLibrary
                libraries={libraries}
                loading={loading}
                onEdit={handleEdit}
                onDelete={handleDelete}
                onViewDetails={handleViewDetails}
                onAddLibrary={handleAddClick}
            />

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
        </div>
    );
}
