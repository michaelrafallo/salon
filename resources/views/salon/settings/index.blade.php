@extends('layouts.salon')

@section('content')
@php
    $settingsTab = request()->query('tab', 'general');
    if (! in_array($settingsTab, ['general', 'clickaio', 'tax', 'discounts', 'gift-cards'], true)) {
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
                <button type="button" onclick="salonSettingsShowTab('clickaio', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'clickaio' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Clickaio API</button>
                <button type="button" onclick="salonSettingsShowTab('tax', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'tax' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Tax & Currency</button>
                <button type="button" onclick="salonSettingsShowTab('discounts', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'discounts' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Discounts & Coupons</button>
                <button type="button" onclick="salonSettingsShowTab('gift-cards', this)" class="tab-button px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $settingsTab === 'gift-cards' ? 'text-[#003047] border-[#003047]' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">Gift Cards</button>
            </div>
        </div>
        <div id="tab-general" class="settings-tab {{ $settingsTab === 'general' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Logo</h2>
                <div class="flex items-center gap-6" id="logoUploadSection" data-upload-url="{{ route('api.salon.settings.upload-logo') }}" data-remove-url="{{ route('api.salon.settings.remove-logo') }}">
                    <div id="logoPreviewContainer" class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 flex-shrink-0">
                        <div id="logoPlaceholder" class="text-center">
                            <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs text-gray-400 mt-1 block">No logo</span>
                        </div>
                        <img id="logoPreviewImg" src="" alt="Business Logo" class="w-full h-full object-contain hidden">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-3">Upload your business logo. Recommended: square image, max 2MB. Formats: JPG, PNG, GIF, SVG, WebP.</p>
                        <div class="flex items-center gap-3">
                            <label class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm cursor-pointer active:scale-95">
                                <span id="logoUploadBtnText">Upload Logo</span>
                                <input type="file" id="logoFileInput" accept="image/*" class="hidden">
                            </label>
                            <button type="button" id="logoRemoveBtn" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm hidden active:scale-95">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h2>
                <form class="space-y-4 settings-form" data-settings-keys="business_name,business_phone,business_email,business_address,timezone">
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
                        <div>
                            <label class="flex items-center justify-between text-sm font-medium text-gray-700 mb-2"><span>Timezone</span><span id="timezoneCurrentTime" class="text-xs text-gray-500 font-normal"></span></label>
                            <select name="timezone" id="timezoneSelect" class="w-full">
                                @foreach(timezone_identifiers_list() as $tz)
                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tab-clickaio" class="settings-tab {{ $settingsTab === 'clickaio' ? '' : 'hidden' }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
                <form class="space-y-4 settings-form" data-settings-keys="clickaio_location_id,clickaio_calendar_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location ID</label>
                            <input type="text" name="clickaio_location_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent font-mono text-sm" placeholder="Auto-filled on authorize or enter manually">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Calendar</label>
                            <div class="flex gap-2">
                                <select name="clickaio_calendar_id" id="clickaio_calendar_select" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm">
                                    <option value="">-- Select a calendar --</option>
                                </select>
                                <button type="button" id="clickaio_refresh_calendars" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-gray-600 flex-shrink-0" title="Refresh calendars">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </div>
                            <div id="clickaio_calendar_id_display" class="mt-1 hidden">
                                <span class="text-xs text-gray-500">ID: <code id="clickaio_calendar_id_text" class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono select-all cursor-pointer hover:bg-gray-200 active:scale-95 transition-all duration-150" title="Click to copy"></code></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Settings</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" data-clickaio-authorize-url="{{ route('salon.settings.clickaio.authorize') }}" data-clickaio-refresh-url="{{ route('api.salon.settings.clickaio.refresh') }}" data-clickaio-test-url="{{ route('api.salon.settings.clickaio.test-api') }}">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Clickaio Credentials</h2>
                <!-- Step 1: Credentials Form -->
                <div id="clickaio-credentials-section">
                    <form class="space-y-4 settings-form" data-settings-keys="clickaio_client_id,clickaio_client_secret,clickaio_version,clickaio_scopes" id="clickaio-credentials-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                                <input type="text" name="clickaio_client_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Client ID">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                                <input type="password" name="clickaio_client_secret" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter Client Secret">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                                <input type="text" name="clickaio_version" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="2.0.0" value="2.0.0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Scopes</label>
                            <textarea name="clickaio_scopes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="contacts.readonly contacts.write locations/customFields.readonly ...">contacts.readonly contacts.write locations/customFields.readonly locations/customFields.write locations/customValues.readonly locations/customValues.write opportunities.readonly opportunities.write</textarea>
                            <p class="text-xs text-gray-500 mt-1">Space-separated list of GoHighLevel API scopes.</p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Save Credentials
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Step 2: Authorize Button (shown after credentials saved, no token yet) -->
                <div id="clickaio-authorize-section" class="hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yellow-700">Credentials saved - authorization required</span>
                    </div>
                    <div class="space-y-3 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                                <code id="clickaio-auth-display-id" class="block px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono break-all"></code>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                                <div class="flex items-center gap-2">
                                    <code id="clickaio-auth-display-secret" class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono break-all"></code>
                                    <button type="button" id="clickaio-auth-toggle-secret" class="px-3 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition active:scale-95" title="Show/Hide">
                                        <svg class="w-5 h-5 text-gray-500 clickaio-eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg class="w-5 h-5 text-gray-500 clickaio-eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                                <code id="clickaio-auth-display-version" class="block px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono"></code>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scopes</label>
                            <div id="clickaio-auth-display-scopes" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg flex flex-wrap gap-1.5"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" id="clickaio-edit-credentials-btn" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition">Edit Credentials</button>
                        <a href="{{ route('salon.settings.clickaio.authorize') }}" id="clickaio-authorize-btn" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 inline-flex items-center gap-2 no-underline">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Authorize
                        </a>
                    </div>
                </div>
                <!-- Step 3: Connected with Token (shown after authorization) -->
                <div id="clickaio-connected-section" class="hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-700">Connected & Authorized</span>
                    </div>
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                                <code id="clickaio-conn-display-id" class="block px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono break-all"></code>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                                <div class="flex items-center gap-2">
                                    <code id="clickaio-conn-display-secret" class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono break-all"></code>
                                    <button type="button" class="clickaio-toggle-secret-btn px-3 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition active:scale-95" data-target="secret" title="Show/Hide">
                                        <svg class="w-5 h-5 text-gray-500 clickaio-eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg class="w-5 h-5 text-gray-500 clickaio-eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                                <code id="clickaio-conn-display-version" class="block px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-mono"></code>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scopes</label>
                            <div id="clickaio-conn-display-scopes" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg flex flex-wrap gap-1.5"></div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Access Token <span class="text-xs text-gray-400 font-normal">(auto-refreshes)</span></label>
                                <span id="clickaio-conn-display-expiry" class="text-xs text-gray-500"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <code id="clickaio-conn-display-token" class="flex-1 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 font-mono break-all"></code>
                                <button type="button" class="clickaio-toggle-secret-btn px-3 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition active:scale-95" data-target="token" title="Show/Hide">
                                    <svg class="w-5 h-5 text-gray-500 clickaio-eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg class="w-5 h-5 text-gray-500 clickaio-eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                                <button type="button" id="clickaio-copy-token-btn" class="px-3 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition active:scale-95" title="Copy Token">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <button type="button" id="clickaio-disconnect-btn" class="px-6 py-3 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition font-medium active:scale-95 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Disconnect
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" id="clickaio-refresh-token-btn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium active:scale-95 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Refresh Token
                            </button>
                            <a href="{{ route('salon.settings.clickaio.authorize') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 inline-flex items-center gap-2 no-underline">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Re-authorize
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">API Endpoints</h2>
                <form class="space-y-4 settings-form" data-settings-keys="clickaio_endpoint_find_contact,clickaio_endpoint_create_contact,clickaio_endpoint_book_appointment,clickaio_endpoint_delete_appointment">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Find Contact</label>
                            <input type="text" name="clickaio_endpoint_find_contact" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="https://services.leadconnectorhq.com/contacts/search">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Create Contact</label>
                            <input type="text" name="clickaio_endpoint_create_contact" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="https://services.leadconnectorhq.com/contacts">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Book Appointment</label>
                            <input type="text" name="clickaio_endpoint_book_appointment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="https://services.leadconnectorhq.com/calendars/events/appointments">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delete Appointment</label>
                            <input type="text" name="clickaio_endpoint_delete_appointment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="https://services.leadconnectorhq.com/calendars/events/">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Endpoints</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Clickaio Webhooks</h2>
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
                <form class="space-y-4 settings-form" data-settings-keys="ghl_webhook_no_show_sms">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Show SMS</label>
                        <input type="url" name="ghl_webhook_no_show_sms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="https://...">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Webhook Settings</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Test API Endpoint
                </h2>
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <select id="clickaio-test-method" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white font-mono text-sm font-semibold">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="PATCH">PATCH</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <input type="text" id="clickaio-test-url" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder="https://services.leadconnectorhq.com/contacts/?locationId=...">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Headers <span class="text-xs text-gray-400 font-normal">(JSON - Authorization auto-added)</span></label>
                            <button type="button" id="clickaio-test-headers-reset" class="text-xs text-blue-600 hover:text-blue-800">Reset to Default</button>
                        </div>
                        <textarea id="clickaio-test-headers" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder='{"Content-Type": "application/json"}'>{
  "Content-Type": "application/json",
  "Version": "2021-07-28"
}</textarea>
                    </div>
                    <div id="clickaio-test-body-section">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body <span class="text-xs text-gray-400 font-normal">(JSON)</span></label>
                        <textarea id="clickaio-test-body" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm font-mono" placeholder='{
  "key": "value"
}'></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="clickaio-test-send-btn" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            Send Request
                        </button>
                    </div>
                    <div id="clickaio-test-response-section" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Response</label>
                            <div class="flex items-center gap-3">
                                <span id="clickaio-test-response-status" class="text-xs font-mono font-semibold"></span>
                                <span id="clickaio-test-response-time" class="text-xs text-gray-500"></span>
                            </div>
                        </div>
                        <pre id="clickaio-test-response-body" class="px-4 py-3 bg-gray-900 text-green-400 rounded-lg text-sm font-mono overflow-x-auto max-h-96 overflow-y-auto whitespace-pre-wrap"></pre>
                    </div>
                </div>
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
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single { height: 48px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 0.5rem; background: #fff; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; color: #374151; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px; }
.select2-container--default.select2-container--focus .select2-selection--single { border-color: #003047; box-shadow: 0 0 0 2px rgba(0,48,71,.2); outline: none; }
.select2-dropdown { border-color: #d1d5db; border-radius: 0.5rem; }
.select2-results__option--highlighted[aria-selected] { background-color: #003047 !important; }
</style>
@endpush
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
        if (typeof $ !== 'undefined' && $.fn.select2) $('#timezoneSelect').trigger('change.select2');
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
            if (typeof $ !== 'undefined' && $.fn.select2) $('#timezoneSelect').trigger('change.select2');
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
    var validTabs = ['general', 'clickaio', 'tax', 'discounts', 'gift-cards'];
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
    salonLogoInit();
    if (tabToShow === 'discounts') salonSettingsLoadCoupons();
    if (tabToShow === 'gift-cards') salonSettingsLoadGiftCards();
});
function salonLogoInit() {
    var section = document.getElementById('logoUploadSection');
    if (!section) return;
    var uploadUrl = section.getAttribute('data-upload-url');
    var removeUrl = section.getAttribute('data-remove-url');
    var fileInput = document.getElementById('logoFileInput');
    var previewImg = document.getElementById('logoPreviewImg');
    var placeholder = document.getElementById('logoPlaceholder');
    var removeBtn = document.getElementById('logoRemoveBtn');
    var btnText = document.getElementById('logoUploadBtnText');
    var container = document.getElementById('logoPreviewContainer');

    // Load current logo from bootstrap data
    var bootstrap = window.salonSettingsBootstrap || {};
    if (bootstrap.business_logo) {
        var base = document.querySelector('meta[name="asset-url"]');
        var assetBase = base ? base.content.replace(/\/+$/, '') : '';
        previewImg.src = assetBase + '/storage/' + bootstrap.business_logo;
        previewImg.classList.remove('hidden');
        placeholder.classList.add('hidden');
        container.classList.remove('border-dashed', 'border-gray-300');
        container.classList.add('border-solid', 'border-gray-200');
        removeBtn.classList.remove('hidden');
    }

    fileInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be under 2MB.');
            this.value = '';
            return;
        }
        var formData = new FormData();
        formData.append('logo', file);
        btnText.textContent = 'Uploading…';
        fileInput.disabled = true;
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success && res.data && res.data.logo_url) {
                previewImg.src = res.data.logo_url;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
                container.classList.remove('border-dashed', 'border-gray-300');
                container.classList.add('border-solid', 'border-gray-200');
                removeBtn.classList.remove('hidden');
                // Update sidebar logo
                var sidebarLogo = document.getElementById('sidebarLogoImg');
                var sidebarIcon = document.getElementById('sidebarLogoIcon');
                var sidebarWrap = document.getElementById('sidebarLogoWrap');
                if (sidebarLogo) { sidebarLogo.src = res.data.logo_url; sidebarLogo.classList.remove('hidden'); }
                if (sidebarIcon) sidebarIcon.classList.add('hidden');
                if (sidebarWrap) sidebarWrap.classList.remove('bg-[#003047]');
            } else {
                alert(res.message || 'Upload failed.');
            }
        })
        .catch(function() { alert('Upload failed.'); })
        .finally(function() {
            btnText.textContent = 'Upload Logo';
            fileInput.disabled = false;
            fileInput.value = '';
        });
    });

    removeBtn.addEventListener('click', function() {
        if (!confirm('Remove business logo?')) return;
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(removeUrl, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                previewImg.src = '';
                previewImg.classList.add('hidden');
                placeholder.classList.remove('hidden');
                container.classList.add('border-dashed', 'border-gray-300');
                container.classList.remove('border-solid', 'border-gray-200');
                removeBtn.classList.add('hidden');
                // Reset sidebar logo
                var sidebarLogo = document.getElementById('sidebarLogoImg');
                var sidebarIcon = document.getElementById('sidebarLogoIcon');
                var sidebarWrap = document.getElementById('sidebarLogoWrap');
                if (sidebarLogo) sidebarLogo.classList.add('hidden');
                if (sidebarIcon) sidebarIcon.classList.remove('hidden');
                if (sidebarWrap) sidebarWrap.classList.add('bg-[#003047]');
            }
        })
        .catch(function() { alert('Failed to remove logo.'); });
    });
}
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
    if (form.id === 'clickaio-credentials-form') return;
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
                if (payload.business_name !== undefined) {
                    var sbn = document.getElementById('sidebarBusinessName');
                    if (sbn) sbn.textContent = payload.business_name || 'Nail Salon POS';
                }
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

