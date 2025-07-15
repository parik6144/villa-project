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
        });
    }
    
    // Initial cleanup
    removeLoaders();
    
    // Prevent Livewire loading states on checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.hasAttribute('data-no-loader')) {
            // Immediately remove any loading classes that might appear
            setTimeout(removeLoaders, 1);
            setTimeout(removeLoaders, 10);
            setTimeout(removeLoaders, 50);
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
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Continuous monitoring to prevent loaders
    setInterval(removeLoaders, 100);
});