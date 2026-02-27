@extends('layouts.salon')

@section('content')
@php
    $settingsTab = request()->query('tab', 'general');
    if (! in_array($settingsTab, ['general', 'tax', 'discounts', 'gift-cards'], true)) {
        $settingsTab = 'general';
    }
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0" data-settings-url="{{ route('api.salon.settings.index') }}" data-settings-update-url="{{ route('api.salon.settings.update') }}" data-coupons-url="{{ route('api.salon.coupons.index') }}" data-gift-cards-url="{{ route('api.salon.gift-cards.index') }}">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Settings</h1>
            <p class="text-gray-600 text-sm sm:text-base mt-1">Configure your nail salon POS system</p>
        </div>
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-gray-200">
                <button type="button" onclick="salonSettingsShowTab('general', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'general' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">General</button>
                <button type="button" onclick="salonSettingsShowTab('tax', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'tax' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Tax & Currency</button>
                <button type="button" onclick="salonSettingsShowTab('discounts', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'discounts' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Discounts & Coupons</button>
                <button type="button" onclick="salonSettingsShowTab('gift-cards', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'gift-cards' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Gift Cards</button>
            </div>
        </div>
        <div id="tab-general" class="settings-tab {{ $settingsTab === 'general' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h2>
                <form class="space-y-4 settings-form" data-settings-keys="business_name,business_phone,business_email,business_address">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                            <input type="text" name="business_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Nail Salon Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="business_phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="(555) 123-4567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="business_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="info@salon.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="business_address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="123 Main Street">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Changes</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">GoHighLevel Webhooks</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Online Check-in</label>
                    <div class="relative">
                        <div class="flex items-center gap-2">
                            <input type="url" id="onlineCheckinUrl" value="{{ url('/check-in') }}" readonly class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-default focus:outline-none">
                            <button type="button" id="copyCheckinBtn" class="px-3 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition active:scale-95" title="Copy URL">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </div>
                        <span id="copyCheckinToast" class="absolute -top-8 right-0 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 transition-opacity duration-300 pointer-events-none">Copied!</span>
                    </div>
                    <script>
                        document.getElementById('copyCheckinBtn').addEventListener('click', function() {
                            var btn = this;
                            var toast = document.getElementById('copyCheckinToast');
                            navigator.clipboard.writeText(document.getElementById('onlineCheckinUrl').value).then(function() {
                                btn.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                btn.classList.add('border-green-400', 'bg-green-50');
                                toast.classList.remove('opacity-0');
                                toast.classList.add('opacity-100');
                                setTimeout(function() {
                                    btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';
                                    btn.classList.remove('border-green-400', 'bg-green-50');
                                    toast.classList.remove('opacity-100');
                                    toast.classList.add('opacity-0');
                                }, 1500);
                            });
                        });
                    </script>
                </div>
                <form class="space-y-4 settings-form" data-settings-keys="ghl_webhook_book_appointment,ghl_webhook_no_show_sms">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Book Appointment</label>
                        <input type="url" name="ghl_webhook_book_appointment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="https://...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Show SMS</label>
                        <input type="url" name="ghl_webhook_no_show_sms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="https://...">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Webhook Settings</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-tax" class="settings-tab {{ $settingsTab === 'tax' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tax Configuration</h2>
                <form class="space-y-4 settings-form" data-settings-keys="tax_rate,tax_name,tax_apply_to_all">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="5.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax Name</label>
                            <input type="text" name="tax_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Sales Tax">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Apply Tax to All Services</label>
                            <p class="text-xs text-gray-500">Automatically calculate tax for all services</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="tax_apply_to_all" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Tax Settings</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Currency</h2>
                <form class="space-y-4 settings-form" data-settings-keys="currency_code,commission_rate,points_rate_fixed,points_rate_percentage,points_unit_value,points_per_unit,reward_percentage">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Currency</label>
                        <select name="currency_code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white">
                            <option value="USD">United States Dollar ($)</option>
                            <option value="EUR">Euro (€)</option>
                            <option value="GBP">British Pound (£)</option>
                            <option value="JPY">Japanese Yen (¥)</option>
                            <option value="AUD">Australian Dollar (A$)</option>
                            <option value="CAD">Canadian Dollar (C$)</option>
                            <option value="CHF">Swiss Franc (CHF)</option>
                            <option value="CNY">Chinese Yuan (¥)</option>
                            <option value="INR">Indian Rupee (₹)</option>
                            <option value="MXN">Mexican Peso ($)</option>
                            <option value="BRL">Brazilian Real (R$)</option>
                            <option value="RUB">Russian Ruble (₽)</option>
                            <option value="KRW">South Korean Won (₩)</option>
                            <option value="SGD">Singapore Dollar (S$)</option>
                            <option value="HKD">Hong Kong Dollar (HK$)</option>
                            <option value="NZD">New Zealand Dollar (NZ$)</option>
                            <option value="SEK">Swedish Krona (kr)</option>
                            <option value="NOK">Norwegian Krone (kr)</option>
                            <option value="DKK">Danish Krone (kr)</option>
                            <option value="PLN">Polish Złoty (zł)</option>
                            <option value="TRY">Turkish Lira (₺)</option>
                            <option value="ZAR">South African Rand (R)</option>
                            <option value="AED">UAE Dirham (د.إ)</option>
                            <option value="SAR">Saudi Riyal (﷼)</option>
                            <option value="THB">Thai Baht (฿)</option>
                            <option value="MYR">Malaysian Ringgit (RM)</option>
                            <option value="IDR">Indonesian Rupiah (Rp)</option>
                            <option value="PHP">Philippine Peso (₱)</option>
                            <option value="VND">Vietnamese Dong (₫)</option>
                            <option value="ILS">Israeli Shekel (₪)</option>
                            <option value="EGP">Egyptian Pound (£)</option>
                            <option value="PKR">Pakistani Rupee (₨)</option>
                            <option value="BDT">Bangladeshi Taka (৳)</option>
                            <option value="NGN">Nigerian Naira (₦)</option>
                            <option value="ARS">Argentine Peso ($)</option>
                            <option value="CLP">Chilean Peso ($)</option>
                            <option value="COP">Colombian Peso ($)</option>
                            <option value="PEN">Peruvian Sol (S/)</option>
                            <option value="CZK">Czech Koruna (Kč)</option>
                            <option value="HUF">Hungarian Forint (Ft)</option>
                            <option value="RON">Romanian Leu (lei)</option>
                            <option value="BGN">Bulgarian Lev (лв)</option>
                            <option value="HRK">Croatian Kuna (kn)</option>
                            <option value="ISK">Icelandic Króna (kr)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission (%)</label>
                        <div class="relative">
                            <input type="number" name="commission_rate" min="0" max="100" step="0.01" inputmode="decimal" value="30.00" class="w-full pr-10 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="30.00">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 text-sm">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Used to calculate technician commissions (example: 30 = 30%).</p>
                    </div>
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="text-md font-semibold text-gray-900 mb-3">Credit Points Rate</h3>
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="points_rate_fixed" value="1" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] settings-checkbox" id="points-rate-fixed-cb">
                                <span class="text-sm font-medium text-gray-700">Fixed Rate</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="points_rate_percentage" value="1" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] settings-checkbox" id="points-rate-percentage-cb">
                                <span class="text-sm font-medium text-gray-700">Percentage-Based</span>
                            </label>
                        </div>
                        <div id="points-fixed-fields" class="hidden">
                            <div class="flex items-end gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Points per Unit</label>
                                    <input type="number" name="points_per_unit" min="0" step="0.01" inputmode="decimal" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Points Unit Value</label>
                                    <input type="number" name="points_unit_value" min="0" step="0.01" inputmode="decimal" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div id="points-percentage-fields" class="hidden">
                            <div class="flex items-end gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Reward Percentage</label>
                                    <div class="relative inline-flex items-center">
                                        <input type="number" name="reward_percentage" min="0" max="100" step="0.01" inputmode="decimal" class="pr-12 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00">
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 text-base font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Currency Settings</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-discounts" class="settings-tab {{ $settingsTab === 'discounts' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Discount Settings</h2>
                </div>
                <form class="space-y-4 settings-form" data-settings-keys="discounts_enabled">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Discount System</label>
                            <p class="text-xs text-gray-500">Allow discounts and coupons to be applied at checkout</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="discounts_enabled" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Coupons</h2>
                    <button type="button" onclick="salonSettingsOpenAddCouponModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">+ Add Coupon</button>
                </div>
                <div id="settingsCouponsList" class="space-y-3">
                    <div class="text-center py-6 text-gray-500 text-sm">Loading coupons...</div>
                </div>
            </div>
        </div>
        <div id="tab-gift-cards" class="settings-tab {{ $settingsTab === 'gift-cards' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Gift Card Settings</h2>
                </div>
                <form class="space-y-4 settings-form" data-settings-keys="gift_cards_enabled">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Gift Cards</label>
                            <p class="text-xs text-gray-500">Allow gift cards to be applied at checkout</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="gift_cards_enabled" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Gift Cards</h2>
                    <button type="button" onclick="salonSettingsOpenAddGiftCardModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">+ Add Gift Card</button>
                </div>
                <div id="settingsGiftCardsList" class="space-y-3">
                    <div class="text-center py-6 text-gray-500 text-sm">Loading gift cards...</div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('scripts')
<script>
window.salonSettingsBootstrap = window.salonSettingsBootstrap || @json($settingsBootstrap ?? null);
function salonSettingsShowTab(tabName, element) {
    document.querySelectorAll('.settings-tab').forEach(function(tab) { tab.classList.add('hidden'); });
    document.querySelectorAll('.tab-button').forEach(function(btn) {
        btn.classList.remove('text-[#003047]', 'border-[#003047]');
        btn.classList.add('text-gray-500', 'border-transparent');
        btn.classList.remove('border-b-2');
    });
    var tab = document.getElementById('tab-' + tabName);
    if (tab) tab.classList.remove('hidden');
    if (element) {
        element.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
        element.classList.remove('text-gray-500', 'border-transparent');
    }
    var url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    if (tabName === 'discounts') salonSettingsLoadCoupons();
    if (tabName === 'gift-cards') salonSettingsLoadGiftCards();
}
function salonSettingsLoad() {
    var main = document.querySelector('main[data-settings-url]');
    if (!main || typeof salonApi === 'undefined') return;
    var url = main.getAttribute('data-settings-url');

    if (window.salonSettingsBootstrap && typeof window.salonSettingsBootstrap === 'object') {
        var data = window.salonSettingsBootstrap || {};
        Object.keys(data).forEach(function(key) {
            var val = data[key];
            var input = document.querySelector('[name="' + key + '"]');
            if (!input) return;
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = (val === '1' || val === 'true' || val === true);
            } else {
                input.value = (val === null || val === undefined) ? '' : String(val);
            }
        });
        salonPointsRateToggle();
        return;
    }

    fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var data = (res && res.data) ? res.data : {};
            Object.keys(data).forEach(function(key) {
                var val = data[key];
                var input = document.querySelector('[name="' + key + '"]');
                if (!input) return;
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = (val === '1' || val === 'true' || val === true);
                } else {
                    input.value = (val === null || val === undefined) ? '' : String(val);
                }
            });
            salonPointsRateToggle();
        })
        .catch(function() {});
}
function salonSettingsCollectFormPayload(form) {
    var keysAttr = form.getAttribute('data-settings-keys');
    if (!keysAttr) return {};
    var keys = keysAttr.split(',').map(function(k) { return k.trim(); });
    var payload = {};
    keys.forEach(function(key) {
        var input = form.querySelector('[name="' + key + '"]');
        if (!input) {
            var byForm = form.id && document.querySelector('input[name="' + key + '"][form="' + form.id + '"]');
            input = byForm;
        }
        if (input) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                payload[key] = input.checked ? '1' : '0';
            } else {
                payload[key] = input.value ? String(input.value).trim() : '';
            }
        }
    });
    return payload;
}
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var validTabs = ['general', 'tax', 'discounts', 'gift-cards'];
    var tabToShow = validTabs.indexOf(tabParam) >= 0 ? tabParam : 'general';
    var tabButton = document.querySelector('button[onclick*="salonSettingsShowTab(\'' + tabToShow + '\'"]');
    if (tabButton) {
        salonSettingsShowTab(tabToShow, tabButton);
    } else {
        var firstButton = document.querySelector('.tab-button');
        if (firstButton) {
            salonSettingsShowTab('general', firstButton);
        }
    }
    salonSettingsLoad();
    salonPointsRateInit();
    if (tabToShow === 'discounts') salonSettingsLoadCoupons();
    if (tabToShow === 'gift-cards') salonSettingsLoadGiftCards();
});
function salonPointsRateToggle() {
    var fixedCb = document.getElementById('points-rate-fixed-cb');
    var pctCb = document.getElementById('points-rate-percentage-cb');
    var fixedFields = document.getElementById('points-fixed-fields');
    var pctFields = document.getElementById('points-percentage-fields');
    if (!fixedCb || !pctCb || !fixedFields || !pctFields) return;
    fixedFields.classList.toggle('hidden', !fixedCb.checked);
    pctFields.classList.toggle('hidden', !pctCb.checked);
}
function salonPointsRateInit() {
    var fixedCb = document.getElementById('points-rate-fixed-cb');
    var pctCb = document.getElementById('points-rate-percentage-cb');
    if (!fixedCb || !pctCb) return;
    fixedCb.addEventListener('change', function() {
        if (fixedCb.checked) { pctCb.checked = false; }
        salonPointsRateToggle();
    });
    pctCb.addEventListener('change', function() {
        if (pctCb.checked) { fixedCb.checked = false; }
        salonPointsRateToggle();
    });
    salonPointsRateToggle();
}
function salonSettingsLoadCoupons() {
    var main = document.querySelector('main[data-coupons-url]');
    var listEl = document.getElementById('settingsCouponsList');
    if (!main || !listEl) return;
    var url = main.getAttribute('data-coupons-url');
    listEl.innerHTML = '<div class="text-center py-6 text-gray-500 text-sm">Loading coupons...</div>';
    fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var coupons = (res && res.data) ? res.data : [];
            if (coupons.length === 0) {
                listEl.innerHTML = '<div class="text-center py-6 text-gray-500 text-sm">No coupons yet. Click "+ Add Coupon" to create one.</div>';
                return;
            }
            var html = '';
            coupons.forEach(function(c) {
                var codeEsc = (c.code || '').replace(/</g, '&lt;');
                var descEsc = (c.description || '').replace(/</g, '&lt;');
                var statusClass = c.active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';
                var statusText = c.active ? 'Active' : 'Inactive';
                var dataCoupon = (typeof JSON !== 'undefined' && JSON.stringify) ? JSON.stringify(c).replace(/&/g, '&amp;').replace(/"/g, '&quot;') : '';
                html += '<div class="p-4 border border-gray-200 rounded-lg"><div class="flex items-center justify-between mb-2"><div><p class="font-medium text-gray-900">' + codeEsc + '</p><p class="text-xs text-gray-500">' + descEsc + ' &middot; ' + (c.discount_type === 'percent' ? c.discount_value + '%' : (window.salonCurrencySymbol || '$') + c.discount_value) + (c.min_order_amount ? ' (min ' + (window.salonCurrencySymbol || '$') + c.min_order_amount + ')' : '') + '</p></div><div class="flex items-center gap-2"><span class="px-2 py-1 ' + statusClass + ' text-xs font-medium rounded">' + statusText + '</span><button type="button" class="salon-settings-edit-coupon inline-flex items-center justify-center w-8 h-8 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" data-coupon="' + dataCoupon + '" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button><button type="button" class="salon-settings-delete-coupon inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" data-id="' + c.id + '" data-code="' + codeEsc + '" title="Remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div></div>';
            });
            listEl.innerHTML = html;
            listEl.querySelectorAll('.salon-settings-edit-coupon').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var raw = this.getAttribute('data-coupon');
                    if (raw) {
                        try {
                            var coupon = JSON.parse(raw.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
                            salonSettingsOpenEditCouponModal(coupon);
                        } catch (e) {}
                    }
                });
            });
            listEl.querySelectorAll('.salon-settings-delete-coupon').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    salonSettingsDeleteCoupon(parseInt(this.getAttribute('data-id'), 10), this.getAttribute('data-code') || '');
                });
            });
        })
        .catch(function() {
            listEl.innerHTML = '<div class="text-center py-6 text-red-500 text-sm">Failed to load coupons.</div>';
        });
}

