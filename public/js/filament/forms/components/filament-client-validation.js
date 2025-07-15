document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form?.addEventListener('submit', function (e) {
        let isValid = true;

        // Find all required fields with Filament classes
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach((input) => {
            const container = input.closest('[x-data]');
            const errorDiv = container?.querySelector('[x-show="hasError"]');

            if (!input.value.trim()) {
                isValid = false;

                // Add red border
                container?.classList.add('fi-fo-error');
                input.classList.add('border-danger-600');

                // Show error message
                if (errorDiv) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = 'This field is required.';
                } else {
                    // fallback if no filament error div found
                    const fallback = document.createElement('p');
                    fallback.className = 'text-sm text-danger-600 mt-1';
                    fallback.textContent = 'This field is required.';
                    input.after(fallback);
                }

            } else {
                // Clear errors
                container?.classList.remove('fi-fo-error');
                input.classList.remove('border-danger-600');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });
});
