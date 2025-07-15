<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Only validate forms marked with data-needs-validation
        const requiredInputs = document.querySelectorAll('[data-required="true"]');
        const requiredInputsBasic = document.querySelectorAll('[data-required-basic="true"]');

        console.log('vvvvv', requiredInputsBasic);
        // form[data-needs-validation] 

        const showError = (input) => {
            const wrapper = input.closest('.fi-input-wrp');
            if (!wrapper) return;
            // Add logical class, even if it won't enforce the style
            wrapper.classList.add('fi-invalid');

            // Apply the ring-danger-600 style manually with !important
            wrapper.style.setProperty('box-shadow', '0 0 0 1px rgb(233, 68, 68)', 'important'); // Tailwind's red-600
            wrapper.style.setProperty('border-color', 'rgb(233, 68, 68)', 'important');

            // Add error message if not already present
            if (!wrapper.nextElementSibling || !wrapper.nextElementSibling.dataset.validationError) {
                const error = document.createElement('p');
                error.className = 'fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 v';
                error.dataset.validationError = input.name || '';
                error.textContent = `The ${input.name || 'field'} field is required.`;

                let message = `The ${input.name || 'field'} field is required.`;
                const value = input.value;

                if (value !== '' && input.dataset.type === 'number' && parseFloat(value) <= 0) {
                    message = `The ${input.name || 'field'} must be greater than 0.`;
                }

                error.textContent = message;

                wrapper.insertAdjacentElement('afterend', error);
                // console.log('errorerrorerror', wrapper);
            } else {
                const errorMsg = wrapper.parentElement?.querySelector('[data-validation-error]');
                if (errorMsg) {
                    errorMsg.style.display = 'block';
                }
            }
            iconvalidation();
        };

        const clearError = (input) => {
            const wrapper = input.closest('.fi-input-wrp');
            if (!wrapper) return;

            wrapper.classList.remove('fi-invalid');
            wrapper.style.removeProperty('box-shadow');
            wrapper.style.removeProperty('border-color');

            const next = wrapper.nextElementSibling;
            if (next?.dataset.validationError === input.name) {
                next.remove();
            }
        };

        requiredInputs.forEach(input => {
            if (input.dataset.blurAttached === 'true') return;

            input.addEventListener('blur', () => {
                clearError(input);

                let isValid = true;
                const value = input.value;

                if (value === '') {
                    isValid = false;
                } else if (input.dataset.type === 'number' && parseFloat(value) <= 0) {
                    isValid = false;
                } else if (input.tagName === 'SELECT' && input.dataset.minValue) {
                    const min = parseInt(input.dataset.minValue);
                    if (parseInt(value) < min) {
                        isValid = false;
                    }
                }

                if (!isValid) {
                    showError(input);
                } else {
                    clearError(input);
                }
            });

            input.dataset.blurAttached = 'true';
        });

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                let hasError = false;

                requiredInputs.forEach(input => {
                    clearError(input);

                    let isValid = true;
                    const value = input.value;

                    if (value === '') {
                        isValid = false;
                    } else if (input.dataset.type === 'number' && parseFloat(value) <= 0) {
                        isValid = false;
                    } else if (input.tagName === 'SELECT' && input.dataset.minValue) {
                        const min = parseInt(input.dataset.minValue);
                        if (parseInt(value) < min) {
                            isValid = false;
                        }
                    }

                    if (!isValid) {
                        showError(input);
                        hasError = true;
                    }
                });

                if (hasError) {
                    e.preventDefault(); // prevent submission if errors exist
                }
            });
        }

        // console.log("✅ Client-side blur + submit validation attached.");
    });
</script>


<script>
    function clearFilamentValidationOnFocus(event) {
        // console.log("Clearing validation on focus for:", event.target);
        const inputWrapper = event.target.closest('.fi-input-wrp');
        if (inputWrapper) {
            inputWrapper.classList.remove('fi-invalid', 'ring-danger-600', 'dark:ring-danger-500');

            inputWrapper.classList.remove('fi-invalid');
            inputWrapper.style.removeProperty('box-shadow');
            inputWrapper.style.removeProperty('border-color');

            // Remove tailwind danger focus ring classes
            inputWrapper.className = inputWrapper.className.replace(/focus-within:ring-danger-[0-9]+/g, '');

            // Hide the validation message
            const errorMsg = inputWrapper.parentElement?.querySelector('[data-validation-error]');
            if (errorMsg) {
                errorMsg.style.display = 'none';
            }
        }
    }
</script>

<!-- icon validation -->
<script>
    function iconvalidation1(error = true) {
        const activeTab = document.querySelector('button[role="tab"].fi-tabs-item-active');
        let tabName = "";
        if (activeTab) {
            tabName = activeTab.textContent.trim();
            // console.log('Active tab name:', tabName);
        }

        const tabButtons = document.querySelectorAll('button[role="tab"]');
        tabButtons.forEach(button => {
            if (button.textContent.includes(tabName)) {
                const iconSvg = button.querySelector('svg');
                if (iconSvg) {
                    iconSvg.setAttribute('viewBox', '0 0 16 16');
                    iconSvg.setAttribute('width', '16');
                    iconSvg.setAttribute('height', '16');
                    iconSvg.removeAttribute('stroke');
                    iconSvg.removeAttribute('stroke-width');
                    iconSvg.innerHTML = error ?
                        `<path fill="#FBBF24" d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"></path>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"></path>
                        ` // Error icon
                        :
                        `<path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" fill-rule="evenodd"></path>`; // Check icon

                    // Optional: color
                    iconSvg.setAttribute('fill', error ? '#FBBF24' : 'green');
                }
            }
        });
    }

    function iconvalidation() {
        const activeTab = document.querySelector('button[role="tab"].fi-tabs-item-active');
        const allTabs = document.querySelectorAll('button[role="tab"]');
        const tabPanels = document.querySelectorAll('[role="tabpanel"]');
        if (!activeTab) return;

        // Get index of active tab
        let tabIndex = -1;
        allTabs.forEach((tab, index) => {
            if (tab === activeTab) tabIndex = index;
        });

        if (tabIndex === -1 || !tabPanels[tabIndex]) return;

        const currentPanel = tabPanels[tabIndex];
        const requiredInputs = currentPanel.querySelectorAll('[data-required="true"]');

        let hasError = false;

        requiredInputs.forEach(input => {
            const value = input.value;

            let isValid = true;
            if (value === '') {
                isValid = false;
            } else if (input.dataset.type === 'number' && parseFloat(value) <= 0) {
                isValid = false;
            } else if (input.tagName === 'SELECT' && input.dataset.minValue) {
                const min = parseInt(input.dataset.minValue);
                if (parseInt(value) < min) {
                    isValid = false;
                }
            }

            if (!isValid) {
                hasError = true;
            }
        });

        const iconSvg = activeTab.querySelector('svg');
        if (iconSvg) {
            iconSvg.setAttribute('viewBox', '0 0 16 16');
            iconSvg.setAttribute('width', '16');
            iconSvg.setAttribute('height', '16');
            iconSvg.removeAttribute('stroke');
            iconSvg.removeAttribute('stroke-width');
            iconSvg.innerHTML = hasError ?
                `<path fill="#FBBF24" d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"></path>
               <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"></path>` :
                `<path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" fill-rule="evenodd"></path>`;

            iconSvg.setAttribute('fill', hasError ? '#FBBF24' : 'green');
        }
    }
</script>