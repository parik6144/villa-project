<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mt-4 mb-4">
            <x-input-label :value="__('Assign Roles')" required />

            @foreach ($roles as $role)
                @if ($role !== 'admin')
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="roles[]" value="{{ $role }}"
                               {{ in_array($role, old('roles', [])) ? 'checked' : '' }}
                               class="role-checkbox mr-2">
                        <span>{{ ucfirst($role) }}</span>
                    </label>
                @endif
            @endforeach

            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name">
                {{ __('First Name') }} <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="last_name">
                {{ __('Last Name') }} <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email">
                {{ __('Email') }} <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password">
                {{ __('Password') }} <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <small class="text-gray-500">
                Minimum 8 characters, mixed case & special symbols.
            </small>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation">
                {{ __('Confirm Password') }} <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div id="common-meta-fields" class="hidden">
            <!-- Phone Number -->
            <div class="mt-4">
                <x-input-label for="number">
                    {{ __('Phone Number') }} <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="number" class="block mt-1 w-full maska" data-maska="+## ### ###-####" required type="text" name="number" :value="old('number')" placeholder="+1234567890" />
                <x-input-error :messages="$errors->get('number')" class="mt-2" />
            </div>


            <!-- Social Media -->
            <div class="mt-4">
                <x-input-label for="telegram" :value="__('Telegram')" />
                <x-text-input id="telegram" class="block mt-1 w-full maska" data-maska="+## ### ###-####" type="text" name="telegram" :value="old('telegram')" placeholder="+1234567890" />
                <x-input-error :messages="$errors->get('telegram')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="viber" :value="__('Viber')" />
                <x-text-input id="viber" class="block mt-1 w-full maska" data-maska="+## ### ###-####" type="text" name="viber" :value="old('viber')" placeholder="+1234567890" />
                <x-input-error :messages="$errors->get('viber')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="whatsapp" :value="__('WhatsApp')" />
                <x-text-input id="whatsapp" class="block mt-1 w-full maska" data-maska="+## ### ###-####"  type="text" name="whatsapp" :value="old('whatsapp')" placeholder="+1234567890" />
                <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="facebook" :value="__('Facebook')" />
                <x-text-input id="facebook" class="block mt-1 w-full" type="text" pattern="https?://.+" name="facebook" :value="old('facebook')" placeholder="https://facebook.com" />
                <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="instagram" :value="__('Instagram')" />
                <x-text-input id="instagram" class="block mt-1 w-full" type="text" pattern="https?://.+" name="instagram" :value="old('instagram')" placeholder="https://instagram.com" />
                <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="tiktok" :value="__('TikTok')" />
                <x-text-input id="tiktok" class="block mt-1 w-full" type="text" pattern="https?://.+" name="tiktok" :value="old('tiktok')" placeholder="https://tik-tok.com" />
                <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
            </div>


            <!-- Country Code -->
            <div class="mt-4">
                <x-input-label for="country">
                    {{ __('Country') }} <span class="text-red-500">*</span>
                </x-input-label>
                <select id="country_code" name="country_code" class="block mt-1 w-full" required onchange="updateCities(this.value)">
                    <option value="">{{ __('Select a country') }}</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ old('country_code') == $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('country_code')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="city">
                    {{ __('City') }} <span class="text-red-500">*</span>
                </x-input-label>
                <select id="city" name="city" class="block mt-1 w-full" required>
                    <option value="">{{ __('Select a city') }}</option>
                </select>
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>



            <!-- Additional Info -->
            <div class="mt-4">
                <x-input-label for="street_address">
                    {{ __('Street Address') }} <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="street_address" class="block mt-1 w-full" type="text" name="street_address" :value="old('street_address')" required />
                <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="street_address_line_2" :value="__('Street Address Line 2')" />
                <x-text-input id="street_address_line_2" class="block mt-1 w-full" type="text" name="street_address_line_2" :value="old('street_address_line_2')" />
                <x-input-error :messages="$errors->get('street_address_line_2')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="state_province">
                    {{ __('State/Province') }} <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="state_province" class="block mt-1 w-full" type="text" name="state_province" :value="old('state_province')" required />
                <x-input-error :messages="$errors->get('state_province')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="postal_code">
                    {{ __('Postal/Zip Code') }} <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="postal_code" class="block mt-1 w-full maska" data-maska="******" type="number" name="postal_code" :value="old('postal_code')" maxlength="6" required />
                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
            </div>



            <div class="mt-4">
                <x-input-label for="heard_about_us" :value="__('How Did You Hear About Us?')" />
                <select id="heard_about_us" name="heard_about_us" class="block mt-1 w-full">
                    <option value="" selected>{{ __('Select Option') }}</option>
                    <option value="partner_recommendation">{{ __('Partner Recommendation') }}</option>
                    <option value="search">{{ __('Search') }}</option>
                    <option value="social_media">{{ __('Social Media') }}</option>
                    <option value="ad">{{ __('Ad') }}</option>
                    <option value="conference">{{ __('Conference') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
                <x-input-error :messages="$errors->get('heard_about_us')" class="mt-2" />
            </div>
        </div>



        <div id="property-owner-fields" class="hidden">
            <!-- Tax ID -->
            <div class="mt-4">
                <x-input-label for="tax_id" :value="__('Tax ID')" />
                <x-text-input id="tax_id" class="block mt-1 w-full" type="number" name="tax_id" :value="old('tax_id')" placeholder="Enter Tax ID" />
                <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
            </div>

            <!-- IBAN -->
            <div class="mt-4">
                <x-input-label for="iban" :value="__('IBAN')" />
                <x-text-input id="iban" class="block mt-1 w-full maska" data-maska="@@## #### #### #### #### #### #### #" type="text" name="iban" :value="old('iban')" placeholder="Enter IBAN" />
                <x-input-error :messages="$errors->get('iban')" class="mt-2" />
            </div>

            <!-- Beneficiary -->
            <div class="mt-4">
                <x-input-label for="beneficiary" :value="__('Beneficiary')" />
                <x-text-input id="beneficiary" class="block mt-1 w-full" type="text" name="beneficiary" :value="old('beneficiary')" placeholder="Enter Beneficiary Name" />
                <x-input-error :messages="$errors->get('beneficiary')" class="mt-2" />
            </div>

            <!-- Accountant -->
            <div class="mt-4">
                <x-input-label for="accountant_id" :value="__('Accountant')" />
                <select id="accountant_id" class="block mt-1 w-full select-with-search" name="accountant_id">
                    <option value="" selected>{{ __('Select Accountant') }}</option>
                </select>
                <x-input-error :messages="$errors->get('accountant_id')" class="mt-2" />
            </div>

        </div>


        <!-- Agent -->
        <div id="agent-fields" class="hidden">
            <!-- Birthday -->
            <div class="mt-4">
                <x-input-label for="birthday" :value="__('Birthday')" />
                <x-text-input id="birthday" class="block mt-1 w-full" type="date" name="birthday" :value="old('birthday')" />
                <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
            </div>

            <!-- Company Name -->
            <div class="mt-4">
                <x-input-label for="company_name" :value="__('Company Name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <!-- Company Type -->
            <div class="mt-4">
                <x-input-label for="company_type" :value="__('Company Type')" />
                <select id="company_type" name="company_type" class="block mt-1 w-full">
                    <option value="" selected>{{ __('Select Type') }}</option>
                    <option value="management">{{ __('Management') }}</option>
                    <option value="agency">{{ __('Agency') }}</option>
                    <option value="broker">{{ __('Broker') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
                <x-input-error :messages="$errors->get('company_type')" class="mt-2" />
            </div>

            <!-- Role in Company -->
            <div class="mt-4">
                <x-input-label for="role_in_company" :value="__('Role in Company')" />
                <select id="role_in_company" name="role_in_company" class="block mt-1 w-full">
                    <option value="" selected>{{ __('Select Role') }}</option>
                    <option value="owner">{{ __('Owner') }}</option>
                    <option value="co-owner">{{ __('Co-Owner') }}</option>
                    <option value="manager">{{ __('Manager') }}</option>
                    <option value="operator">{{ __('Operator') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
                <x-input-error :messages="$errors->get('role_in_company')" class="mt-2" />
            </div>

            <!-- Website Link -->
            <div class="mt-4">
                <x-input-label for="website_link" :value="__('Website Link')" />
                <x-text-input id="website_link" class="block mt-1 w-full" type="url" name="website_link" pattern="https?://.+" :value="old('website_link')" placeholder="https://your-website.com" />
                <x-input-error :messages="$errors->get('website_link')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="about_agency" :value="__('About Agency')" />
                <textarea id="about_agency" class="block mt-1 w-full" name="about_agency">{{ old('about_agency') }}</textarea>
                <x-input-error :messages="$errors->get('about_agency')" class="mt-2" />
            </div>
        </div>

        <div id="permission-fields" class="hidden">
            <div class="mt-4">
                <x-input-label :value="__('Permissions')" />

                <label class="flex items-center mt-2">
                    <input type="checkbox" name="permissions[rent]" value="1" {{ old('permissions.rent') ? 'checked' : '' }} class="mr-2">
                    <span>{{ __('Rent') }}</span>
                </label>

                <label class="flex items-center mt-2">
                    <input type="checkbox" name="permissions[real_estate]" value="1" {{ old('permissions.real_estate') ? 'checked' : '' }} class="mr-2">
                    <span>{{ __('Real Estate') }}</span>
                </label>

                <label class="flex items-center mt-2">
                    <input type="checkbox" name="permissions[service]" value="1" {{ old('permissions.service') ? 'checked' : '' }} class="mr-2">
                    <span>{{ __('Service') }}</span>
                </label>

                <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
            </div>
        </div>


        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const agentOwnerFields = document.getElementById('common-meta-fields');
            const companyFields = document.getElementById('agent-fields');
            const propertyOwnerFields = document.getElementById('property-owner-fields');
            const permissionFields = document.getElementById('permission-fields')

            const updateVisibility = () => {
                const selectedRoles = Array.from(roleCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                if (selectedRoles.includes('agent') || selectedRoles.includes('property_owner') || selectedRoles.includes('accountant') || selectedRoles.includes('manager')) {
                    agentOwnerFields.classList.remove('hidden');
                } else {
                    agentOwnerFields.classList.add('hidden');
                }

                if (selectedRoles.includes('agent') || selectedRoles.includes('manager')) {
                    companyFields.classList.remove('hidden');
                } else {
                    companyFields.classList.add('hidden');
                }

                if (selectedRoles.includes('agent')) {
                    permissionFields.classList.remove('hidden');
                } else {
                    permissionFields.classList.add('hidden');
                }

                if (selectedRoles.includes('property_owner')) {
                    propertyOwnerFields.classList.remove('hidden');
                } else {
                    propertyOwnerFields.classList.add('hidden');
                }
            };

            roleCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updateVisibility));
            updateVisibility();
        });
    </script>

    <script>
        function updateCities(countryCode) {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">{{ __('Loading cities...') }}</option>';

            if (!countryCode) {
                citySelect.innerHTML = '<option value="">{{ __('Select a country first') }}</option>';
                return;
            }

            fetch(`/api/cities/${countryCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    citySelect.innerHTML = '<option value="">{{ __('Select a city') }}</option>';
                    Object.keys(data).forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    citySelect.innerHTML = '<option value="">{{ __('Error loading cities') }}</option>';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectedCountryCode = document.getElementById('country_code').value;
            if (selectedCountryCode) {
                updateCities(selectedCountryCode);
            }
        });



        $(document).ready(function() {
            $('#accountant_id').select2({
                ajax: {
                    url: '{{ route("search.accountants") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            query: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.name + ' ' + item.last_name + ' (' + item.email + ')'
                                };
                            })
                        };
                    },
                    minimumInputLength: 3,
                },
                placeholder: 'Select Accountant',
                allowClear: true,
            });
        });

    </script>
</x-guest-layout>