function salonSettingsLoadGiftCards() {
    var main = document.querySelector('main[data-gift-cards-url]');
    var listEl = document.getElementById('settingsGiftCardsList');
    if (!main || !listEl) return;
    var url = main.getAttribute('data-gift-cards-url');
    listEl.innerHTML = '<div class="text-center py-6 text-gray-500 text-sm">Loading gift cards...</div>';
    fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var cards = (res && res.data) ? res.data : [];
            if (cards.length === 0) {
                listEl.innerHTML = '<div class="text-center py-6 text-gray-500 text-sm">No gift cards yet. Click "+ Add Gift Card" to create one.</div>';
                return;
            }
            var html = '';
            cards.forEach(function(c) {
                var codeEsc = (c.code || '').replace(/</g, '&lt;');
                var descEsc = (c.description || '').replace(/</g, '&lt;');
                var statusClass = c.active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';
                var statusText = c.active ? 'Active' : 'Inactive';
                var dataCard = (typeof JSON !== 'undefined' && JSON.stringify) ? JSON.stringify(c).replace(/&/g, '&amp;').replace(/"/g, '&quot;') : '';
                html += '<div class="p-4 border border-gray-200 rounded-lg"><div class="flex items-center justify-between mb-2"><div><p class="font-medium text-gray-900">' + codeEsc + '</p><p class="text-xs text-gray-500">' + descEsc + ' &middot; Balance ' + window.salonFormatMoney(Number(c.balance || 0)) + ' / Initial ' + window.salonFormatMoney(Number(c.initial_value || 0)) + '</p></div><div class="flex items-center gap-2"><span class="px-2 py-1 ' + statusClass + ' text-xs font-medium rounded">' + statusText + '</span><button type="button" class="salon-settings-edit-gift-card inline-flex items-center justify-center w-8 h-8 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" data-gift-card="' + dataCard + '" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button><button type="button" class="salon-settings-delete-gift-card inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" data-id="' + c.id + '" data-code="' + codeEsc + '" title="Remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div></div>';
            });
            listEl.innerHTML = html;
            listEl.querySelectorAll('.salon-settings-edit-gift-card').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var raw = this.getAttribute('data-gift-card');
                    if (raw) {
                        try {
                            var card = JSON.parse(raw.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
                            salonSettingsOpenEditGiftCardModal(card);
                        } catch (e) {}
                    }
                });
            });
            listEl.querySelectorAll('.salon-settings-delete-gift-card').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    salonSettingsDeleteGiftCard(parseInt(this.getAttribute('data-id'), 10), this.getAttribute('data-code') || '');
                });
            });
        })
        .catch(function() {
            listEl.innerHTML = '<div class="text-center py-6 text-red-500 text-sm">Failed to load gift cards.</div>';
        });
}
document.querySelectorAll('form.settings-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var main = document.querySelector('main[data-settings-update-url]');
        if (!main || typeof salonApi === 'undefined') return;
        var url = main.getAttribute('data-settings-update-url');
        var payload = salonSettingsCollectFormPayload(form);
        if (Object.keys(payload).length === 0) return;
        var button = form.querySelector('button[type="submit"]');
        var originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Saving…';
        salonApi.put(url, { settings: payload })
            .then(function() {
                button.textContent = 'Saved!';
                button.classList.add('bg-green-500', 'hover:bg-green-600');
                button.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
                setTimeout(function() {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-500', 'hover:bg-green-600');
                    button.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
                    button.disabled = false;
                }, 2000);
            })
            .catch(function() {
                button.textContent = originalText;
                button.disabled = false;
            });
    });
});
window.salonSettingsOpenAddCouponModal = function() {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Coupon</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form id="salon-coupon-form" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label><input type="text" name="code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="WELCOME10"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="10% off first visit"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label><select name="discount_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white"><option value="percent">Percentage</option><option value="fixed">Fixed Amount</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label><input type="number" name="discount_value" required step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="10"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount (optional)</label><input type="number" name="min_order_amount" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label><p class="text-xs text-gray-500">Enable this coupon immediately</p></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" value="1" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Coupon</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
        var form = document.getElementById('salon-coupon-form');
        if (form) form.addEventListener('submit', function(e) { salonSettingsSaveCoupon(e, null); });
    }
};
window.salonSettingsOpenEditCouponModal = function(coupon) {
    if (!coupon || !coupon.id) return;
    var code = (coupon.code || '').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var desc = (coupon.description || '').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var type = coupon.discount_type === 'fixed' ? 'fixed' : 'percent';
    var val = coupon.discount_value != null ? coupon.discount_value : '';
    var minOrder = coupon.min_order_amount != null && coupon.min_order_amount !== '' ? coupon.min_order_amount : '';
    var activeChecked = coupon.active ? ' checked' : '';
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Coupon</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form id="salon-coupon-form" class="space-y-4"><input type="hidden" name="id" value="' + coupon.id + '"><div><label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label><input type="text" name="code" required value="' + code + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" required value="' + desc + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label><select name="discount_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white"><option value="percent"' + (type === 'percent' ? ' selected' : '') + '>Percentage</option><option value="fixed"' + (type === 'fixed' ? ' selected' : '') + '>Fixed Amount</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label><input type="number" name="discount_value" required step="0.01" min="0" value="' + val + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount (optional)</label><input type="number" name="min_order_amount" step="0.01" min="0" value="' + minOrder + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" value="1" class="sr-only peer"' + activeChecked + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Coupon</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
        var form = document.getElementById('salon-coupon-form');
        if (form) form.addEventListener('submit', function(e) { salonSettingsSaveCoupon(e, coupon.id); });
    }
};
window.salonSettingsSaveCoupon = function(event, editId) {
    event.preventDefault();
    var form = event.target;
    var main = document.querySelector('main[data-coupons-url]');
    if (!main || typeof salonApi === 'undefined') return;
    var baseUrl = main.getAttribute('data-coupons-url');
    var payload = {
        code: (form.querySelector('[name="code"]') || {}).value || '',
        description: (form.querySelector('[name="description"]') || {}).value || '',
        discount_type: (form.querySelector('[name="discount_type"]') || {}).value || 'percent',
        discount_value: parseFloat((form.querySelector('[name="discount_value"]') || {}).value, 10) || 0,
        min_order_amount: (function() { var v = (form.querySelector('[name="min_order_amount"]') || {}).value; return v === '' || v === null ? null : parseFloat(v, 10); })(),
        active: form.querySelector('[name="active"]') ? form.querySelector('[name="active"]').checked : true
    };
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    var req = editId
        ? salonApi.put(baseUrl + '/' + editId, payload)
        : salonApi.post(baseUrl, payload);
    req.then(function() {
        if (typeof closeModal === 'function') closeModal();
        if (typeof showSuccessMessage === 'function') showSuccessMessage(editId ? 'Coupon updated successfully!' : 'Coupon added successfully!');
        salonSettingsLoadCoupons();
    }).catch(function(err) {
        if (btn) { btn.disabled = false; btn.textContent = editId ? 'Update Coupon' : 'Save Coupon'; }
        if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to save coupon.');
    });
};
window.salonSettingsDeleteCoupon = function(id, codeDisplay) {
    var codeEsc = (codeDisplay || '').replace(/</g, '&lt;');
    var confirmContent = '<div class="p-6"><div class="flex items-center gap-4 mb-4"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><div class="flex-1"><h3 class="text-xl font-bold text-gray-900">Remove Coupon</h3></div></div><p class="text-gray-700 mb-6 ml-16">Are you sure you want to remove coupon "<strong>' + codeEsc + '</strong>"? This action cannot be undone.</p><div class="flex justify-end gap-3 pt-4 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="button" id="salon-settings-confirm-delete-btn" data-id="' + id + '" class="px-6 py-3 text-white bg-red-500 rounded-lg hover:bg-red-600 transition font-medium active:scale-95">Yes, Remove</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(confirmContent);
        var btn = document.getElementById('salon-settings-confirm-delete-btn');
        if (btn) btn.addEventListener('click', function() { salonSettingsConfirmDeleteCoupon(parseInt(this.getAttribute('data-id'), 10)); });
    }
};

