import React, { useState, useEffect } from 'react';
import api from '../../../lib/axios';

export default function AddLibrary({ isOpen, onClose, editingLibrary, users, onSuccess }) {
    const [formData, setFormData] = useState({
        name: '', address: '', latitude: '', longitude: '', phone: '', email: '',
        valid_upto: '', code: '', user_id: '', logo: null
    });

    useEffect(() => {
        if (editingLibrary) {
            setFormData({
                name: editingLibrary.name || '',
                address: editingLibrary.address || '',
                latitude: editingLibrary.latitude || '',
                longitude: editingLibrary.longitude || '',
                phone: editingLibrary.phone || '',
                email: editingLibrary.email || '',
                valid_upto: editingLibrary.valid_upto || '',
                code: editingLibrary.code || '',
                user_id: editingLibrary.user_id || '',
                logo: null
            });
        } else {
            setFormData({
                name: '', address: '', latitude: '', longitude: '', phone: '', email: '',
                valid_upto: '', code: '', user_id: users.length > 0 ? users[0].id : '', logo: null
            });
        }
    }, [editingLibrary, users, isOpen]);

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
                data.append('_method', 'PUT');
                await api.post(`/libraries/${editingLibrary.id}`, data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            } else {
                await api.post('/libraries', data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            }
            onSuccess();
            onClose();
        } catch (error) {
            console.error('Operation failed', error.response?.data || error);
            alert('Operation failed: ' + (error.response?.data?.message || error.message));
        }
    };

    if (!isOpen) return null;

    return (
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
                        <button type="button" onClick={onClose} className="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {editingLibrary ? 'Update Library' : 'Save Library'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
