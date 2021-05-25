<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticker Store') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container">
                        <table class="w-full text-left border-collapse currencies">
                            <thead>
                            <tr>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">#</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Asset</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Price Change</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Last Price</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Date Stored</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            {{$user}}
                            @if(count($user->currencytickers) > 0)
                                <tr>
                                    <th class="py-2" scope="row"></th>
                                    <td class="py-2 asset"></td>
                                    <td class="py-2 quote">stuff</td>
                                    <td class="py-2 tracked">data</td>
                                    <td class="py-2 first_tracked"></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="5">
                                        <h3 class="font-semibold text-xl text-gray-800 leading-tight">Sorry no data stored yet</h3>
                                        <p>Have you setup your currencies?</p>
                                        <p>Have you stored any data yet?</p>
                                    </td>
                                </tr>
                            @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