window.salonSettingsOpenAddGiftCardModal = function() {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add Gift Card</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form id="salon-gift-card-form" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Gift Card Code</label><input type="text" name="code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="GC-1000"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Holiday promo"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">PIN (optional)</label><input type="text" name="pin" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="1234"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Initial Value</label><input type="number" name="initial_value" required step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="50.00"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Current Balance</label><input type="number" name="balance" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="50.00"></div></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label><p class="text-xs text-gray-500">Enable this gift card immediately</p></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" value="1" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Gift Card</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
        var form = document.getElementById('salon-gift-card-form');
        if (form) form.addEventListener('submit', function(e) { salonSettingsSaveGiftCard(e, null); });
    }
};

window.salonSettingsOpenEditGiftCardModal = function(card) {
    if (!card || !card.id) return;
    var code = (card.code || '').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var desc = (card.description || '').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var pin = (card.pin || '').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var initialVal = card.initial_value != null ? card.initial_value : '';
    var balanceVal = card.balance != null ? card.balance : '';
    var activeChecked = card.active ? ' checked' : '';
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Gift Card</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form id="salon-gift-card-form" class="space-y-4"><input type="hidden" name="id" value="' + card.id + '"><div><label class="block text-sm font-medium text-gray-700 mb-2">Gift Card Code</label><input type="text" name="code" required value="' + code + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" value="' + desc + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">PIN (optional)</label><input type="text" name="pin" value="' + pin + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Initial Value</label><input type="number" name="initial_value" required step="0.01" min="0" value="' + initialVal + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Current Balance</label><input type="number" name="balance" step="0.01" min="0" value="' + balanceVal + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" value="1" class="sr-only peer"' + activeChecked + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Gift Card</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
        var form = document.getElementById('salon-gift-card-form');
        if (form) form.addEventListener('submit', function(e) { salonSettingsSaveGiftCard(e, card.id); });
    }
};

