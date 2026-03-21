import { Edit, Trash, Eye, Plus, QrCode } from 'lucide-react';

export default function ViewLibrary({ libraries, loading, onEdit, onDelete, onViewDetails, onAddLibrary, onShowQr }) {
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
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Address</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Contact</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Code</th>
                            <th className="px-6 py-4 text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {loading ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        ) : libraries.length === 0 ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-4 text-center text-gray-500">No libraries found</td>
                            </tr>
                        ) : (
                            libraries.map((library) => (
                                <tr key={library.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900">{library.name}</td>
                                    <td className="px-6 py-4 text-gray-600">{library.address}</td>
                                    <td className="px-6 py-4 text-gray-600">
                                        <div>{library.phone}</div>
                                        <div className="text-sm text-gray-500">{library.email}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {library.code}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex space-x-3">
                                            <button onClick={() => onShowQr(library)} className="text-gray-600 hover:text-gray-800 transition-colors" title="QR Label">
                                                <QrCode className="w-4 h-4" />
                                            </button>
                                            <button onClick={() => onViewDetails(library)} className="text-gray-600 hover:text-gray-800 transition-colors" title="View Details">
                                                <Eye className="w-4 h-4" />
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
        </div>
    );
}
