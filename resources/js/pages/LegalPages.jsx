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
        support_phone: '',
        support_email: '',
        support_whatsapp: '',
        faqs: [],
    });

    const [faqString, setFaqString] = useState('');

    const tabs = [
        { key: 'terms_conditions', label: 'Terms & Conditions', icon: '📋' },
        { key: 'privacy_policy', label: 'Privacy Policy', icon: '🛡️' },
        { key: 'disclaimer', label: 'Disclaimer', icon: '⚠️' },
        { key: 'support', label: 'Support Info', icon: '📞' },
        { key: 'faqs', label: 'FAQs', icon: '❓' },
    ];

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            setLoading(true);
            const [legalRes, supportRes] = await Promise.all([
                api.get('/library-app/legal-pages'),
                api.get('/library-app/support')
            ]);

            const legalData = legalRes.data.data || {};
            const supportData = supportRes.data.data || {};

            setData({
                terms_conditions: legalData.terms_conditions || '',
                privacy_policy: legalData.privacy_policy || '',
                disclaimer: legalData.disclaimer || '',
                support_phone: supportData.support_phone || '',
                support_email: supportData.support_email || '',
                support_whatsapp: supportData.support_whatsapp || '',
                faqs: supportData.faqs || [],
            });

            setFaqString(JSON.stringify(supportData.faqs || [], null, 2));

        } catch (err) {
            console.error(err);
            setMessage({ type: 'error', text: 'Failed to load settings.' });
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        setSaving(true);
        setMessage({ type: '', text: '' });
        try {
            let finalFaqs = data.faqs;
            if (activeTab === 'faqs') {
                try {
                    finalFaqs = JSON.parse(faqString);
                } catch (e) {
                    setMessage({ type: 'error', text: 'Invalid JSON format in FAQs.' });
                    setSaving(false);
                    return;
                }
            }

            await api.post('/library-app/legal-pages', {
                ...data,
                faqs: activeTab === 'faqs' ? faqString : JSON.stringify(data.faqs),
            });

            setMessage({ type: 'success', text: 'Settings updated successfully!' });
            if (activeTab === 'faqs') {
                setData(prev => ({ ...prev, faqs: JSON.parse(faqString) }));
            }
        } catch (err) {
            setMessage({ type: 'error', text: 'Failed to save. Please try again.' });
        } finally {
            setSaving(false);
            setTimeout(() => setMessage({ type: '', text: '' }), 5000);
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
                <h1 className="text-2xl font-bold text-gray-800">Global App Settings</h1>
                <p className="text-gray-500 mt-1">
                    Manage Terms, Privacy, and Support details for the entire application.
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
                <div className="flex border-b border-gray-200 overflow-x-auto">
                    {tabs.map((tab) => (
                        <button
                            key={tab.key}
                            onClick={() => setActiveTab(tab.key)}
                            className={`flex-1 min-w-[120px] py-4 px-4 text-sm font-medium flex items-center justify-center gap-2 transition-all ${
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
                    {['terms_conditions', 'privacy_policy', 'disclaimer'].includes(activeTab) && (
                        <>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                {tabs.find((t) => t.key === activeTab)?.label} Content
                            </label>
                            <textarea
                                className="w-full h-96 p-4 border border-gray-300 rounded-lg text-sm text-gray-800 leading-relaxed focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"
                                value={data[activeTab]}
                                onChange={(e) =>
                                    setData((prev) => ({
                                        ...prev,
                                        [activeTab]: e.target.value,
                                    }))
                                }
                                placeholder={`Enter your ${tabs.find((t) => t.key === activeTab)?.label}...`}
                            />
                        </>
                    )}

                    {activeTab === 'support' && (
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Support Phone</label>
                                <input
                                    type="text"
                                    className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                    value={data.support_phone}
                                    onChange={(e) => setData(prev => ({ ...prev, support_phone: e.target.value }))}
                                    placeholder="+91 98765 43210"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                                <input
                                    type="email"
                                    className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                    value={data.support_email}
                                    onChange={(e) => setData(prev => ({ ...prev, support_email: e.target.value }))}
                                    placeholder="support@example.com"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Support WhatsApp (Phone number only)</label>
                                <input
                                    type="text"
                                    className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                    value={data.support_whatsapp}
                                    onChange={(e) => setData(prev => ({ ...prev, support_whatsapp: e.target.value }))}
                                    placeholder="919876543210"
                                />
                            </div>
                        </div>
                    )}

                    {activeTab === 'faqs' && (
                        <>
                            <label className="block text-sm font-medium text-gray-700 mb-2">FAQs (JSON format)</label>
                            <p className="text-xs text-gray-400 mb-2">Format: {'[{"question": "...", "answer": "..."}]'}</p>
                            <textarea
                                className="w-full h-96 p-4 border border-gray-300 rounded-lg font-mono text-sm text-gray-800 leading-relaxed focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"
                                value={faqString}
                                onChange={(e) => setFaqString(e.target.value)}
                                placeholder='[{"question": "How to...", "answer": "..."}]'
                            />
                        </>
                    )}

                    {/* Footer buttons */}
                    <div className="flex justify-between items-center mt-6">
                        <p className="text-xs text-gray-400">
                             Manage your global app configuration here.
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
                            {saving ? 'Saving...' : 'Save All Changes'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
