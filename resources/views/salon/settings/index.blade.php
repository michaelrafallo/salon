@extends('layouts.salon')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
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
                <button type="button" onclick="salonSettingsShowTab('roles', this)" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent">Roles & Permissions</button>
            </div>
        </div>
        <div id="tab-general" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Nail Salon Name" value="Lucky Nail Salon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="(555) 123-4567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="info@salon.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="123 Main Street">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Changes</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Receipt Settings</h2>
                <form class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Receipt Printing</label>
                            <p class="text-xs text-gray-500">Automatically print receipts after payment</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Include Business Info on Receipt</label>
                            <p class="text-xs text-gray-500">Show business name, address, and contact on receipts</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
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
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <form class="space-y-4 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Login ID</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter API Login ID">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Key</label>
                            <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Transaction Key">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Tokenization</label>
                            <p class="text-xs text-gray-500">Store customer payment methods for faster checkout</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white">
                            <option>Sandbox (Testing)</option>
                            <option>Production</option>
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
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <form class="space-y-4 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Username">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Password">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Enable Fraud Detection</label>
                            <p class="text-xs text-gray-500">Use NMI fraud settings for secure transactions</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
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
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Tax Rate (%)</label>
                            <input type="number" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="5.00" value="5.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax Name</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Sales Tax" value="Sales Tax">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Apply Tax to All Services</label>
                            <p class="text-xs text-gray-500">Automatically calculate tax for all services</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
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
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Currency</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white">
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
                    <button onclick="salonSettingsOpenAddCouponModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">+ Add Coupon</button>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Enable Discount System</label>
                        <p class="text-xs text-gray-500">Allow discounts and coupons to be applied at checkout</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="space-y-3">
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-medium text-gray-900">WELCOME10</p>
                                <p class="text-xs text-gray-500">10% off first visit</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Active</span>
                                <button onclick="salonSettingsOpenEditCouponModal('WELCOME10', '10% off first visit', 'percent', 10)" class="text-[#003047] hover:text-[#002535] text-sm font-medium">Edit</button>
                                <button onclick="salonSettingsDeleteCoupon('WELCOME10')" class="text-red-600 hover:text-red-700 text-sm font-medium">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-medium text-gray-900">SPRING20</p>
                                <p class="text-xs text-gray-500">$20 off orders over $100</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Active</span>
                                <button onclick="salonSettingsOpenEditCouponModal('SPRING20', '$20 off orders over $100', 'fixed', 20)" class="text-[#003047] hover:text-[#002535] text-sm font-medium">Edit</button>
                                <button onclick="salonSettingsDeleteCoupon('SPRING20')" class="text-red-600 hover:text-red-700 text-sm font-medium">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tab-roles" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Role-Based Access Control</h2>
                <div class="space-y-4">
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">Admin</h3>
                                <p class="text-xs text-gray-500">Full system access and configuration</p>
                            </div>
                            <span class="px-3 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded-full">2 users</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">All Permissions</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">Receptionist</h3>
                                <p class="text-xs text-gray-500">Process payments and manage booking</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">3 users</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">View Booking</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">Process Payments</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">View Customers</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">Technician</h3>
                                <p class="text-xs text-gray-500">Manage assigned services and workload</p>
                            </div>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">4 users</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">View Booking</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">Update Service Status</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700">View Schedule</span>
                            </div>
                        </div>
                    </div>
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
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var validTabs = ['general', 'payment', 'ghl', 'tax', 'discounts', 'roles'];
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
});
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var button = this.querySelector('button[type="submit"]');
        var originalText = button.textContent;
        button.textContent = 'Saved!';
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        button.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('bg-green-500', 'hover:bg-green-600');
            button.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
        }, 2000);
    });
});
window.salonSettingsOpenAddCouponModal = function() {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Coupon</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonSettingsSaveCoupon(event, \'add\')" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label><input type="text" name="code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="WELCOME10"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="10% off first visit"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label><select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white"><option value="percent">Percentage</option><option value="fixed">Fixed Amount</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label><input type="number" name="value" required step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="10"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount (optional)</label><input type="number" name="min_amount" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label><p class="text-xs text-gray-500">Enable this coupon immediately</p></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Coupon</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
};
window.salonSettingsOpenEditCouponModal = function(code, description, type, value) {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Coupon</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonSettingsSaveCoupon(event, \'edit\')" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label><input type="text" name="code" required value="' + code + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><input type="text" name="description" required value="' + description + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label><select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white"><option value="percent"' + (type === 'percent' ? ' selected' : '') + '>Percentage</option><option value="fixed"' + (type === 'fixed' ? ' selected' : '') + '>Fixed Amount</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label><input type="number" name="value" required step="0.01" value="' + value + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Coupon</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
};
window.salonSettingsSaveCoupon = function(event, action) {
    event.preventDefault();
    if (typeof closeModal === 'function') {
        closeModal();
    }
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage(action === 'add' ? 'Coupon added successfully!' : 'Coupon updated successfully!');
    }
    setTimeout(function() { location.reload(); }, 1500);
};
window.salonSettingsDeleteCoupon = function(code) {
    var confirmContent = '<div class="p-6"><div class="flex items-center gap-4 mb-4"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><div class="flex-1"><h3 class="text-xl font-bold text-gray-900">Remove Coupon</h3></div></div><p class="text-gray-700 mb-6 ml-16">Are you sure you want to remove coupon "<strong>' + code + '</strong>"? This action cannot be undone.</p><div class="flex justify-end gap-3 pt-4 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonSettingsConfirmDeleteCoupon(\'' + code + '\')" class="px-6 py-3 text-white bg-red-500 rounded-lg hover:bg-red-600 transition font-medium active:scale-95">Yes, Remove</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(confirmContent);
    }
};
window.salonSettingsConfirmDeleteCoupon = function(code) {
    if (typeof closeModal === 'function') {
        closeModal();
    }
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage('Coupon "' + code + '" removed successfully!');
    }
    setTimeout(function() { location.reload(); }, 1500);
};
</script>
@endpush
@endsection
