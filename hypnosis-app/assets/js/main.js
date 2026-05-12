document.addEventListener('DOMContentLoaded', function () {
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('/pwa/service-worker.js').catch(function () {
			// Silent fallback for unsupported local setups.
		});
	}

	var suicidalField = document.querySelector('[name="suicidal_thoughts"]');
	var warningBox = document.getElementById('safetyWarning');

	function toggleSafetyWarning() {
		if (!suicidalField || !warningBox) {
			return;
		}
		warningBox.classList.toggle('d-none', suicidalField.value !== 'yes');
	}

	if (suicidalField && warningBox) {
		suicidalField.addEventListener('change', toggleSafetyWarning);
		toggleSafetyWarning();
	}
});

