import React, { useEffect, useState } from 'react';
import api from '../lib/axios';
import { Plus, Edit, Trash } from 'lucide-react';

export default function LibraryStore() {
    const [libraries, setLibraries] = useState([]);
    const [loading, setLoading] = useState(true);
    const [users, setUsers] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [editingLibrary, setEditingLibrary] = useState(null);
    const [formData, setFormData] = useState({
        name: '', address: '', latitude: '', longitude: '', phone: '', email: '',
        valid_upto: '', code: '', user_id: '', logo: null
    });

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

    const handleInputChange = (e) => {
        const { name, value, files } = e.target;
        if (name === 'logo') {
            setFormData({ ...formData, logo: files[0] });
        } else {
            setFormData({ ...formData, [name]: value });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = new FormData();
        Object.keys(formData).forEach(key => {
            if (formData[key] !== null) data.append(key, formData[key]);
        });

        try {
            if (editingLibrary) {
                // For PUT with files, Laravel sometimes needs _method: PUT and POST request, 
                // but standard PUT might work if no file or processed correctly. 
                // Safest to use POST with _method: PUT for file support compatibility if needed, using api.post
                data.append('_method', 'PUT');
                await api.post(`/libraries/${editingLibrary.id}`, data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            } else {
                await api.post('/libraries', data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            }
            fetchLibraries();
            setShowModal(false);
            resetForm();
        } catch (error) {
            console.error('Operation failed', error.response?.data || error);
            alert('Operation failed: ' + (error.response?.data?.message || error.message));
        }
    };

    const handleEdit = (library) => {
        setEditingLibrary(library);
        setFormData({
            name: library.name,
            address: library.address,
            latitude: library.latitude,
            longitude: library.longitude,
            phone: library.phone || '',
            email: library.email || '',
            valid_upto: library.valid_upto,
            code: library.code,
            user_id: library.user_id,
            logo: null // Don't preload file
        });
        setShowModal(true);
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

    const resetForm = () => {
        setEditingLibrary(null);
        setFormData({
            name: '', address: '', latitude: '', longitude: '', phone: '', email: '',
            valid_upto: '', code: '', user_id: users.length > 0 ? users[0].id : '', logo: null
        });
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-3xl font-bold text-gray-800">Library Store</h1>
                <button
                    onClick={() => { resetForm(); setShowModal(true); }}
                    className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center"
                >
                    <Plus className="w-5 h-5 mr-2" />
                    Add Library
                </button>
            </div>
            <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Name</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Address</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Contact</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Code</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {loading ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        ) : libraries.length === 0 ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">No libraries found</td>
                            </tr>
                        ) : (
                            libraries.map((library) => (
                                <tr key={library.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900">{library.name}</td>
                                    <td className="px-6 py-4 text-gray-600">{library.address}</td>
                                    <td className="px-6 py-4 text-gray-600">
                                        <div>{library.phone}</div>
                                        <div className="text-sm text-gray-500">{library.email}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {library.code}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex space-x-3">
                                            <button onClick={() => handleEdit(library)} className="text-blue-600 hover:text-blue-800 transition-colors">
                                                <Edit className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => handleDelete(library.id)} className="text-red-600 hover:text-red-800 transition-colors">
                                                <Trash className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto">
                    <div className="bg-white rounded-xl p-6 w-full max-w-2xl my-8">
                        <h3 className="text-xl font-bold text-gray-800 mb-4">{editingLibrary ? 'Edit Library' : 'Add Library'}</h3>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Library Name *</label>
                                <input name="name" value={formData.name} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <textarea name="address" value={formData.address} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" rows="2" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Latitude *</label>
                                <input name="latitude" type="number" step="any" value={formData.latitude} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Longitude *</label>
                                <input name="longitude" type="number" step="any" value={formData.longitude} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input name="phone" value={formData.phone} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input name="email" type="email" value={formData.email} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Library Code *</label>
                                <input name="code" value={formData.code} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Valid Upto *</label>
                                <input name="valid_upto" type="date" value={formData.valid_upto} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Manager *</label>
                                <select
                                    name="user_id"
                                    value={formData.user_id}
                                    onChange={handleInputChange}
                                    required
                                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                                >
                                    <option value="">Select a Manager</option>
                                    {users.map(user => (
                                        <option key={user.id} value={user.id}>
                                            {user.name} ({user.email})
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                <input name="logo" type="file" onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div className="md:col-span-2 flex justify-end space-x-3 mt-4">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                                <button type="submit" className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Library</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
