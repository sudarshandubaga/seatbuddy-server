import React from 'react';
import { X, Printer, Download } from 'lucide-react';

export default function QrModal({ isOpen, library, onClose }) {
    if (!isOpen || !library) return null;

    const qrLabelUrl = `/api/libraries/${library.id}/qr-code-label`;

    const handlePrint = () => {
        window.open(qrLabelUrl, '_blank');
    };

    const handleDownload = () => {
        const link = document.createElement('a');
        link.href = qrLabelUrl;
        link.download = `QR_Label_${library.code}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
            <div className="bg-white rounded-3xl w-full max-w-lg shadow-2xl relative overflow-hidden flex flex-col">
                <div className="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 className="text-xl font-bold text-gray-900">Library QR Label</h3>
                        <p className="text-sm text-gray-500">{library.name} ({library.code})</p>
                    </div>
                    <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full transition-all">
                        <X className="w-5 h-5 text-gray-400" />
                    </button>
                </div>

                <div className="p-8 flex flex-col items-center bg-gray-50/50">
                    <div className="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 max-w-sm w-full">
                        <img
                            src={qrLabelUrl}
                            alt="QR Code Label"
                            className="w-auto h-[250px] rounded-lg shadow-sm"
                        />
                    </div>
                    <p className="text-xs text-gray-400 font-medium uppercase tracking-widest text-center">
                        Scan to mark attendance with Student App
                    </p>
                </div>

                <div className="p-6 border-t border-gray-100 flex gap-4">
                    <button
                        onClick={handlePrint}
                        className="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-xl transition-all font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary-100"
                    >
                        <Printer className="w-5 h-5" />
                        Print Label
                    </button>
                    <button
                        onClick={handleDownload}
                        className="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl transition-all font-bold flex items-center justify-center gap-2"
                    >
                        <Download className="w-5 h-5" />
                        Download PNG
                    </button>
                </div>
            </div>
        </div>
    );
}
