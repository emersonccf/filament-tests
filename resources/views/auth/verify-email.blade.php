@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-[#006eb6] text-white py-4 px-6">
                <h2 class="text-2xl font-bold">{{ __('Verify Your Email Address') }}</h2>
            </div>

            <div class="p-6">
                @if (session('resent'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ __('A fresh verification link has been sent to your email address.') }}</p>
                    </div>
                @endif

                <p class="mb-4">{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                <p class="mb-4">{{ __('If you did not receive the email') }},</p>

                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-[#006eb6] hover:text-[#002e98] transition duration-300">
                        {{ __('click here to request another') }}
                    </button>.
                </form>
            </div>
        </div>
    </div>
@endsection