// Clickaio API Settings
(function() {
    var credentialsSection = document.getElementById('clickaio-credentials-section');
    var authorizeSection = document.getElementById('clickaio-authorize-section');
    var connectedSection = document.getElementById('clickaio-connected-section');
    var credentialsForm = document.getElementById('clickaio-credentials-form');
    var authorizeBtn = document.getElementById('clickaio-authorize-btn');
    var reauthorizeBtn = document.getElementById('clickaio-reauthorize-btn');
    var disconnectBtn = document.getElementById('clickaio-disconnect-btn');
    var editCredentialsBtn = document.getElementById('clickaio-edit-credentials-btn');
    var copyTokenBtn = document.getElementById('clickaio-copy-token-btn');

    var storedClientId = '';
    var storedClientSecret = '';
    var storedToken = '';
    var storedVersion = '';
    var storedLocationId = '';
    var storedCalendarId = '';
    var storedScopes = '';
    var secretVisibility = { 'auth-secret': false, 'secret': false, 'token': false };

    function maskValue(val) {
        if (!val || val.length <= 8) return '••••••••••••••••';
        return val.substring(0, 4) + '••••••••' + val.substring(val.length - 4);
    }

    function renderScopeBadges(container, scopesStr) {
        container.innerHTML = '';
        if (!scopesStr) return;
        scopesStr.trim().split(/\s+/).forEach(function(scope) {
            if (!scope) return;
            var badge = document.createElement('span');
            badge.className = 'inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-mono rounded';
            badge.textContent = scope;
            container.appendChild(badge);
        });
    }

    function showSection(section) {
        credentialsSection.classList.add('hidden');
        authorizeSection.classList.add('hidden');
        connectedSection.classList.add('hidden');
        section.classList.remove('hidden');
    }

    function showCredentials() { showSection(credentialsSection); }

    function showAuthorize(clientId, clientSecret, version, scopes) {
        storedClientId = clientId;
        storedClientSecret = clientSecret;
        storedVersion = version || '';
        storedScopes = scopes || '';
        document.getElementById('clickaio-auth-display-id').textContent = clientId;
        document.getElementById('clickaio-auth-display-secret').textContent = maskValue(clientSecret);
        document.getElementById('clickaio-auth-display-version').textContent = version || '-';
        renderScopeBadges(document.getElementById('clickaio-auth-display-scopes'), scopes);
        secretVisibility['auth-secret'] = false;
        var btn = document.getElementById('clickaio-auth-toggle-secret');
        btn.querySelector('.clickaio-eye-show').classList.remove('hidden');
        btn.querySelector('.clickaio-eye-hide').classList.add('hidden');
        showSection(authorizeSection);
    }

    function formatExpiry(isoStr) {
        if (!isoStr) return '';
        try {
            var d = new Date(isoStr);
            var now = new Date();
            if (d <= now) return 'Expired - will auto-refresh on next use';
            var diff = Math.floor((d - now) / 60000);
            var hours = Math.floor(diff / 60);
            var mins = diff % 60;
            return 'Renews in ' + (hours > 0 ? hours + 'h ' : '') + mins + 'm';
        } catch (e) { return ''; }
    }

    function showConnected(clientId, clientSecret, token, version, scopes, expiresAt) {
        storedClientId = clientId;
        storedClientSecret = clientSecret;
        storedToken = token;
        storedVersion = version || '';
        storedScopes = scopes || '';
        document.getElementById('clickaio-conn-display-id').textContent = clientId;
        document.getElementById('clickaio-conn-display-secret').textContent = maskValue(clientSecret);
        document.getElementById('clickaio-conn-display-token').textContent = maskValue(token);
        document.getElementById('clickaio-conn-display-version').textContent = version || '-';
        document.getElementById('clickaio-conn-display-expiry').textContent = formatExpiry(expiresAt);
        renderScopeBadges(document.getElementById('clickaio-conn-display-scopes'), scopes);
        secretVisibility['secret'] = false;
        secretVisibility['token'] = false;
        connectedSection.querySelectorAll('.clickaio-toggle-secret-btn').forEach(function(btn) {
            btn.querySelector('.clickaio-eye-show').classList.remove('hidden');
            btn.querySelector('.clickaio-eye-hide').classList.add('hidden');
        });
        showSection(connectedSection);
    }

    // Toggle show/hide for authorize section secret
    document.getElementById('clickaio-auth-toggle-secret').addEventListener('click', function() {
        secretVisibility['auth-secret'] = !secretVisibility['auth-secret'];
        var display = document.getElementById('clickaio-auth-display-secret');
        display.textContent = secretVisibility['auth-secret'] ? storedClientSecret : maskValue(storedClientSecret);
        this.querySelector('.clickaio-eye-show').classList.toggle('hidden', secretVisibility['auth-secret']);
        this.querySelector('.clickaio-eye-hide').classList.toggle('hidden', !secretVisibility['auth-secret']);
    });

    // Toggle show/hide for connected section
    connectedSection.querySelectorAll('.clickaio-toggle-secret-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            secretVisibility[target] = !secretVisibility[target];
            var displayEl = target === 'secret' ? document.getElementById('clickaio-conn-display-secret') : document.getElementById('clickaio-conn-display-token');
            var rawValue = target === 'secret' ? storedClientSecret : storedToken;
            displayEl.textContent = secretVisibility[target] ? rawValue : maskValue(rawValue);
            this.querySelector('.clickaio-eye-show').classList.toggle('hidden', secretVisibility[target]);
            this.querySelector('.clickaio-eye-hide').classList.toggle('hidden', !secretVisibility[target]);
        });
    });

    // Copy token
    if (copyTokenBtn) {
        copyTokenBtn.addEventListener('click', function() {
            var btn = this;
            navigator.clipboard.writeText(storedToken).then(function() {
                btn.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                btn.classList.add('border-green-400', 'bg-green-50');
                setTimeout(function() {
                    btn.innerHTML = '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';
                    btn.classList.remove('border-green-400', 'bg-green-50');
                }, 1500);
            });
        });
    }

    // Copy buttons for locationId and calendarId
    document.querySelectorAll('.clickaio-copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var copyKey = this.getAttribute('data-copy');
            var inputId = copyKey === 'auth-location' ? 'clickaio-auth-input-location'
                : copyKey === 'auth-calendar' ? 'clickaio-auth-input-calendar'
                : copyKey === 'conn-location' ? 'clickaio-conn-input-location'
                : copyKey === 'conn-calendar' ? 'clickaio-conn-input-calendar' : null;
            if (!inputId) return;
            var value = document.getElementById(inputId).value.trim();
            if (!value) return;
            var el = this;
            navigator.clipboard.writeText(value).then(function() {
                el.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                el.classList.add('border-green-400', 'bg-green-50');
                setTimeout(function() {
                    el.innerHTML = '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';
                    el.classList.remove('border-green-400', 'bg-green-50');
                }, 1500);
            });
        });
    });

    // Auto-save locationId and calendarId on blur
    var settingsUpdateUrl = document.querySelector('[data-settings-update-url]');
    var updateUrl = settingsUpdateUrl ? settingsUpdateUrl.getAttribute('data-settings-update-url') : null;
    document.querySelectorAll('.clickaio-inline-edit').forEach(function(input) {
        var lastValue = input.value;
        input.addEventListener('blur', function() {
            var newValue = this.value.trim();
            if (newValue === lastValue) return;
            lastValue = newValue;
            var key = this.getAttribute('data-key');
            if (!key || !updateUrl || typeof salonApi === 'undefined') return;
            var settings = {};
            settings[key] = newValue;
            if (key === 'clickaio_location_id') storedLocationId = newValue;
            if (key === 'clickaio_calendar_id') storedCalendarId = newValue;
            // Sync all matching inputs
            document.querySelectorAll('.clickaio-inline-edit[data-key="' + key + '"]').forEach(function(el) { el.value = newValue; });
            document.querySelectorAll('[name="' + key + '"]').forEach(function(el) { el.value = newValue; });
            salonApi.put(updateUrl, { settings: settings })
                .then(function() {
                    input.classList.add('border-green-400');
                    setTimeout(function() { input.classList.remove('border-green-400'); }, 1500);
                });
        });
    });

    // Edit credentials button
    if (editCredentialsBtn) {
        editCredentialsBtn.addEventListener('click', function() {
            showCredentials();
        });
    }

    // Save credentials form
    if (credentialsForm) {
        credentialsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var main = document.querySelector('main[data-settings-update-url]');
            if (!main || typeof salonApi === 'undefined') return;
            var url = main.getAttribute('data-settings-update-url');
            var payload = salonSettingsCollectFormPayload(credentialsForm);
            if (!payload.clickaio_client_id || !payload.clickaio_client_secret) {
                if (typeof showErrorMessage === 'function') showErrorMessage('Please enter both Client ID and Client Secret.');
                return;
            }
            var button = credentialsForm.querySelector('button[type="submit"]');
            var originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving...';
            salonApi.put(url, { settings: payload })
                .then(function() {
                    if (typeof showSuccessMessage === 'function') showSuccessMessage('Credentials saved. Click Authorize to connect.');
                    showAuthorize(payload.clickaio_client_id, payload.clickaio_client_secret, payload.clickaio_version, payload.clickaio_scopes);
                })
                .catch(function() {
                    if (typeof showErrorMessage === 'function') showErrorMessage('Failed to save credentials.');
                })
                .finally(function() {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                });
        });
    }

    // Refresh token button
    var refreshTokenBtn = document.getElementById('clickaio-refresh-token-btn');
    if (refreshTokenBtn) {
        refreshTokenBtn.addEventListener('click', function() {
            var container = this.closest('[data-clickaio-refresh-url]');
            if (!container || typeof salonApi === 'undefined') return;
            var refreshUrl = container.getAttribute('data-clickaio-refresh-url');
            var btn = this;
            var originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Refreshing...';
            salonApi.post(refreshUrl, {})
                .then(function(res) {
                    var token = res && res.data && res.data.access_token ? res.data.access_token : '';
                    if (token) {
                        storedToken = token;
                        document.getElementById('clickaio-conn-display-token').textContent = maskValue(token);
                        secretVisibility['token'] = false;
                        var tokenToggle = connectedSection.querySelector('.clickaio-toggle-secret-btn[data-target="token"]');
                        if (tokenToggle) {
                            tokenToggle.querySelector('.clickaio-eye-show').classList.remove('hidden');
                            tokenToggle.querySelector('.clickaio-eye-hide').classList.add('hidden');
                        }
                        if (typeof showSuccessMessage === 'function') showSuccessMessage('Token refreshed successfully!');
                    } else {
                        if (typeof showErrorMessage === 'function') showErrorMessage('Refresh succeeded but no token received.');
                    }
                })
                .catch(function(err) {
                    if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to refresh token. Try re-authorizing.');
                })
                .finally(function() {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
        });
    }

    // Disconnect button
    if (disconnectBtn) {
        disconnectBtn.addEventListener('click', function() {
            var main = document.querySelector('main[data-settings-update-url]');
            if (!main || typeof salonApi === 'undefined') return;
            var url = main.getAttribute('data-settings-update-url');
            var btn = this;
            btn.disabled = true;
            salonApi.put(url, { settings: { clickaio_access_token: '', clickaio_refresh_token: '', clickaio_token_expires_at: '' } })
                .then(function() {
                    storedToken = '';
                    showAuthorize(storedClientId, storedClientSecret, storedVersion, storedScopes);
                    if (typeof showSuccessMessage === 'function') showSuccessMessage('Clickaio API disconnected.');
                })
                .catch(function() {
                    if (typeof showErrorMessage === 'function') showErrorMessage('Failed to disconnect.');
                })
                .finally(function() { btn.disabled = false; });
        });
    }

    // Determine initial state after settings load
    function clickaioCheckState(data) {
        var clientId = (data && data.clickaio_client_id) || '';
        var clientSecret = (data && data.clickaio_client_secret) || '';
        var token = (data && data.clickaio_access_token) || '';
        var version = (data && data.clickaio_version) || '';
        var locationId = (data && data.clickaio_location_id) || '';
        var calendarId = (data && data.clickaio_calendar_id) || '';
        var scopes = (data && data.clickaio_scopes) || '';
        var expiresAt = (data && data.clickaio_token_expires_at) || '';
        // Also populate form inputs
        var idInput = document.querySelector('[name="clickaio_client_id"]');
        var secretInput = document.querySelector('[name="clickaio_client_secret"]');
        var versionInput = document.querySelector('[name="clickaio_version"]');
        var locationInput = document.querySelector('[name="clickaio_location_id"]');
        var calendarInput = document.querySelector('[name="clickaio_calendar_id"]');
        var scopesInput = document.querySelector('[name="clickaio_scopes"]');
        var endpointFindInput = document.querySelector('[name="clickaio_endpoint_find_contact"]');
        var endpointBookInput = document.querySelector('[name="clickaio_endpoint_book_appointment"]');
        var endpointDeleteInput = document.querySelector('[name="clickaio_endpoint_delete_appointment"]');
        if (idInput && !idInput.value && clientId) idInput.value = clientId;
        if (secretInput && !secretInput.value && clientSecret) secretInput.value = clientSecret;
        if (versionInput && !versionInput.value && version) versionInput.value = version;
        if (locationInput && !locationInput.value && locationId) locationInput.value = locationId;
        storedCalendarId = calendarId;
        if (calendarInput && calendarId) {
            // If dropdown has no matching option yet, add a placeholder one
            if (!calendarInput.querySelector('option[value="' + calendarId + '"]')) {
                var opt = document.createElement('option');
                opt.value = calendarId;
                opt.textContent = calendarId + ' (saved)';
                calendarInput.appendChild(opt);
            }
            calendarInput.value = calendarId;
        }
        // Auto-fetch calendars if we have a token and location
        if (token && locationId) fetchGhlCalendars();
        if (scopesInput && !scopesInput.value && scopes) scopesInput.value = scopes;
        if (endpointFindInput && !endpointFindInput.value && data.clickaio_endpoint_find_contact) endpointFindInput.value = data.clickaio_endpoint_find_contact;
        if (endpointBookInput && !endpointBookInput.value && data.clickaio_endpoint_book_appointment) endpointBookInput.value = data.clickaio_endpoint_book_appointment;
        if (endpointDeleteInput && !endpointDeleteInput.value && data.clickaio_endpoint_delete_appointment) endpointDeleteInput.value = data.clickaio_endpoint_delete_appointment;
        if (clientId && clientSecret && token) {
            showConnected(clientId, clientSecret, token, version, scopes, expiresAt);
        } else if (clientId && clientSecret) {
            showAuthorize(clientId, clientSecret, version, scopes);
        } else {
            showCredentials();
        }
    }
    var origSettingsLoad = window.salonSettingsLoad;
    window.salonSettingsLoad = function() {
        origSettingsLoad.apply(this, arguments);
        if (window.salonSettingsBootstrap && typeof window.salonSettingsBootstrap === 'object') {
            setTimeout(function() { clickaioCheckState(window.salonSettingsBootstrap); }, 50);
        } else {
            // For API fetch path, re-fetch to get the token
            var main = document.querySelector('main[data-settings-url]');
            if (main) {
                var url = main.getAttribute('data-settings-url');
                fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(res) { clickaioCheckState(res && res.data ? res.data : {}); })
                    .catch(function() { clickaioCheckState({}); });
            }
        }
    };
    // Test API (mini Postman)
    var testMethodEl = document.getElementById('clickaio-test-method');
    var testUrlEl = document.getElementById('clickaio-test-url');
    var testHeadersEl = document.getElementById('clickaio-test-headers');
    var testBodyEl = document.getElementById('clickaio-test-body');
    var testBodySection = document.getElementById('clickaio-test-body-section');
    var testSendBtn = document.getElementById('clickaio-test-send-btn');
    var testResponseSection = document.getElementById('clickaio-test-response-section');
    var testResponseStatus = document.getElementById('clickaio-test-response-status');
    var testResponseTime = document.getElementById('clickaio-test-response-time');
    var testResponseBody = document.getElementById('clickaio-test-response-body');
    var testHeadersResetBtn = document.getElementById('clickaio-test-headers-reset');

    var defaultHeaders = '{\n  "Content-Type": "application/json",\n  "Version": "2021-07-28"\n}';

    // Show/hide body based on method
    if (testMethodEl) {
        testMethodEl.addEventListener('change', function() {
            var m = this.value;
            if (m === 'GET' || m === 'DELETE') {
                testBodySection.classList.add('hidden');
            } else {
                testBodySection.classList.remove('hidden');
            }
        });
    }

    // Reset headers
    if (testHeadersResetBtn) {
        testHeadersResetBtn.addEventListener('click', function() {
            testHeadersEl.value = defaultHeaders;
        });
    }

    // Send request
    if (testSendBtn) {
        testSendBtn.addEventListener('click', function() {
            var container = this.closest('[data-clickaio-test-url]');
            if (!container || typeof salonApi === 'undefined') return;
            var apiUrl = container.getAttribute('data-clickaio-test-url');
            var method = testMethodEl.value;
            var url = testUrlEl.value.trim();

            if (!url) {
                if (typeof showErrorMessage === 'function') showErrorMessage('Please enter a URL.');
                return;
            }

            // Parse headers
            var headers = {};
            var headersStr = testHeadersEl.value.trim();
            if (headersStr) {
                try { headers = JSON.parse(headersStr); }
                catch (e) {
                    if (typeof showErrorMessage === 'function') showErrorMessage('Invalid JSON in Headers field.');
                    return;
                }
            }

            // Parse body
            var body = null;
            if (method !== 'GET' && method !== 'DELETE') {
                var bodyStr = testBodyEl.value.trim();
                if (bodyStr) {
                    try { body = JSON.parse(bodyStr); }
                    catch (e) {
                        if (typeof showErrorMessage === 'function') showErrorMessage('Invalid JSON in Body field.');
                        return;
                    }
                }
            }

            var btn = this;
            var originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Sending...';
            testResponseSection.classList.add('hidden');

            var payload = { method: method, url: url, headers: headers };
            if (body !== null) payload.body = body;

            salonApi.post(apiUrl, payload)
                .then(function(res) {
                    var status = res.status || 0;
                    var timeMs = res.time_ms || 0;
                    var responseBody = res.body;

                    // Status badge
                    testResponseStatus.textContent = status + ' ' + (status >= 200 && status < 300 ? 'OK' : status >= 400 ? 'Error' : '');
                    testResponseStatus.className = 'text-xs font-mono font-semibold px-2 py-0.5 rounded ' +
                        (status >= 200 && status < 300 ? 'bg-green-100 text-green-800' :
                         status >= 400 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');

                    testResponseTime.textContent = timeMs + 'ms';

                    // Format response body
                    if (typeof responseBody === 'object' && responseBody !== null) {
                        testResponseBody.textContent = JSON.stringify(responseBody, null, 2);
                    } else {
                        testResponseBody.textContent = String(responseBody || '');
                    }

                    testResponseSection.classList.remove('hidden');
                })
                .catch(function(err) {
                    testResponseStatus.textContent = 'Error';
                    testResponseStatus.className = 'text-xs font-mono font-semibold px-2 py-0.5 rounded bg-red-100 text-red-800';
                    testResponseTime.textContent = '';
                    testResponseBody.textContent = err && err.message ? err.message : 'Request failed.';
                    testResponseSection.classList.remove('hidden');
                })
                .finally(function() {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
        });
    }
    // Fetch GHL calendars and populate dropdown
    function fetchGhlCalendars() {
        var select = document.getElementById('clickaio_calendar_select');
        var refreshBtn = document.getElementById('clickaio_refresh_calendars');
        if (!select) return;
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.querySelector('svg').classList.add('animate-spin');
        }
        fetch('/lucky/nailsalon-app/public/api/salon/settings/clickaio/calendars', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && data.calendars) {
                var currentVal = select.value || storedCalendarId;
                select.innerHTML = '<option value="">-- Select a calendar --</option>';
                data.calendars.forEach(function(cal) {
                    var opt = document.createElement('option');
                    opt.value = cal.id;
                    opt.textContent = cal.name;
                    if (cal.id === currentVal) opt.selected = true;
                    select.appendChild(opt);
                });
                updateCalendarIdDisplay();
            }
        })
        .catch(function(e) { console.error('Failed to fetch calendars:', e); })
        .finally(function() {
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.querySelector('svg').classList.remove('animate-spin');
            }
        });
    }

    var refreshCalBtn = document.getElementById('clickaio_refresh_calendars');
    if (refreshCalBtn) {
        refreshCalBtn.addEventListener('click', function() { fetchGhlCalendars(); });
    }

    // Show/hide calendar ID below dropdown
    function updateCalendarIdDisplay() {
        var select = document.getElementById('clickaio_calendar_select');
        var display = document.getElementById('clickaio_calendar_id_display');
        var text = document.getElementById('clickaio_calendar_id_text');
        if (!select || !display || !text) return;
        if (select.value) {
            text.textContent = select.value;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    }

    var calSelect = document.getElementById('clickaio_calendar_select');
    if (calSelect) {
        calSelect.addEventListener('change', updateCalendarIdDisplay);
    }

    var calIdText = document.getElementById('clickaio_calendar_id_text');
    if (calIdText) {
        calIdText.addEventListener('click', function() {
            var el = this;
            var originalText = el.textContent;
            navigator.clipboard.writeText(originalText).then(function() {
                el.textContent = 'Copied!';
                el.classList.remove('bg-gray-100');
                el.classList.add('bg-green-100', 'text-green-700', 'ring-2', 'ring-green-400');
                el.style.transition = 'all 0.2s ease';
                el.style.transform = 'scale(1.1)';
                setTimeout(function() { el.style.transform = 'scale(1)'; }, 200);
                setTimeout(function() {
                    el.textContent = originalText;
                    el.classList.remove('bg-green-100', 'text-green-700', 'ring-2', 'ring-green-400');
                    el.classList.add('bg-gray-100');
                }, 1200);
            });
        });
    }

    // Initial display update
    updateCalendarIdDisplay();
})();
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#timezoneSelect').select2({ placeholder: 'Select timezone', width: '100%' });

    function updateTimezoneClock() {
        var tz = $('#timezoneSelect').val();
        if (tz) {
            try {
                var now = new Date().toLocaleString('en-US', { timeZone: tz, weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
                $('#timezoneCurrentTime').text(now);
            } catch(e) { $('#timezoneCurrentTime').text(''); }
        } else {
            $('#timezoneCurrentTime').text('');
        }
    }
    $('#timezoneSelect').on('change', updateTimezoneClock);
    updateTimezoneClock();
    setInterval(updateTimezoneClock, 1000);
});
</script>
@endpush
@endsection
