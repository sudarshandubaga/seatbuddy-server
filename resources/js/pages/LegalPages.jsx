import React, { useState, useEffect } from 'react';
import api from '../lib/axios';

export default function LegalPages() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [activeTab, setActiveTab] = useState('terms_conditions');
    const [message, setMessage] = useState({ type: '', text: '' });
    const [data, setData] = useState({
        terms_conditions: '',
        privacy_policy: '',
        disclaimer: '',
    });

    const tabs = [
        { key: 'terms_conditions', label: 'Terms & Conditions', icon: '📋' },
        { key: 'privacy_policy', label: 'Privacy Policy', icon: '🛡️' },
        { key: 'disclaimer', label: 'Disclaimer', icon: '⚠️' },
    ];

    useEffect(() => {
        fetchLegalPages();
    }, []);

    const fetchLegalPages = async () => {
        try {
            // Fetch all libraries, find the first one's legal pages
            const res = await api.get('/libraries');
            if (res.data && res.data.length > 0) {
                const library = res.data[0];
                setData({
                    terms_conditions: library.terms_conditions || '',
                    privacy_policy: library.privacy_policy || '',
                    disclaimer: library.disclaimer || '',
                });
            }
        } catch (err) {
            setMessage({ type: 'error', text: 'Failed to load legal pages.' });
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        setSaving(true);
        setMessage({ type: '', text: '' });
        try {
            const res = await api.get('/libraries');
            if (res.data && res.data.length > 0) {
                const library = res.data[0];
                await api.put(`/libraries/${library.id}`, {
                    terms_conditions: data.terms_conditions,
                    privacy_policy: data.privacy_policy,
                    disclaimer: data.disclaimer,
                });
                setMessage({ type: 'success', text: 'Legal pages updated successfully!' });
            }
        } catch (err) {
            setMessage({ type: 'error', text: 'Failed to save. Please try again.' });
        } finally {
            setSaving(false);
            setTimeout(() => setMessage({ type: '', text: '' }), 3000);
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="max-w-5xl mx-auto">
            {/* Page Header */}
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-800">Legal Pages</h1>
                <p className="text-gray-500 mt-1">
                    Manage your Terms & Conditions, Privacy Policy, and Disclaimer content. These will be displayed in the app.
                </p>
            </div>

            {/* Success / Error Message */}
            {message.text && (
                <div
                    className={`mb-4 px-4 py-3 rounded-lg flex items-center gap-2 text-sm font-medium ${
                        message.type === 'success'
                            ? 'bg-green-50 text-green-700 border border-green-200'
                            : 'bg-red-50 text-red-700 border border-red-200'
                    }`}
                >
                    <span>{message.type === 'success' ? '✅' : '❌'}</span>
                    {message.text}
                </div>
            )}

            {/* Tabs */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div className="flex border-b border-gray-200">
                    {tabs.map((tab) => (
                        <button
                            key={tab.key}
                            onClick={() => setActiveTab(tab.key)}
                            className={`flex-1 py-4 px-6 text-sm font-medium flex items-center justify-center gap-2 transition-all ${
                                activeTab === tab.key
                                    ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50'
                                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                            }`}
                        >
                            <span className="text-lg">{tab.icon}</span>
                            {tab.label}
                        </button>
                    ))}
                </div>

                {/* Editor Area */}
                <div className="p-6">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        {tabs.find((t) => t.key === activeTab)?.label} Content
                    </label>
                    <p className="text-xs text-gray-400 mb-3">
                        Write your content below. You can use plain text. This will be shown as-is in the mobile app.
                    </p>
                    <textarea
                        className="w-full h-96 p-4 border border-gray-300 rounded-lg text-sm text-gray-800 leading-relaxed focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all placeholder:text-gray-300"
                        value={data[activeTab]}
                        onChange={(e) =>
                            setData((prev) => ({
                                ...prev,
                                [activeTab]: e.target.value,
                            }))
                        }
                        placeholder={`Enter your ${tabs.find((t) => t.key === activeTab)?.label}...`}
                    />

                    {/* Character count */}
                    <div className="flex justify-between items-center mt-3">
                        <p className="text-xs text-gray-400">
                            {data[activeTab]?.length || 0} characters
                        </p>
                        <button
                            onClick={handleSave}
                            disabled={saving}
                            className={`px-6 py-2.5 rounded-lg text-sm font-medium text-white transition-all ${
                                saving
                                    ? 'bg-blue-400 cursor-not-allowed'
                                    : 'bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow'
                            }`}
                        >
                            {saving ? (
                                <span className="flex items-center gap-2">
                                    <svg
                                        className="animate-spin h-4 w-4"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            className="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            strokeWidth="4"
                                        />
                                        <path
                                            className="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        />
                                    </svg>
                                    Saving...
                                </span>
                            ) : (
                                'Save Changes'
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
