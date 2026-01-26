import React, { useEffect, useState } from 'react';
import api from '../lib/axios';
import { Plus, Edit, Trash, User } from 'lucide-react';

export default function Users() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [formData, setFormData] = useState({
        name: '', email: '', password: '', role: 'user'
    });

    useEffect(() => {
        fetchUsers();
    }, []);

    const fetchUsers = async () => {
        try {
            const response = await api.get('/users');
            setUsers(response.data);
        } catch (error) {
            console.error('Failed to fetch users', error);
        } finally {
            setLoading(false);
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = { ...formData };
        if (!data.password) delete data.password; // Don't send empty password on edit

        try {
            if (editingUser) {
                await api.put(`/users/${editingUser.id}`, data);
            } else {
                await api.post('/users', data);
            }
            fetchUsers();
            setShowModal(false);
            resetForm();
        } catch (error) {
            console.error('Operation failed', error.response?.data || error);
            alert('Operation failed: ' + (error.response?.data?.message || error.message));
        }
    };

    const handleEdit = (user) => {
        setEditingUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            password: '', // Password not retrieved
            role: user.role
        });
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        if (!confirm('Are you sure you want to delete this user?')) return;
        try {
            await api.delete(`/users/${id}`);
            fetchUsers();
        } catch (error) {
            console.error('Delete failed', error);
        }
    };

    const resetForm = () => {
        setEditingUser(null);
        setFormData({
            name: '', email: '', password: '', role: 'user'
        });
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-3xl font-bold text-gray-800">Users</h1>
                <button
                    onClick={() => { resetForm(); setShowModal(true); }}
                    className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center"
                >
                    <Plus className="w-5 h-5 mr-2" />
                    Add User
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Name</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Email</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Role</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Created At</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {loading ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        ) : users.length === 0 ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">No users found</td>
                            </tr>
                        ) : (
                            users.map((user) => (
                                <tr key={user.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900 flex items-center">
                                        <div className="bg-gray-100 p-2 rounded-full mr-3">
                                            <User className="w-4 h-4 text-gray-500" />
                                        </div>
                                        {user.name}
                                    </td>
                                    <td className="px-6 py-4 text-gray-600">{user.email}</td>
                                    <td className="px-6 py-4">
                                        <span className={`px-3 py-1 rounded-full text-xs font-medium 
                                            ${user.role === 'admin' ? 'bg-purple-100 text-purple-700' :
                                                user.role === 'library' ? 'bg-blue-100 text-blue-700' :
                                                    'bg-gray-100 text-gray-700'}`}>
                                            {user.role}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-gray-500 text-sm">
                                        {new Date(user.created_at).toLocaleDateString()}
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex space-x-3">
                                            <button onClick={() => handleEdit(user)} className="text-blue-600 hover:text-blue-800 transition-colors">
                                                <Edit className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => handleDelete(user.id)} className="text-red-600 hover:text-red-800 transition-colors">
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
                    <div className="bg-white rounded-xl p-6 w-full max-w-md my-8">
                        <h3 className="text-xl font-bold text-gray-800 mb-4">{editingUser ? 'Edit User' : 'Add User'}</h3>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input name="name" value={formData.name} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input name="email" type="email" value={formData.email} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Password {editingUser && '(Leave blank to keep current)'} *
                                </label>
                                <input
                                    name="password"
                                    type="password"
                                    value={formData.password}
                                    onChange={handleInputChange}
                                    required={!editingUser}
                                    minLength="8"
                                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select
                                    name="role"
                                    value={formData.role}
                                    onChange={handleInputChange}
                                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                                >
                                    <option value="user">User</option>
                                    <option value="library">Library Manager</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div className="flex justify-end space-x-3 mt-6">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                                <button type="submit" className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
