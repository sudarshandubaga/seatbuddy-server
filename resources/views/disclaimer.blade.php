@extends('layouts.landing')

@section('title', 'Disclaimer')

@section('content')
<div class="mb-12 text-center">
    <div class="inline-flex items-center justify-center p-3 mb-6 rounded-full bg-primary/10 text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
    </div>
    <h1 class="text-4xl font-extrabold tracking-tight text-secondary dark:text-white sm:text-5xl">Disclaimer</h1>
    <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">Important information about using our services.</p>
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
            General Information
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            The information provided by SeatBuddy is for general informational purposes only. All information on the app is provided in good faith, however we make no representation or warranty of any kind, express or implied, regarding the accuracy, adequacy, validity, reliability, availability, or completeness of any information on the app.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">02</span>
            No Liability
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            Under no circumstance shall we have any liability to you for any loss or damage of any kind incurred as a result of the use of the app or reliance on any information provided on the app. Your use of the app and your reliance on any information on the app is solely at your own risk.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">03</span>
            External Links
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            The app may contain links to other websites or content belonging to or originating from third parties. Such external links are not investigated, monitored, or checked for accuracy, adequacy, validity, reliability, or completeness by us.
        </p>
    </section>

    <section>
        <h2 class="text-2xl font-bold flex items-center mb-4">
            <span class="mr-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-primary">04</span>
            Contact Us
        </h2>
        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            If you have any questions about this Disclaimer, please contact us at:
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
