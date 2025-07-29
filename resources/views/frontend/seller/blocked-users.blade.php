@extends('frontend.layouts.app')

@section('title',  __('blocked_seller_list'))


@section('content')
<x-frontend.breadcrumb.breadcrumb :links="[['text' => 'Dashboard', 'url' => '/dashboard'], ['text' => __('blocked_seller_list')]]" />

    <div class="container mx-auto ">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-semibold">{{ __('blocked_seller_list') }}</h1>
        </div>
    
        <div class="bg-white shadow rounded-lg p-6">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-blue-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('user') }}
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-blue-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('email') }}
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-blue-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('action') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blockedUsers as $user)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm break-words">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <img class="w-full h-full rounded-full" src="{{ $user->image_url }}" alt="{{ $user->name }}" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-normal break-words">
                                            {{ $user->name }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm break-words break-all">
                                <p class="text-gray-900 whitespace-normal">
                                    {{ $user->email }}
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <form action="{{ route('unblock.user', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Unblock') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    
            @if ($blockedUsers->isEmpty())
                <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg mt-4">
                    {{ __('No blocked users found.') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
@endpush
