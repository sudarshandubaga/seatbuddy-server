@extends('layouts.landing')

@section('title', 'Terms & Conditions')

@section('content')
<div class="mb-12 text-center">
    <div class="inline-flex items-center justify-center p-3 mb-6 rounded-full bg-primary/10 text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
    </div>
    <h1 class="text-4xl font-extrabold tracking-tight text-secondary dark:text-white sm:text-5xl">Terms & Conditions</h1>
    <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">Our commitment to transparency and service.</p>
    <p class="mt-2 text-sm text-gray-400">Last Updated: {{ date('F d, Y') }}</p>
</div>

<div class="space-y-12">
    <section>
        <h2 class="text-2xl font-bold text-secondary dark:text-white mb-6 flex items-center">
            <span class="mr-4 w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-white text-sm">1</span>
            Acceptance of Terms
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed pl-14">
            By accessing and using the SeatBuddy application and website, you agree to be bound by these Terms and Conditions. If you do not agree to all of these terms, do not use our services.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold text-secondary dark:text-white mb-6 flex items-center">
            <span class="mr-4 w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-white text-sm">2</span>
            User Account and Security
        </h2>
        <div class="pl-14 space-y-4">
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account. 
            </p>
            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-primary mb-2 italic">Prohibited Activities:</p>
                <ul class="list-disc ml-6 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li>Using the app for any illegal or unauthorized purpose.</li>
                    <li>Modifying, adapting or hacking the Service.</li>
                    <li>Falsely implying that you are associated with the Service.</li>
                </ul>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-2xl font-bold text-secondary dark:text-white mb-6 flex items-center">
            <span class="mr-4 w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-white text-sm">3</span>
            Service Modifications
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed pl-14">
            SeatBuddy reserves the right to modify or terminate the Service for any reason, without notice, at any time. We reserve the right to refuse service to anyone for any reason at any time.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold text-secondary dark:text-white mb-6 flex items-center">
            <span class="mr-4 w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-white text-sm">4</span>
            Subscription and Payments
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed pl-14">
            Library admins agree to pay all fees associated with their chosen subscription plan. SeatBuddy uses secure payment gateways (like PhonePe or Razorpay) to process transactions. All payments are non-refundable unless stated otherwise.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold text-secondary dark:text-white mb-6 flex items-center">
            <span class="mr-4 w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-white text-sm">5</span>
            Limitation of Liability
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed pl-14">
            SeatBuddy shall not be liable for any indirect, incidental, special, consequential or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data or other intangible losses resulting from the use or the inability to use the service.
        </p>
    </section>

    <div class="mt-16 p-8 rounded-3xl bg-gradient-to-br from-primary to-red-600 text-white shadow-xl shadow-red-200 dark:shadow-none">
        <h3 class="text-xl font-bold mb-3">Still have questions?</h3>
        <p class="mb-6 opacity-90 leading-relaxed">Our legal team is here to help you understand our terms better.</p>
        <a href="mailto:legal@seatbuddy.in" class="px-8 py-3 bg-white text-primary font-bold rounded-xl hover:bg-gray-100 transition-colors inline-block">
            Contact Support
        </a>
    </div>
</div>
@endsection
