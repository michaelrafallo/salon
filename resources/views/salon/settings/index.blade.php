@extends('layouts.salon')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0" data-settings-url="{{ route('api.salon.settings.index') }}" data-settings-update-url="{{ route('api.salon.settings.update') }}" data-coupons-url="{{ route('api.salon.coupons.index') }}">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Settings</h1>
            <p class="text-gray-600 text-sm sm:text-base mt-1">Configure your nail salon POS system</p>
        </div>
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-gray-200">
                <button type="button" onclick="salonSettingsShowTab('general', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">General</button>
                <button type="button" onclick="salonSettingsShowTab('payment', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">Payment Gateways</button>
                <button type="button" onclick="salonSettingsShowTab('ghl', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">Clickaio</button>
                <button type="button" onclick="salonSettingsShowTab('tax', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">Tax & Currency</button>
                <button type="button" onclick="salonSettingsShowTab('discounts', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">Discounts & Coupons</button>
            </div>
        </div>
        <div id="tab-general" class="settings-tab hidden">
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
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Receipt Settings</h2>
                <form class="space-y-4 settings-form" data-settings-keys="receipt_print_enabled,receipt_include_business_info">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Receipt Printing</label>
                            <p class="text-xs text-gray-500">Automatically print receipts after payment</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="receipt_print_enabled" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Include Business Info on Receipt</label>
                            <p class="text-xs text-gray-500">Show business name, address, and contact on receipts</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="receipt_include_business_info" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-payment" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Authorize.net</h2>
                            <p class="text-xs text-gray-500">Secure card processing with tokenization</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="payment_authorize_net_enabled" value="1" class="sr-only peer settings-checkbox" form="form-payment-authorize">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <form id="form-payment-authorize" class="space-y-4 mt-4 settings-form" data-settings-keys="payment_authorize_net_enabled,payment_authorize_net_api_login_id,payment_authorize_net_transaction_key,payment_authorize_net_tokenization,payment_authorize_net_environment">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Login ID</label>
                            <input type="text" name="payment_authorize_net_api_login_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter API Login ID">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Key</label>
                            <input type="password" name="payment_authorize_net_transaction_key" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Transaction Key">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Tokenization</label>
                            <p class="text-xs text-gray-500">Store customer payment methods for faster checkout</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="payment_authorize_net_tokenization" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                        <select name="payment_authorize_net_environment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white">
                            <option value="sandbox">Sandbox (Testing)</option>
                            <option value="production">Production</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Authorize.net Settings</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">NMI.com</h2>
                            <p class="text-xs text-gray-500">Multi-processor support with fraud settings</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="payment_nmi_enabled" value="1" class="sr-only peer settings-checkbox" form="form-payment-nmi">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <form id="form-payment-nmi" class="space-y-4 mt-4 settings-form" data-settings-keys="payment_nmi_enabled,payment_nmi_username,payment_nmi_password,payment_nmi_fraud_detection">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" name="payment_nmi_username" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Username">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="payment_nmi_password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Password">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Fraud Detection</label>
                            <p class="text-xs text-gray-500">Use NMI fraud settings for secure transactions</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="payment_nmi_fraud_detection" value="1" class="sr-only peer settings-checkbox">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save NMI.com Settings</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-ghl" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Clickaio Integration</h2>
                            <p class="text-xs text-gray-500">LeadConnector API for seamless synchronization</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <form class="space-y-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                        <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter LeadConnector API Key">
                        <p class="text-xs text-gray-500 mt-1">Get your API key from Clickaio Settings > Integrations > API</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location ID</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Location ID">
                    </div>
                    <div class="border-t border-gray-200 pt-4 mt-6">
                        <h3 class="text-md font-semibold text-gray-900 mb-4">Sync Settings</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-900">Sync Customer Contacts</label>
                                    <p class="text-xs text-gray-500">Automatically sync POS customers with Clickaio Contacts</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-900">Sync Appointments to Calendar</label>
                                    <p class="text-xs text-gray-500">Send booking and appointment details to Clickaio Calendars</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-900">Sync Service Status to Opportunities</label>
                                    <p class="text-xs text-gray-500">Update Clickaio Opportunities/Pipelines with service status</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Clickaio Settings</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-tax" class="settings-tab hidden">
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
                <form class="space-y-4 settings-form" data-settings-keys="currency_code">
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
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Currency Settings</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-discounts" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Discounts & Coupons</h2>
                    <button type="button" onclick="salonSettingsOpenAddCouponModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">+ Add Coupon</button>
                </div>
                <form class="space-y-4 settings-form mb-4" data-settings-keys="discounts_enabled">
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
                <div id="settingsCouponsList" class="space-y-3">
                    <div class="text-center py-6 text-gray-500 text-sm">Loading coupons...</div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('scripts')
<script>
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
}
function salonSettingsLoad() {
    var main = document.querySelector('main[data-settings-url]');
    if (!main || typeof salonApi === 'undefined') return;
    var url = main.getAttribute('data-settings-url');
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
                    input.value = val || '';
                }
            });
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
    var validTabs = ['general', 'payment', 'ghl', 'tax', 'discounts'];
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
    if (tabToShow === 'discounts') salonSettingsLoadCoupons();
});
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
                html += '<div class="p-4 border border-gray-200 rounded-lg"><div class="flex items-center justify-between mb-2"><div><p class="font-medium text-gray-900">' + codeEsc + '</p><p class="text-xs text-gray-500">' + descEsc + ' &middot; ' + (c.discount_type === 'percent' ? c.discount_value + '%' : '$' + c.discount_value) + (c.min_order_amount ? ' (min $' + c.min_order_amount + ')' : '') + '</p></div><div class="flex items-center gap-2"><span class="px-2 py-1 ' + statusClass + ' text-xs font-medium rounded">' + statusText + '</span><button type="button" class="salon-settings-edit-coupon text-[#003047] hover:text-[#002535] text-sm font-medium" data-coupon="' + dataCoupon + '">Edit</button><button type="button" class="salon-settings-delete-coupon text-red-600 hover:text-red-700 text-sm font-medium" data-id="' + c.id + '" data-code="' + codeEsc + '">Remove</button></div></div></div>';
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