window.salonSettingsSaveGiftCard = function(event, editId) {
    event.preventDefault();
    var form = event.target;
    var main = document.querySelector('main[data-gift-cards-url]');
    if (!main || typeof salonApi === 'undefined') return;
    var baseUrl = main.getAttribute('data-gift-cards-url');
    var payload = {
        code: (form.querySelector('[name="code"]') || {}).value || '',
        description: (form.querySelector('[name="description"]') || {}).value || '',
        pin: (form.querySelector('[name="pin"]') || {}).value || null,
        initial_value: parseFloat((form.querySelector('[name="initial_value"]') || {}).value, 10) || 0,
        balance: (function() { var v = (form.querySelector('[name="balance"]') || {}).value; return v === '' || v === null ? null : parseFloat(v, 10); })(),
        active: form.querySelector('[name="active"]') ? form.querySelector('[name="active"]').checked : true
    };
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    var req = editId
        ? salonApi.put(baseUrl + '/' + editId, payload)
        : salonApi.post(baseUrl, payload);
    req.then(function() {
        if (typeof closeModal === 'function') closeModal();
        if (typeof showSuccessMessage === 'function') showSuccessMessage(editId ? 'Gift card updated successfully!' : 'Gift card added successfully!');
        salonSettingsLoadGiftCards();
    }).catch(function(err) {
        if (btn) { btn.disabled = false; btn.textContent = editId ? 'Update Gift Card' : 'Save Gift Card'; }
        if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to save gift card.');
    });
};

