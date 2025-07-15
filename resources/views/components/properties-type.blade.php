<div data-field-wrapper="" class="fi-fo-field-wrp" x-show="propertyClass && propertyClass !== 'other'">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-3 justify-between ">
            <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.property_class">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Property Type<sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                </span>
            </label>
        </div>
        <div class="grid auto-cols-fr gap-y-2">
            <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-select">
                <div class="fi-input-wrp-input min-w-0 flex-1">
                    <select x-model="property_type_id" data-required="true" class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&amp;_optgroup]:bg-white [&amp;_optgroup]:dark:bg-gray-900 [&amp;_option]:bg-white [&amp;_option]:dark:bg-gray-900 ps-3" id="data.property_type" name="property type" :disabled="!typeOptions.length">
                        <option value="">Select an option</option>
                        <template x-for="option in typeOptions" :key="option.id">
                            <option :value="option.id" x-text="option.name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-field-wrapper="" class="fi-fo-field-wrp" x-show="propertyClass && propertyClass === 'other'">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-3 justify-between ">
            <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.title">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Property Type<sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                </span>
            </label>
        </div>
        <div class="grid auto-cols-fr gap-y-2">
            <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">
                <div class="fi-input-wrp-input min-w-0 flex-1">
                    <input x-model="property_type_custom" data-required="true" class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3" id="data.title" type="text" name="property type">
                </div>
            </div>
        </div>
    </div>
</div>

