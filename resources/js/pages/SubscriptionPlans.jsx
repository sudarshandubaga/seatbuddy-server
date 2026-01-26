import React, { useEffect, useState } from 'react';
import api from '../lib/axios';
import { Plus, Check } from 'lucide-react';

export default function SubscriptionPlans() {
    const [plans, setPlans] = useState([]);
    const [loading, setLoading] = useState(true);
    const [libraries, setLibraries] = useState([]);
    const [showAllocateModal, setShowAllocateModal] = useState(false); // Keep this for allocation
    const [selectedPlan, setSelectedPlan] = useState(null);
    const [selectedLibraryId, setSelectedLibraryId] = useState('');
    const [allocating, setAllocating] = useState(false);

    // New states for CRUD operations
    const [showModal, setShowModal] = useState(false);
    const [editingPlan, setEditingPlan] = useState(null);
    const [formData, setFormData] = useState({
        name: '', regular_amount: '', trade_amount: '', validity: '', description: '', is_recommended: false
    });

    useEffect(() => {
        fetchPlans();
    }, []);

    const fetchPlans = async () => {
        try {
            const response = await api.get('/subscription-plans');
            setPlans(response.data);
        } catch (error) {
            console.error('Failed to fetch plans', error);
        } finally {
            setLoading(false);
        }
    };

    // New functions for CRUD operations
    const handleInputChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = {
            ...formData,
            description: formData.description.split('\n').filter(line => line.trim() !== '')
        };

        try {
            if (editingPlan) {
                await api.put(`/subscription-plans/${editingPlan.id}`, data);
            } else {
                await api.post('/subscription-plans', data);
            }
            fetchPlans();
            setShowModal(false);
            resetForm();
        } catch (error) {
            console.error('Operation failed', error.response?.data || error);
            alert('Operation failed: ' + (error.response?.data?.message || error.message));
        }
    };

    const handleEdit = (plan) => {
        setEditingPlan(plan);
        setFormData({
            name: plan.name,
            regular_amount: plan.regular_amount,
            trade_amount: plan.trade_amount,
            validity: plan.validity,
            description: Array.isArray(plan.description) ? plan.description.join('\n') : '',
            is_recommended: !!plan.is_recommended
        });
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        if (!confirm('Are you sure you want to delete this plan?')) return;
        try {
            await api.delete(`/subscription-plans/${id}`);
            fetchPlans();
        } catch (error) {
            console.error('Delete failed', error);
        }
    };

    const resetForm = () => {
        setEditingPlan(null);
        setFormData({
            name: '', regular_amount: '', trade_amount: '', validity: '', description: '', is_recommended: false
        });
    };

    const handleAllocateClick = async (plan) => {
        setSelectedPlan(plan);
        setShowAllocateModal(true);
        try {
            const response = await api.get('/libraries');
            setLibraries(response.data);
        } catch (error) {
            console.error('Failed to fetch libraries', error);
        }
    };

    const handleAllocateSubmit = async () => {
        if (!selectedLibraryId) return;
        setAllocating(true);
        try {
            await api.post('/subscription-histories', {
                library_id: selectedLibraryId,
                plan_id: selectedPlan.id,
                amount: selectedPlan.offer_price || selectedPlan.trade_amount, // Use trade_amount as amount
                is_paid: true, // Assuming paid for now
            });
            setShowAllocateModal(false);
            setSelectedLibraryId('');
            setSelectedPlan(null);
            alert('Plan allocated successfully!');
        } catch (error) {
            console.error('Allocation failed', error);
            alert('Allocation failed');
        } finally {
            setAllocating(false);
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-3xl font-bold text-gray-800">Subscription Plans</h1>
                <button
                    onClick={() => { resetForm(); setShowModal(true); }}
                    className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center"
                >
                    <Plus className="w-5 h-5 mr-2" />
                    Create Plan
                </button>
            </div>

            {loading ? (
                <div className="text-center text-gray-500">Loading plans...</div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {plans.map((plan) => (
                        <div key={plan.id} className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div className="flex justify-between items-start mb-4">
                                <div>
                                    <h3 className="text-lg font-bold text-gray-800">{plan.name}</h3>
                                    <p className="text-2xl font-bold text-blue-600 mt-2">
                                        ₹{plan.trade_amount}
                                        <span className="text-sm text-gray-500 font-normal ml-2 line-through">₹{plan.regular_amount}</span>
                                    </p>
                                    <p className="text-sm text-gray-500">{plan.validity} Months</p>
                                </div>
                                {plan.is_recommended && (
                                    <span className="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Recommended</span>
                                )}
                            </div>
                            <div className="space-y-3 mb-6">
                                {plan.description && plan.description.map((desc, idx) => (
                                    <div key={idx} className="flex items-center text-gray-600 text-sm">
                                        <Check className="w-4 h-4 text-green-500 mr-2" />
                                        {desc}
                                    </div>
                                ))}
                            </div>
                            <div className="flex space-x-3">
                                <button onClick={() => handleEdit(plan)} className="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-gray-600 text-sm font-medium hover:bg-gray-50">
                                    Edit
                                </button>
                                <button onClick={() => handleDelete(plan.id)} className="px-4 py-2 border border-red-200 rounded-lg text-red-600 text-sm font-medium hover:bg-red-50">
                                    Delete
                                </button>
                                <button
                                    onClick={() => handleAllocateClick(plan)}
                                    className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                                >
                                    Allocate
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto">
                    <div className="bg-white rounded-xl p-6 w-full max-w-2xl my-8">
                        <h3 className="text-xl font-bold text-gray-800 mb-4">{editingPlan ? 'Edit Plan' : 'Create Plan'}</h3>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Plan Name *</label>
                                <input name="name" value={formData.name} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Regular Amount *</label>
                                <input name="regular_amount" type="number" step="0.01" value={formData.regular_amount} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Trade Amount (Offer) *</label>
                                <input name="trade_amount" type="number" step="0.01" value={formData.trade_amount} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Validity (Months) *</label>
                                <input name="validity" type="number" value={formData.validity} onChange={handleInputChange} required className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            </div>
                            <div className="flex items-center mt-6">
                                <input
                                    type="checkbox"
                                    name="is_recommended"
                                    id="is_recommended"
                                    checked={formData.is_recommended}
                                    onChange={handleInputChange}
                                    className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label htmlFor="is_recommended" className="ml-2 block text-sm font-medium text-gray-700">Recommended Plan</label>
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description (One per line)</label>
                                <textarea name="description" value={formData.description} onChange={handleInputChange} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" rows="4" placeholder="- Feature 1&#10;- Feature 2" />
                            </div>
                            <div className="md:col-span-2 flex justify-end space-x-3 mt-4">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                                <button type="submit" className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Plan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showAllocateModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-xl p-6 w-full max-w-md">
                        <h3 className="text-xl font-bold text-gray-800 mb-4">Allocate {selectedPlan?.name}</h3>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700 mb-2">Select Library</label>
                            <select
                                value={selectedLibraryId}
                                onChange={(e) => setSelectedLibraryId(e.target.value)}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            >
                                <option value="">Select a library...</option>
                                {libraries.map((lib) => (
                                    <option key={lib.id} value={lib.id}>{lib.name} ({lib.code})</option>
                                ))}
                            </select>
                        </div>
                        <div className="flex justify-end space-x-3">
                            <button
                                onClick={() => setShowAllocateModal(false)}
                                className="px-4 py-2 text-gray-600 hover:text-gray-800"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={handleAllocateSubmit}
                                disabled={allocating || !selectedLibraryId}
                                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                            >
                                {allocating ? 'Allocating...' : 'Confirm Allocation'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
