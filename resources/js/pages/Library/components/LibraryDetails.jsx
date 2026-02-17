import React, { useRef, useMemo } from 'react';
import { X, MapPin, Phone, Mail, Calendar, Hash, User, Clock, QrCode, Printer } from 'lucide-react';

export default function LibraryDetails({ library, isOpen, onClose, users = [] }) {
    const printRef = useRef();

    const manager = useMemo(() => {
        if (!library) return null;
        return users.find(u => u.id === library.user_id);
    }, [library, users]);

    const daysLeft = useMemo(() => {
        if (!library || !library.valid_upto) return null;
        const expiryDate = new Date(library.valid_upto);
        const today = new Date();
        const diffTime = expiryDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }, [library]);

    if (!isOpen || !library) return null;

    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(library.code)}&ecc=H`;
    const mapUrl = `https://maps.google.com/maps?q=${library.latitude},${library.longitude}&z=15&output=embed`;

    const handlePrintQR = () => {
        const printContent = document.getElementById('qr-label-to-print').innerHTML;

        const printWindow = window.open('', '_blank', 'width=1000,height=1000');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print QR Label - ${library.name}</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        @page { size: A4; margin: 0; }
                        body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; font-family: sans-serif; }
                        #print-wrapper { width: 210mm; height: 297mm; overflow: hidden; position: relative; }
                        /* Ensure flex container works in print */
                        .flex-container { display: flex !important; }
                        @media print {
                            body { background: white; }
                        }
                    </style>
                </head>
                <body>
                    <div id="print-wrapper">
                        ${printContent}
                    </div>
                    <script>
                        // Force show the content (remove hidden if any)
                        const container = document.querySelector('#print-wrapper > div');
                        if (container) {
                            container.classList.remove('hidden');
                            container.style.display = 'flex';
                        }

                        window.onload = () => {
                            const images = Array.from(document.getElementsByTagName('img'));
                            let loadedCount = 0;
                            
                            function checkReady() {
                                loadedCount++;
                                if (loadedCount >= images.length) {
                                    setTimeout(() => {
                                        window.print();
                                        window.close();
                                    }, 800);
                                }
                            }

                            if (images.length === 0) {
                                window.print();
                                window.close();
                            } else {
                                images.forEach(img => {
                                    if (img.complete) {
                                        checkReady();
                                    } else {
                                        img.onload = checkReady;
                                        img.onerror = checkReady;
                                    }
                                });
                            }
                        };
                    </script>
                </body>
            </html>
        `);
        printWindow.document.close();
    };

    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 overflow-y-auto">
            <div className="bg-white rounded-2xl w-full max-w-4xl shadow-2xl relative overflow-hidden flex flex-col md:flex-row">

                {/* Hidden Print Content */}
                <div id="qr-label-to-print" className="hidden">
                    <div className="w-[210mm] h-[297mm] flex flex-col items-center justify-between py-24 px-12 bg-white text-center border-[20px] border-blue-600 box-border">
                        <div className="flex-none">
                            <h1 className="text-6xl font-black text-gray-900 uppercase tracking-tighter leading-tight mb-4">{library.name}</h1>
                            <div className="h-2 w-48 bg-blue-600 mx-auto rounded-full"></div>
                        </div>

                        <div className="flex-1 flex flex-col items-center justify-center">
                            <div className="relative p-10 bg-white rounded-[40px] shadow-xl border-2 border-gray-100">
                                <img src={qrCodeUrl} alt="QR Code" className="w-[400px] h-[400px]" />
                                {/* Logo in center */}
                                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white p-3 rounded-2xl shadow-lg border-2 border-blue-50">
                                    <img src={`${window.location.origin}/logo.jpeg`} alt="SeatBuddy" className="w-20 h-20 object-contain" />
                                </div>
                            </div>
                        </div>

                        <div className="flex-none space-y-10">
                            <div className="inline-block px-12 py-6 bg-blue-50 rounded-3xl border-2 border-blue-100">
                                <p className="text-xl font-bold text-blue-400 uppercase tracking-[0.3em] mb-2 text-center">Library Entry Code</p>
                                <p className="text-6xl font-black text-blue-600 tracking-widest text-center">{library.code}</p>
                            </div>

                            <div className="pt-10 border-t-2 border-gray-100 w-full max-w-xl mx-auto">
                                <p className="text-2xl font-bold text-gray-300 uppercase tracking-[0.2em] mb-4 text-center">Powered By</p>
                                <div className="flex items-center justify-center space-x-4">
                                    <img src={`${window.location.origin}/logo.jpeg`} alt="SeatBuddy" className="w-14 h-14 grayscale opacity-40" />
                                    <span className="text-5xl font-black text-gray-800 tracking-tighter">SeatBuddy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Left Side: Basic Info & Map */}
                <div className="w-full md:w-1/2 p-8 border-r border-gray-100">
                    <button
                        onClick={onClose}
                        className="md:hidden absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <X className="w-6 h-6" />
                    </button>

                    <div className="flex items-center space-x-4 mb-8">
                        {library.logo ? (
                            <img
                                src={`/storage/${library.logo}`}
                                alt={library.name}
                                className="w-20 h-20 rounded-2xl object-cover border-2 border-blue-50 p-1"
                            />
                        ) : (
                            <div className="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-bold text-3xl shadow-lg shadow-blue-100">
                                {library.name.charAt(0)}
                            </div>
                        )}
                        <div>
                            <h3 className="text-2xl font-extrabold text-gray-900 tracking-tight">{library.name}</h3>
                            <p className="text-blue-600 font-semibold text-sm">Library ID: {library.code}</p>
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div className="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                            <h4 className="text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Location & Address</h4>
                            <div className="flex items-start">
                                <MapPin className="w-5 h-5 text-blue-500 mr-3 mt-0.5" />
                                <p className="text-gray-700 leading-relaxed font-medium">{library.address}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="bg-gray-50 p-4 rounded-xl">
                                <p className="text-xs font-bold text-gray-400 uppercase mb-2">Contact Number</p>
                                <div className="flex items-center text-gray-900 font-semibold">
                                    <Phone className="w-4 h-4 mr-2 text-gray-400" />
                                    {library.phone || 'Not Provided'}
                                </div>
                            </div>
                            <div className="bg-gray-50 p-4 rounded-xl">
                                <p className="text-xs font-bold text-gray-400 uppercase mb-2">Email Address</p>
                                <div className="flex items-center text-gray-900 font-semibold truncate">
                                    <Mail className="w-4 h-4 mr-2 text-gray-400" />
                                    {library.email || 'Not Provided'}
                                </div>
                            </div>
                        </div>

                        <div className="rounded-xl overflow-hidden shadow-inner border border-gray-100 h-48 bg-gray-100 relative">
                            <iframe
                                width="100%"
                                height="100%"
                                frameBorder="0"
                                scrolling="no"
                                marginHeight="0"
                                marginWidth="0"
                                src={mapUrl}
                                title="Library Map"
                                className="filter grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition-all duration-500"
                            ></iframe>
                        </div>
                    </div>
                </div>

                {/* Right Side: Status, Manager & QR */}
                <div className="w-full md:w-1/2 bg-gray-50/50 p-8 flex flex-col">
                    <button
                        onClick={onClose}
                        className="hidden md:block absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <X className="w-6 h-6" />
                    </button>

                    <div className="space-y-6 flex-grow">
                        <div>
                            <h4 className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Subscription Details</h4>
                            <div className={`p-4 rounded-xl border flex items-center justify-between ${daysLeft > 30 ? 'bg-green-50 border-green-100' : 'bg-orange-50 border-orange-100'}`}>
                                <div className="flex items-center">
                                    <Clock className={`w-8 h-8 mr-4 ${daysLeft > 30 ? 'text-green-500' : 'text-orange-500'}`} />
                                    <div>
                                        <p className="text-sm font-semibold text-gray-600">Expires On</p>
                                        <p className="font-bold text-gray-900">{new Date(library.valid_upto).toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <span className={`px-3 py-1 rounded-full text-xs font-bold ${daysLeft > 30 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'}`}>
                                        {daysLeft} days left
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                            <h4 className="text-xs font-bold text-gray-400 uppercase mb-3">Managed By</h4>
                            <div className="flex items-center">
                                <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 mr-4">
                                    <User className="w-6 h-6" />
                                </div>
                                <div>
                                    <p className="font-bold text-gray-900">{manager?.name || `User ID: ${library.user_id}`}</p>
                                    <p className="text-sm text-gray-500">{manager?.email || 'Authorized Library Manager'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="flex flex-col items-center justify-center py-6 bg-white rounded-xl border border-dashed border-gray-300 relative group">
                            <h4 className="text-xs font-bold text-gray-400 uppercase mb-4">Unique QR Code</h4>
                            <div className="p-3 bg-white shadow-sm border border-gray-100 rounded-lg mb-3 relative">
                                <img src={qrCodeUrl} alt="Library QR Code" className="w-32 h-32" />
                                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white p-1 rounded-md shadow-sm border border-gray-50">
                                    <img src="/logo.jpeg" alt="S" className="w-6 h-6 object-contain" />
                                </div>
                            </div>
                            <button
                                onClick={handlePrintQR}
                                className="absolute bottom-2 right-2 p-2 bg-blue-50 text-blue-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-100"
                                title="Print Wall Label"
                            >
                                <Printer className="w-4 h-4" />
                            </button>
                            <p className="text-[10px] text-gray-400 font-medium">Scan to verify library details</p>
                        </div>
                    </div>

                    <div className="mt-8 pt-6 border-t border-gray-200 flex space-x-3">
                        <button
                            className="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 py-3 rounded-xl transition-all font-bold text-sm shadow-sm flex items-center justify-center"
                            onClick={handlePrintQR}
                        >
                            <QrCode className="w-4 h-4 mr-2" />
                            Print QR Label
                        </button>
                        <button
                            onClick={onClose}
                            className="flex-1 bg-gray-900 hover:bg-black text-white py-3 rounded-xl transition-all font-bold text-sm shadow-lg shadow-gray-200"
                        >
                            Close Entry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
