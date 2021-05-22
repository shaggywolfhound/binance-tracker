<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Currencies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">#</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Asset</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Base Currency</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">Is Tracked</div></th>
                                <th class="z-20 sticky top-0 text-sm font-semibold text-gray-600 bg-white p-0"><div class="pb-2 pr-2 border-b border-gray-200">First tracked</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($user->currencies) > 0)
                            @foreach ($user->currencies as $key => $currency)
                            <tr data-id="{{$currency->id}}">
                                <th class="py-2" scope="row">{{$key+1}}</th>
                                <td class="py-2">{{$currency->asset}}</td>
                                <td class="py-2">
                                    <div class="inset-y-0 right-0 flex items-center">
                                        <label for="currency" class="sr-only">Currency</label>
                                        <select id="currency" name="currency" class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
                                            <option selected="selected" disabled="disabled">Select</option>
                                            @foreach ($quoteCurrencies as $quoteCurrency)
                                            <option data-id="{{$quoteCurrency->id}}">{{$quoteCurrency->asset}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <label class="flex items-center space-x-3 tooltip">
                                        <input type="checkbox" name="checked-demo" disabled="disabled" value="1" class="cursor-not-allowed form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none" data-com.bitwarden.browser.user-edited="yes">
                                    </label>
                                    <x-tooltip>
                                        {{ __('Select Base currency first.') }}
                                    </x-tooltip>
                                </td>
                                <td class="py-2">{{$user->userscurrencies[$key]->updated_at->format('d-M-Y')}}</td>
                            </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td colspan="5">
                                        <h3 class="font-semibold text-xl text-gray-800 leading-tight">Sorry you have no currencies at the moment.</h3>
                                        <ul>
                                            <li>Have you set up your API and secret under settings yet?</li>
                                            <li>Have you bought any currencies on Binance yet?</li>
                                            <li>Have you manually added any currencies to track yet?</li>
                                            <li>Have you used the refresh my currencies button yet?</li>
                                        </ul>
                                        <p>If all of the above is true and you still show nothing above please let us know.</p>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Save Settings') }}
                </x-button>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-button-lite src="/currencies/user" >
                    {{ __('Refresh My Currencies') }}
                </x-button-lite>
            </div>
        </div>
    </div>
    <div id="test-element" v-bind:title="message"></div>
</x-app-layout>


<script>
    console.log('script running');
    import('../js/currencies.js');

</script>
