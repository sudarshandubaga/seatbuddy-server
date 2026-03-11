import React, { useState, useEffect } from 'react';
import api from '../../../lib/axios';

export default function AddLibrary({ isOpen, onClose, editingLibrary, users, onSuccess }) {
    const [formData, setFormData] = useState({
        name: '', address: '', latitude: '0', longitude: '0', phone: '', email: '',
        valid_upto: '', code: '', user_id: '', logo: null,
        owner_name: '', user_suffix: '', password: '', plan_id: ''
    });
    const [plans, setPlans] = useState([]);
    const [loadingPlans, setLoadingPlans] = useState(false);

    useEffect(() => {
        const fetchPlans = async () => {
            setLoadingPlans(true);
            try {
                const response = await api.get('/subscription-plans');
                setPlans(response.data);
            } catch (error) {
                console.error('Failed to fetch plans', error);
            } finally {
                setLoadingPlans(false);
            }
        };
        fetchPlans();

        if (editingLibrary) {
            setFormData({
                name: editingLibrary.name || '',
                address: editingLibrary.address || '',
                latitude: editingLibrary.latitude || '0',
                longitude: editingLibrary.longitude || '0',
                phone: editingLibrary.phone || '',
                email: editingLibrary.email || '',
                valid_upto: editingLibrary.valid_upto || '',
                code: editingLibrary.code || '',
                user_id: editingLibrary.user_id || '',
                logo: null,
                owner_name: editingLibrary.user?.name || '',
                user_suffix: '',
                password: '',
                plan_id: editingLibrary.subscription_plan_id || ''
            });
        } else {
            setFormData({
                name: '', address: '', latitude: '0', longitude: '0', phone: '', email: '',
                valid_upto: '', code: '', user_id: '', logo: null,
                owner_name: '', user_suffix: '', password: '', plan_id: ''
            });
        }
    }, [editingLibrary, users, isOpen]);

    const handleInputChange = (e) => {
        const { name, value, files } = e.target;
        if (name === 'logo') {
            setFormData({ ...formData, logo: files[0] });
        } else if (name === 'plan_id') {
            const selectedPlan = plans.find(p => p.id == value);
            if (selectedPlan) {
                const date = new Date();
                date.setMonth(date.getMonth() + parseInt(selectedPlan.validity));
                setFormData({ 
                    ...formData, 
                    plan_id: value,
                    valid_upto: date.toISOString().split('T')[0]
                });
            } else {
                setFormData({ ...formData, [name]: value });
            }
        } else {
            setFormData({ ...formData, [name]: value });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = new FormData();
        Object.keys(formData).forEach(key => {
            if (formData[key] !== null && formData[key] !== '') {
                data.append(key, formData[key]);
            }
        });

        // Add additional logic if creating new user
        if (!editingLibrary && !formData.user_id) {
            data.append('create_new_user', 'true');
        }

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
            const msg = error.response?.data?.message || error.message;
            alert('Operation failed: ' + msg);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto">
            <div className="bg-white rounded-xl p-8 w-full max-w-4xl my-8">
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h3 className="text-2xl font-bold text-gray-800">{editingLibrary ? 'Edit Library' : 'Add New Library'}</h3>
                        <p className="text-gray-500 text-sm">Fill in the details to {editingLibrary ? 'update' : 'register'} the library business.</p>
                    </div>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-8">
                    {/* Section 1: Business Info */}
                    <div>
                        <h4 className="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Business Information</h4>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Library Name *</label>
                                <input name="name" value={formData.name} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Central City Library" />
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <textarea name="address" value={formData.address} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" rows="2" placeholder="Full physical address" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input name="latitude" type="number" step="any" value={formData.latitude} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input name="longitude" type="number" step="any" value={formData.longitude} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                        </div>
                    </div>

                    {/* Section 2: Owner/Admin Settings */}
                    <div>
                        <h4 className="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Owner & Admin Details</h4>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {!editingLibrary && (
                                <>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Owner Name *</label>
                                        <input name="owner_name" value={formData.owner_name} onChange={handleInputChange} required={!formData.user_id} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                                        <input name="password" type="password" value={formData.password} onChange={handleInputChange} required={!formData.user_id} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Min 6 characters" />
                                    </div>
                                </>
                            )}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input name="email" type="email" value={formData.email} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Phone/Mobile *</label>
                                <input name="phone" value={formData.phone} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            {editingLibrary && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Reassign Manager</label>
                                    <select name="user_id" value={formData.user_id} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="">Select Manager</option>
                                        {users.map(user => (
                                            <option key={user.id} value={user.id}>{user.name} ({user.email})</option>
                                        ))}
                                    </select>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Section 3: Access & Subscription */}
                    <div>
                        <h4 className="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Access & Subscription</h4>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Library Code (4 Digits) *</label>
                                <input name="code" maxLength="4" value={formData.code} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="XXXX" />
                            </div>
                            {!editingLibrary && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">User ID Suffix (4 Digits) *</label>
                                    <input name="user_suffix" maxLength="4" value={formData.user_suffix} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="YYYY" />
                                </div>
                            )}
                             <div className="bg-blue-50 p-3 rounded-lg flex flex-col justify-center">
                                <p className="text-xs text-blue-600 font-bold uppercase mb-1">Final User ID Preview</p>
                                <p className="text-xl font-black text-blue-800 tracking-widest">
                                    {formData.code && formData.user_suffix ? `${formData.code}${formData.user_suffix}` : (editingLibrary ? 'LOCKED' : 'XXXXYYYY')}
                                </p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Subscription Plan *</label>
                                <select name="plan_id" value={formData.plan_id} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Select Plan</option>
                                    {plans.map(plan => (
                                        <option key={plan.id} value={plan.id}>{plan.name} (Valid {plan.validity} Months)</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Valid Upto *</label>
                                <input name="valid_upto" type="date" value={formData.valid_upto} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                <input name="logo" type="file" onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-dashed" />
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end space-x-3 pt-6 border-t font-bold">
                        <button type="button" onClick={onClose} className="px-6 py-2 text-gray-500 hover:text-gray-800 transition-colors">Cancel</button>
                        <button type="submit" className="px-10 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                            {editingLibrary ? 'Update Library' : 'Create & Register Library'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
