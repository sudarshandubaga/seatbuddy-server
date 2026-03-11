import React, { useState, useEffect } from 'react';
import api from '../lib/axios';

const SendNotifications = () => {
    const [libraries, setLibraries] = useState([]);
    const [recipientType, setRecipientType] = useState('all');
    const [selectedLibrary, setSelectedLibrary] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [loading, setLoading] = useState(false);
    const [statusMessage, setStatusMessage] = useState(null);

    useEffect(() => {
        const fetchLibraries = async () => {
            try {
                const res = await api.get('/libraries');
                if (res.data && res.data.data) {
                    setLibraries(res.data.data);
                    if (res.data.data.length > 0) {
                        setSelectedLibrary(res.data.data[0].id);
                    }
                }
            } catch (error) {
                console.error("Failed to fetch libraries", error);
            }
        };
        fetchLibraries();
    }, []);

    const handleSend = async (e) => {
        e.preventDefault();
        
        if (!title.trim() || !description.trim()) {
            setStatusMessage({ type: 'error', text: 'Title and Message are required.' });
            return;
        }

        setLoading(true);
        setStatusMessage(null);

        try {
            const res = await api.post('/admin/notifications', {
                title,
                description,
                library_ids: recipientType === 'all' ? 'all' : selectedLibrary
            });
            setStatusMessage({ type: 'success', text: res.data.message || 'Notification sent!' });
            setTitle('');
            setDescription('');
        } catch (error) {
            console.error(error);
            setStatusMessage({ type: 'error', text: error.response?.data?.message || 'Failed to send notification.' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
            <h2 className="text-xl font-bold text-gray-800 mb-6 border-b pb-4">
                Send Push Notification
            </h2>

            {statusMessage && (
                <div className={`p-4 mb-6 rounded-lg ${statusMessage.type === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'}`}>
                    {statusMessage.text}
                </div>
            )}

            <form onSubmit={handleSend} className="space-y-6">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Recipient
                    </label>
                    <div className="flex space-x-6">
                        <label className="flex items-center">
                            <input 
                                type="radio" 
                                name="recipientType" 
                                value="all"
                                checked={recipientType === 'all'}
                                onChange={(e) => setRecipientType(e.target.value)}
                                className="mr-2 text-blue-600 focus:ring-blue-500"
                            />
                            All Libraries
                        </label>
                        <label className="flex items-center">
                            <input 
                                type="radio" 
                                name="recipientType" 
                                value="individual"
                                checked={recipientType === 'individual'}
                                onChange={(e) => setRecipientType(e.target.value)}
                                className="mr-2 text-blue-600 focus:ring-blue-500"
                            />
                            Specific Library
                        </label>
                    </div>
                </div>

                {recipientType === 'individual' && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Select Library
                        </label>
                        <select
                            value={selectedLibrary}
                            onChange={(e) => setSelectedLibrary(e.target.value)}
                            className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            {libraries.map(lib => (
                                <option key={lib.id} value={lib.user_id || lib.id}>
                                    {lib.name} ({lib.code})
                                </option>
                            ))}
                        </select>
                    </div>
                )}

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Notification Title
                    </label>
                    <input
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter title"
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Message Content
                    </label>
                    <textarea
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        rows="4"
                        className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter message body"
                    ></textarea>
                </div>

                <div className="flex justify-end pt-4">
                    <button
                        type="submit"
                        disabled={loading}
                        className="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors flex items-center shadow-sm disabled:opacity-50"
                    >
                        {loading ? 'Sending...' : 'Send Notification'}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default SendNotifications;
