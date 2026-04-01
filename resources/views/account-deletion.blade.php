@extends('layouts.landing')

@section('title', 'Account Deletion Request')

@section('content')
<div class="mb-12 text-center">
    <div class="inline-flex items-center justify-center p-3 mb-6 rounded-full bg-red-100 text-red-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
    </div>
    <h1 class="text-4xl font-extrabold tracking-tight text-secondary dark:text-white sm:text-5xl">Account Deletion</h1>
    <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">Request to delete your SeatBuddy account and associated data.</p>
</div>

<div class="prose dark:prose-invert max-w-none space-y-10">
    <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
        <h2 class="text-2xl font-bold flex items-center mb-6">
            <span class="mr-3 p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600">01</span>
            How to request account deletion
        </h2>
        <p class="text-gray-600 dark:text-gray-300 mb-4">
            You can request to delete your account and all associated data through either of the following methods:
        </p>
        <div class="grid md:grid-cols-2 gap-6 mt-8">
            <div class="p-6 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                <h3 class="font-bold text-lg mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Via the Mobile App
                </h3>
                <ol class="list-decimal ml-5 space-y-2 text-gray-600 dark:text-gray-300">
                    <li>Open the <strong>SeatBuddy</strong> app.</li>
                    <li>Go to the <strong>Profile</strong> tab.</li>
                    <li>Scroll down to the <strong>Danger Zone</strong> section.</li>
                    <li>Tap on <strong>Delete Account</strong>.</li>
                    <li>Confirm the deletion request in the popup.</li>
                </ol>
            </div>
            <div class="p-6 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                <h3 class="font-bold text-lg mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Via Email Support
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Send an email to <strong>support@seatbuddy.in</strong> from your registered email address with the subject "Account Deletion Request". Please include your User ID or Mobile Number for identification.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
        <h2 class="text-2xl font-bold flex items-center mb-6">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">02</span>
            What data will be deleted?
        </h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            When your account deletion request is processed, the following information will be permanently removed from our active servers:
        </p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-gray-600 dark:text-gray-300">Profile information (Name, Email, Phone)</span>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-gray-600 dark:text-gray-300">Login credentials and account settings</span>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-gray-600 dark:text-gray-300">Device tokens used for notifications</span>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-gray-600 dark:text-gray-300">Attendance history and slot allocations</span>
            </div>
        </div>
    </section>

    <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
        <h2 class="text-2xl font-bold flex items-center mb-6">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">03</span>
            Data Retention Policy
        </h2>
        <div class="space-y-4 text-gray-600 dark:text-gray-300 leading-relaxed">
            <p>
                <strong>Immediate Deletion:</strong> Most of your personal data will be deleted immediately or within 30 days of the request being verified.
            </p>
            <p>
                <strong>Exceptional Retention:</strong> We may retain certain information if strictly necessary to comply with legal obligations (such as financial/transaction records for tax purposes), resolve disputes, or enforce our agreements. This data will be kept for the minimum period required by law.
            </p>
            <p>
                <strong>Backups:</strong> Residual copies of deleted data may remain in our backup systems for up to 90 days before being purged.
            </p>
        </div>
    </section>

    <div class="text-center bg-primary/5 rounded-2xl p-8 border border-primary/10">
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            If you have any further questions regarding your data or the deletion process, please don't hesitate to reach out.
        </p>
        <a href="mailto:support@seatbuddy.in" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-bold rounded-2xl text-white bg-primary hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all transform hover:-translate-y-1">
            Contact Support
        </a>
    </div>
</div>
@endsection