window.salonSettingsDeleteGiftCard = function(id, codeDisplay) {
    var codeEsc = (codeDisplay || '').replace(/</g, '&lt;');
    var confirmContent = '<div class="p-6"><div class="flex items-center gap-4 mb-4"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><div class="flex-1"><h3 class="text-xl font-bold text-gray-900">Remove Gift Card</h3></div></div><p class="text-gray-700 mb-6 ml-16">Are you sure you want to remove gift card "<strong>' + codeEsc + '</strong>"? This action cannot be undone.</p><div class="flex justify-end gap-3 pt-4 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="button" id="salon-settings-confirm-delete-gift-card" data-id="' + id + '" class="px-6 py-3 text-white bg-red-500 rounded-lg hover:bg-red-600 transition font-medium active:scale-95">Yes, Remove</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(confirmContent);
        var confirmBtn = document.getElementById('salon-settings-confirm-delete-gift-card');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                var main = document.querySelector('main[data-gift-cards-url]');
                if (!main || typeof salonApi === 'undefined') return;
                var baseUrl = main.getAttribute('data-gift-cards-url');
                salonApi.delete(baseUrl + '/' + id)
                    .then(function() {
                        if (typeof closeModal === 'function') closeModal();
                        if (typeof showSuccessMessage === 'function') showSuccessMessage('Gift card removed.');
                        salonSettingsLoadGiftCards();
                    })
                    .catch(function(err) {
                        if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to remove gift card.');
                    });
            });
        }
    }
};
window.salonSettingsConfirmDeleteCoupon = function(id) {
    var main = document.querySelector('main[data-coupons-url]');
    if (!main || typeof salonApi === 'undefined' || !salonApi.delete) return;
    var url = main.getAttribute('data-coupons-url') + '/' + id;
    salonApi.delete(url).then(function() {
        if (typeof closeModal === 'function') closeModal();
        if (typeof showSuccessMessage === 'function') showSuccessMessage('Coupon removed successfully!');
        salonSettingsLoadCoupons();
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to remove coupon.');
    });
};
</script>
@endpush
@endsection
