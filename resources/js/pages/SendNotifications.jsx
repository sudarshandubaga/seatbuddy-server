import React, { useState, useEffect } from 'react';
import api from '../lib/axios';

const SendNotifications = () => {
    const [libraries, setLibraries] = useState([]);
    const [students, setStudents] = useState([]);
    const [recipientRole, setRecipientRole] = useState('library'); // 'library' or 'student'
    const [recipientType, setRecipientType] = useState('all'); // 'all' or 'individual'
    const [selectedLibrary, setSelectedLibrary] = useState('');
    const [selectedStudent, setSelectedStudent] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [loading, setLoading] = useState(false);
    const [statusMessage, setStatusMessage] = useState(null);

    useEffect(() => {
        const fetchLibraries = async () => {
            try {
                const res = await api.get('/libraries');
                const Libs = Array.isArray(res.data) ? res.data : (res.data.data || []);
                setLibraries(Libs);
                if (Libs.length > 0) {
                    setSelectedLibrary(Libs[0].id);
                }
            } catch (error) {
                console.error("Failed to fetch libraries", error);
            }
        };
        fetchLibraries();
    }, []);

    useEffect(() => {
        if (recipientRole === 'student' && recipientType === 'individual' && selectedLibrary) {
            const fetchStudents = async () => {
                try {
                    const res = await api.get(`/library-app/student?library_id=${selectedLibrary}`);
                    const studentsData = Array.isArray(res.data) ? res.data : (res.data.data || []);
                    setStudents(studentsData);
                    if (studentsData.length > 0) {
                        setSelectedStudent(studentsData[0].user_id || studentsData[0].id);
                    } else {
                        setSelectedStudent('');
                    }
                } catch (error) {
                    console.error("Failed to fetch students", error);
                    setStudents([]);
                }
            };
            fetchStudents();
        }
    }, [recipientRole, recipientType, selectedLibrary]);

    const handleSend = async (e) => {
        e.preventDefault();
        
        if (!title.trim() || !description.trim()) {
            setStatusMessage({ type: 'error', text: 'Title and Message are required.' });
            return;
        }

        setLoading(true);
        setStatusMessage(null);

        try {
            const currentLib = libraries.find(l => l.id === selectedLibrary);
            
            const payload = {
                title,
                description,
                recipient_role: recipientRole,
                library_ids: recipientType === 'all' ? 'all' : selectedLibrary
            };

            // If sending to libraries, translate library ID to its user record ID
            if (recipientRole === 'library' && recipientType === 'individual' && currentLib) {
                payload.library_ids = currentLib.user_id || currentLib.id;
            }

            // If a specific student is selected, send their user_id
            if (recipientRole === 'student' && recipientType === 'individual' && selectedStudent && selectedStudent !== 'all_students_in_library') {
                payload.user_ids = [selectedStudent];
                delete payload.library_ids;
            }

            const res = await api.post('/admin/notifications', payload);
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
                <div className="grid grid-cols-2 gap-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Send To
                        </label>
                        <div className="flex space-x-6 h-10 items-center">
                            <label className="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="recipientRole" 
                                    value="library"
                                    checked={recipientRole === 'library'}
                                    onChange={(e) => {
                                        setRecipientRole(e.target.value);
                                        setRecipientType('all');
                                    }}
                                    className="mr-2 text-blue-600 focus:ring-blue-500"
                                />
                                Libraries
                            </label>
                            <label className="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="recipientRole" 
                                    value="student"
                                    checked={recipientRole === 'student'}
                                    onChange={(e) => {
                                        setRecipientRole(e.target.value);
                                        setRecipientType('all');
                                    }}
                                    className="mr-2 text-blue-600 focus:ring-blue-500"
                                />
                                Students
                            </label>
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Recipient Type
                        </label>
                        <div className="flex space-x-6 h-10 items-center">
                            <label className="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="recipientType" 
                                    value="all"
                                    checked={recipientType === 'all'}
                                    onChange={(e) => setRecipientType(e.target.value)}
                                    className="mr-2 text-blue-600 focus:ring-blue-500"
                                />
                                All
                            </label>
                            <label className="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="recipientType" 
                                    value="individual"
                                    checked={recipientType === 'individual'}
                                    onChange={(e) => setRecipientType(e.target.value)}
                                    className="mr-2 text-blue-600 focus:ring-blue-500"
                                />
                                Specific
                            </label>
                        </div>
                    </div>
                </div>

                {recipientType === 'individual' && (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Select Library
                            </label>
                            <select
                                value={selectedLibrary}
                                onChange={(e) => setSelectedLibrary(e.target.value)}
                                className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select a library</option>
                                {libraries.map(lib => (
                                    <option key={lib.id} value={lib.id}>
                                        {lib.name} ({lib.code})
                                    </option>
                                ))}
                            </select>
                        </div>
                        
                        {recipientRole === 'student' && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Select Student
                                </label>
                                <select
                                    value={selectedStudent}
                                    onChange={(e) => setSelectedStudent(e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all_students_in_library">All Students in this Library</option>
                                    {students.map(student => (
                                        <option key={student.id} value={student.user_id || student.id}>
                                            {student.name} ({student.login_name})
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}
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
