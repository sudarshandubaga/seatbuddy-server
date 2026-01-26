import React from 'react';
import { Outlet, Link, useLocation } from 'react-router-dom';
import { LayoutDashboard, Library, CreditCard, LogOut, User } from 'lucide-react';

export default function AdminLayout() {
    const location = useLocation();

    return (
        <div className="flex h-screen bg-gray-100">
            {/* Sidebar */}
            <div className="w-64 bg-white shadow-lg">
                <div className="p-6 border-b">
                    <h1 className="text-2xl font-bold text-blue-600">LibraryAdmin</h1>
                </div>
                <nav className="mt-6 px-4 space-y-2">
                    <Link
                        to="/admin"
                        className={`flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors ${location.pathname === '/admin' ? 'bg-blue-50 text-blue-600' : ''
                            }`}
                    >
                        <LayoutDashboard className="w-5 h-5 mr-3" />
                        Dashboard
                    </Link>
                    <Link
                        to="/admin/libraries"
                        className={`flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors ${location.pathname === '/admin/libraries' ? 'bg-blue-50 text-blue-600' : ''
                            }`}
                    >
                        <Library className="w-5 h-5 mr-3" />
                        Library Store
                    </Link>
                    <Link
                        to="/admin/subscriptions"
                        className={`flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors ${location.pathname === '/admin/subscriptions' ? 'bg-blue-50 text-blue-600' : ''
                            }`}
                    >
                        <CreditCard className="w-5 h-5 mr-3" />
                        Subscriptions
                    </Link>
                    <Link
                        to="/admin/users"
                        className={`flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors ${location.pathname === '/admin/users' ? 'bg-blue-50 text-blue-600' : ''
                            }`}
                    >
                        <User className="w-5 h-5 mr-3" />
                        Users
                    </Link>
                </nav>
                <div className="absolute bottom-0 w-64 p-4 border-t bg-white">
                    <button className="flex items-center w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors">
                        <LogOut className="w-5 h-5 mr-3" />
                        Logout
                    </button>
                </div>
            </div>

            {/* Main Content */}
            <div className="flex-1 overflow-auto">
                <header className="bg-white shadow-sm p-4 flex justify-between items-center">
                    <h2 className="text-xl font-semibold text-gray-800">Admin Panel</h2>
                    <div className="flex items-center space-x-4">
                        <span className="text-gray-600">Admin User</span>
                        <div className="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            A
                        </div>
                    </div>
                </header>
                <main className="p-6">
                    <Outlet />
                </main>
            </div>
        </div>
    );
}
