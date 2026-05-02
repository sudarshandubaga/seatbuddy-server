import React, { useState } from 'react';
import { Edit, Trash, Eye, Plus, QrCode, KeyRound, X } from 'lucide-react';
import api from '../../../lib/axios';

export default function ViewLibrary({ libraries, loading, onEdit, onDelete, onViewDetails, onAddLibrary, onShowQr }) {
    const [passwordModal, setPasswordModal] = useState({ isOpen: false, library: null });
    const [newPassword, setNewPassword] = useState('');
    const [saving, setSaving] = useState(false);
    const [message, setMessage] = useState({ type: '', text: '' });

    const handleChangePassword = async () => {
        if (!newPassword || newPassword.length < 8) {
            setMessage({ type: 'error', text: 'Password must be at least 8 characters.' });
            return;
        }

        setSaving(true);
        setMessage({ type: '', text: '' });

        try {
            await api.put(`/users/${passwordModal.library.user_id}`, {
                password: newPassword,
            });
            setMessage({ type: 'success', text: 'Password updated successfully!' });
            setNewPassword('');
            setTimeout(() => {
                setPasswordModal({ isOpen: false, library: null });
                setMessage({ type: '', text: '' });
            }, 1500);
        } catch (error) {
            setMessage({ type: 'error', text: 'Failed to update password. Please try again.' });
        } finally {
            setSaving(false);
        }
    };

    const openPasswordModal = (library) => {
        setNewPassword('');
        setMessage({ type: '', text: '' });
        setPasswordModal({ isOpen: true, library });
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-3xl font-bold text-gray-800">Libraries</h1>
                <button
                    onClick={onAddLibrary}
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
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">User ID</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Contact</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Code</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Manager</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Expiry</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {loading ? (
                            <tr>
                                <td colSpan="7" className="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        ) : libraries.length === 0 ? (
                            <tr>
                                <td colSpan="7" className="px-6 py-4 text-center text-gray-500">No libraries found</td>
                            </tr>
                        ) : (
                            libraries.map((library) => (
                                <tr key={library.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900">{library.name}</td>
                                    <td className="px-6 py-4">
                                        <span className="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 font-mono">
                                            {library.user?.login_name || '—'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-gray-600">
                                        <div>{library.phone}</div>
                                        <div className="text-sm text-gray-500">{library.email}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {library.code}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-gray-600">
                                        {library.user?.name || 'No Manager'}
                                    </td>
                                    <td className="px-6 py-4">
                                        {(() => {
                                            const today = new Date();
                                            today.setHours(0,0,0,0);
                                            const expiryDate = new Date(library.valid_upto);
                                            const timeDiff = expiryDate.getTime() - today.getTime();
                                            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                                            const isExpired = daysDiff < 0;

                                            return (
                                                <div className="flex flex-col">
                                                    <span className={`px-2 py-1 rounded-full text-[10px] font-bold inline-block w-max ${isExpired ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`}>
                                                        {isExpired ? 'EXPIRED' : 'ACTIVE'}
                                                    </span>
                                                    <span className="text-xs text-gray-600 mt-1 font-medium">
                                                        {expiryDate.toLocaleDateString()}
                                                    </span>
                                                    <span className={`text-[10px] font-bold ${isExpired ? 'text-red-500' : 'text-blue-500'}`}>
                                                        {isExpired ? `${Math.abs(daysDiff)} days ago` : `${daysDiff} days left`}
                                                    </span>
                                                </div>
                                            );
                                        })()}
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex space-x-3">
                                            <button onClick={() => onShowQr(library)} className="text-gray-600 hover:text-gray-800 transition-colors" title="QR Label">
                                                <QrCode className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => onViewDetails(library)} className="text-gray-600 hover:text-gray-800 transition-colors" title="View Details">
                                                <Eye className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => openPasswordModal(library)} className="text-amber-600 hover:text-amber-800 transition-colors" title="Change Password">
                                                <KeyRound className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => onEdit(library)} className="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                                <Edit className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => onDelete(library.id)} className="text-red-600 hover:text-red-800 transition-colors" title="Delete">
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

            {/* Change Password Modal */}
            {passwordModal.isOpen && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
                        {/* Modal Header */}
                        <div className="flex items-center justify-between p-6 border-b border-gray-100">
                            <div>
                                <h3 className="text-lg font-bold text-gray-900">Change Password</h3>
                                <p className="text-sm text-gray-500 mt-1">
                                    {passwordModal.library?.name}
                                </p>
                            </div>
                            <button
                                onClick={() => setPasswordModal({ isOpen: false, library: null })}
                                className="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-6 space-y-4">
                            {/* Show current User ID */}
                            <div className="bg-gray-50 p-4 rounded-xl">
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">User ID (Login Name)</p>
                                <p className="text-gray-900 font-semibold font-mono">
                                    {passwordModal.library?.user?.login_name || '—'}
                                </p>
                            </div>

                            {/* Message */}
                            {message.text && (
                                <div
                                    className={`px-4 py-3 rounded-lg flex items-center gap-2 text-sm font-medium ${
                                        message.type === 'success'
                                            ? 'bg-green-50 text-green-700 border border-green-200'
                                            : 'bg-red-50 text-red-700 border border-red-200'
                                    }`}
                                >
                                    <span>{message.type === 'success' ? '✅' : '❌'}</span>
                                    {message.text}
                                </div>
                            )}

                            {/* New Password Input */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    New Password
                                </label>
                                <input
                                    type="password"
                                    value={newPassword}
                                    onChange={(e) => setNewPassword(e.target.value)}
                                    placeholder="Enter new password (min 8 characters)"
                                    className="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    onKeyDown={(e) => e.key === 'Enter' && handleChangePassword()}
                                />
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="flex space-x-3 p-6 pt-0">
                            <button
                                onClick={() => setPasswordModal({ isOpen: false, library: null })}
                                className="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={handleChangePassword}
                                disabled={saving}
                                className={`flex-1 px-4 py-3 rounded-xl text-sm font-medium text-white transition-colors ${
                                    saving
                                        ? 'bg-blue-400 cursor-not-allowed'
                                        : 'bg-blue-600 hover:bg-blue-700'
                                }`}
                            >
                                {saving ? 'Updating...' : 'Update Password'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
