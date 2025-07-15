// document.addEventListener('DOMContentLoaded', function () {
//     if (navigator.geolocation) {
// 	navigator.geolocation.getCurrentPosition(function (position) {
// 	    const dataLatitude = document.getElementById('data.latitude');
// 	    const dataLongitude = document.getElementById('data.longitude');
// 	    if( dataLatitude && dataLatitude.value == 0 ){
// 		dataLatitude.value = position.coords.latitude;
// 	    }
// 	    if( dataLongitude && dataLongitude.value == 0 ){
// 		dataLongitude.value = position.coords.longitude;
// 	    }
// 	});	    
//     }
// });

const propertyHtmlController = document.getElementsByClassName("property-html-controller");
const urlForSitePresentation = document.getElementById("data.url_for_site_presentation");
if(propertyHtmlController[0] !== undefined && urlForSitePresentation.value && urlForSitePresentation.value !== undefined){
    propertyHtmlController[0].addEventListener('click', function () {
	const propertyID = propertyHtmlController[0].dataset.propertyid;
	fetch('/save-input', {
	    method: 'POST',
	    headers: {
		'Content-Type': 'application/json',
		'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
	    },
	    body: JSON.stringify({ propertyID: propertyID })
	})
	.then(response => response.json())
	.then(data => {
	    urlForSitePresentation.value = data.url;
	})
    });
}



Livewire.on('loading', () => {
    console.log('Livewire is loading...');
});

Livewire.on('loaded', () => {
    console.log('Livewire has finished loading');
});

// Prevent loaders for Deal Type checkboxes
document.addEventListener('DOMContentLoaded', function() {
    function removeLoaders() {
        // Only target checkboxes with data-no-loader attribute
        const dealTypeCheckboxes = document.querySelectorAll('[data-no-loader="true"]');
        dealTypeCheckboxes.forEach(checkbox => {
            // Remove any loading states from the checkbox itself
            checkbox.classList.remove('fi-loading', 'fi-spinner', 'fi-shimmer');
            
            // Remove loading states from parent containers
            const fieldWrapper = checkbox.closest('.fi-fo-field-wrp');
            if (fieldWrapper) {
                fieldWrapper.classList.remove('fi-loading', 'fi-spinner', 'fi-shimmer');
            }
            
            // Remove loading states from checkbox list container
            const checkboxList = checkbox.closest('.fi-fo-checkbox-list');
            if (checkboxList) {
                checkboxList.classList.remove('fi-loading', 'fi-spinner', 'fi-shimmer');
            }
            
            // Remove any loading attributes
            checkbox.removeAttribute('data-loading');
            checkbox.removeAttribute('wire:loading');
            checkbox.removeAttribute('wire:loading.delay');
            checkbox.removeAttribute('wire:loading.inline');
            
            // Remove loading states from the entire fieldset
            const fieldset = checkbox.closest('fieldset');
            if (fieldset) {
                fieldset.classList.remove('fi-loading', 'fi-spinner', 'fi-shimmer');
            }
            
            // Remove loading states from any parent form elements
            const form = checkbox.closest('form');
            if (form) {
                form.classList.remove('fi-loading', 'fi-spinner', 'fi-shimmer');
            }
            
            // More aggressive: Remove any elements with loading-related classes in the entire fieldset
            if (fieldset) {
                const allLoaders = fieldset.querySelectorAll('.fi-loading, .fi-spinner, .fi-shimmer, .fi-loader, .loading, .spinner, .shimmer, [class*="loading"], [class*="spinner"], [class*="shimmer"], [class*="loader"], svg');
                allLoaders.forEach(loader => {
                    loader.style.display = 'none';
                    loader.style.opacity = '0';
                    loader.style.visibility = 'hidden';
                    loader.style.pointerEvents = 'none';
                    loader.remove();
                });
            }
        });
    }
    
    // Initial cleanup
    removeLoaders();
    
    // Prevent Livewire loading states on checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.hasAttribute('data-no-loader')) {
            console.log('DEBUG: Deal Type checkbox changed, removing loaders...');
            // Immediately remove any loading classes that might appear
            setTimeout(removeLoaders, 1);
            setTimeout(removeLoaders, 10);
            setTimeout(removeLoaders, 50);
            setTimeout(removeLoaders, 100);
            setTimeout(removeLoaders, 200);
        }
    });
    
    // Watch for new deal type checkboxes (for dynamic content)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const newCheckboxes = node.querySelectorAll('[data-no-loader="true"]');
                    if (newCheckboxes.length > 0) {
                        setTimeout(removeLoaders, 1);
                    }
                }
                // Debug: Log any loader/spinner elements added near deal type checkboxes
                if (node.nodeType === 1) {
                    // Check if this node or its children are loader/spinner
                    const possibleLoaders = node.querySelectorAll('.fi-loading, .fi-spinner, .fi-shimmer, .fi-loader, [role="status"], svg, .loading, .spinner, .shimmer, [class*="loading"], [class*="spinner"], [class*="shimmer"], [class*="loader"]');
                    if (possibleLoaders.length > 0) {
                        possibleLoaders.forEach(loader => {
                            // Highlight the loader for debugging
                            loader.style.outline = '2px solid red';
                            console.log('DEBUG: Loader element detected:', loader, loader.outerHTML);
                            // Immediately remove it
                            loader.style.display = 'none';
                            loader.style.opacity = '0';
                            loader.style.visibility = 'hidden';
                            loader.remove();
                        });
                    }
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Continuous monitoring to prevent loaders
    setInterval(removeLoaders, 50);
    
    // Additional monitoring for Livewire events
    if (typeof Livewire !== 'undefined') {
        Livewire.on('loading', function() {
            console.log('DEBUG: Livewire loading event detected');
            setTimeout(removeLoaders, 1);
        });
        
        Livewire.on('loaded', function() {
            console.log('DEBUG: Livewire loaded event detected');
            setTimeout(removeLoaders, 1);
        });
    }
    
    // Debug: Log when the script loads
    console.log('DEBUG: Deal Type loader prevention script loaded');
});