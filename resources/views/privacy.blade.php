@extends('layouts.landing')

@section('title', 'Privacy Policy')

@section('content')
<div class="mb-12 text-center">
    <div class="inline-flex items-center justify-center p-3 mb-6 rounded-full bg-primary/10 text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
    </div>
    <h1 class="text-4xl font-extrabold tracking-tight text-secondary dark:text-white sm:text-5xl">Privacy Policy</h1>
    <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">Protecting your personal data is our top priority.</p>
    <p class="mt-2 text-sm text-gray-400">Last Updated: {{ date('F d, Y') }}</p>
</div>

<div class="prose dark:prose-invert max-w-none space-y-10">
    @if($content)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm">
            <div class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{!! nl2br(e($content)) !!}</div>
        </div>
    @else
    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">01</span>
            Introduction
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            Welcome to SeatBuddy. Your privacy is of paramount importance to us. This Privacy Policy outlines the types of personal information that is received and collected by SeatBuddy and how it is used. At SeatBuddy, we respect your privacy and are committed to protecting it through our compliance with this policy.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">02</span>
            Information We Collect
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            We collect information from and about users of our application directly from you when you provide it to us, and automatically as you navigate through the app. This includes:
        </p>
        <ul class="list-disc ml-8 mt-4 space-y-2 text-gray-600 dark:text-gray-300">
            <li><strong>Personal Identification:</strong> Name, email address, phone number, and account credentials.</li>
            <li><strong>Library Details:</strong> If you are a library admin, we collect your business name, address, and seating configuration.</li>
            <li><strong>Device Data:</strong> We may collect device tokens for push notifications to keep you updated about fee reminders and attendance.</li>
        </ul>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">03</span>
            How We Use Your Information
        </h2>
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border-l-4 border-primary">
            <p class="text-gray-600 dark:text-gray-300 italic leading-relaxed">
                "We use your information only for providing and improving the service. We do not sell your personal data to third parties."
            </p>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-300 leading-relaxed">
            Specific purposes include:
        </p>
        <ul class="list-disc ml-8 mt-2 space-y-2 text-gray-600 dark:text-gray-300">
            <li>Managing seat allocations and student attendance.</li>
            <li>Processing fee payments and sending notifications.</li>
            <li>Personalizing your experience within SeatBuddy.</li>
        </ul>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">04</span>
            Data Security
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method of electronic storage, is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information, we cannot guarantee its absolute security.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">05</span>
            Contact Us
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            If you have any questions about this Privacy Policy, please contact our support team at:
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-4">
            <a href="mailto:support@seatbuddy.in" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary hover:bg-red-700 transition-colors">
                support@seatbuddy.in
            </a>
        </div>
    </section>
    @endif
</div>
@endsection
