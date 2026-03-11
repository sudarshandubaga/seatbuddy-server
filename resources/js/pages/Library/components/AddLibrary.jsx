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
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto p-4 py-12">
            <div className="bg-white rounded-2xl p-8 w-full max-w-6xl shadow-2xl relative">
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h3 className="text-2xl font-black text-gray-900 tracking-tight">{editingLibrary ? 'Edit Library' : 'Add New Library'}</h3>
                        <p className="text-gray-500 text-sm font-medium">Register and configure the library business profile.</p>
                    </div>
                    <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg className="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-10">
                    {/* Section 1: Business Info */}
                    <div>
                        <h4 className="flex items-center text-xs font-black text-blue-600 uppercase tracking-[0.2em] mb-6">
                            <span className="bg-blue-600 w-2 h-2 rounded-full mr-2"></span>
                            Business Information
                        </h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="md:col-span-2">
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Library Name *</label>
                                <input name="name" value={formData.name} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="e.g. Central City Library" />
                            </div>
                            <div className="md:col-span-1">
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Library Code (4 Digits) *</label>
                                <input name="code" maxLength="4" value={formData.code} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono tracking-widest" placeholder="XXXX" />
                            </div>
                            <div className="md:col-span-3">
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Address *</label>
                                <textarea name="address" value={formData.address} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" rows="2" placeholder="Full physical address" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Latitude</label>
                                <input name="latitude" type="number" step="any" value={formData.latitude} onChange={handleInputChange} className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Longitude</label>
                                <input name="longitude" type="number" step="any" value={formData.longitude} onChange={handleInputChange} className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Logo</label>
                                <input name="logo" type="file" onChange={handleInputChange} className="w-full px-4 py-2 bg-gray-50 border border-dashed border-gray-200 rounded-xl focus:bg-white outline-none text-xs" />
                            </div>
                        </div>
                    </div>

                    {/* Section 2: Owner & Subscription */}
                    <div>
                        <h4 className="flex items-center text-xs font-black text-blue-600 uppercase tracking-[0.2em] mb-6">
                            <span className="bg-blue-600 w-2 h-2 rounded-full mr-2"></span>
                            Ownership & Access
                        </h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {!editingLibrary && (
                                <>
                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Owner Name *</label>
                                        <input name="owner_name" value={formData.owner_name} onChange={handleInputChange} required={!formData.user_id} className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Password *</label>
                                        <input name="password" type="password" value={formData.password} onChange={handleInputChange} required={!formData.user_id} className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Min 6 characters" />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase mb-2">User ID Suffix (4 Digits) *</label>
                                        <input name="user_suffix" maxLength="4" value={formData.user_suffix} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono tracking-widest" placeholder="YYYY" />
                                    </div>
                                </>
                            )}
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Email Address *</label>
                                <input name="email" type="email" value={formData.email} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Phone/Mobile *</label>
                                <input name="phone" value={formData.phone} onChange={handleInputChange} required className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                            <div className="bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex flex-col justify-center">
                                <p className="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-1">Generated User ID</p>
                                <p className="text-2xl font-black text-blue-600 tracking-widest">
                                    {formData.code && formData.user_suffix ? `${formData.code}${formData.user_suffix}` : (editingLibrary ? 'LOCKED' : 'XXXXYYYY')}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Section 3: Subscription Logic */}
                    <div className="pt-2">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Subscription Plan *</label>
                                <select name="plan_id" value={formData.plan_id} onChange={handleInputChange} required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold">
                                    <option value="">Select Plan</option>
                                    {plans.map(plan => (
                                        <option key={plan.id} value={plan.id}>{plan.name} ({plan.validity} Mo)</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Validity Date (Auto) *</label>
                                <input name="valid_upto" type="date" value={formData.valid_upto} onChange={handleInputChange} required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-blue-600" />
                            </div>
                            {editingLibrary && (
                                <div>
                                    <label className="block text-xs font-bold text-gray-400 uppercase mb-2">Change Manager</label>
                                    <select name="user_id" value={formData.user_id} onChange={handleInputChange} className="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm">
                                        <option value="">Stay Same</option>
                                        {users.map(user => (
                                            <option key={user.id} value={user.id}>{user.name}</option>
                                        ))}
                                    </select>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="flex justify-end space-x-4 pt-4">
                        <button type="button" onClick={onClose} className="px-8 py-3 text-gray-400 hover:text-gray-600 font-bold transition-colors">Cancel</button>
                        <button type="submit" className="px-12 py-3 bg-gray-900 text-white rounded-xl hover:bg-black shadow-xl shadow-gray-200 transition-all font-black uppercase tracking-widest text-xs">
                            {editingLibrary ? 'Save Changes' : 'Register Library'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
