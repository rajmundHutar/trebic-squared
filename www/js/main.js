window.addEventListener('load', (e) => {

	document.querySelectorAll('.square').forEach((square) => {
		square.addEventListener("click", (e) => {
			var coord = e.target.dataset.coordinates;

			var guessElement = document.getElementById('guess');
			if (guessElement) {
				guessElement.value = coord;
			}

			var prefillElement = document.getElementById('prefill');
			if (prefillElement) {
				prefillElement.value = coord;
			}

			toggleModal('guess-form');

		}, false);
	});

	var modal = document.getElementById('close-modal');
	if (modal) {
		modal.addEventListener('click', (e) => {
			toggleModal(e.target.parentElement.parentElement.parentElement.id);
		});
	}

	// Get all "navbar-burger" elements
	const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

	// Check if there are any navbar burgers
	if ($navbarBurgers.length > 0) {

		// Add a click event on each of them
		$navbarBurgers.forEach( el => {
			el.addEventListener('click', () => {

				// Get the target from the "data-target" attribute
				const target = el.dataset.target;
				const $target = document.getElementById(target);

				// Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
				el.classList.toggle('is-active');
				$target.classList.toggle('is-active');

			});
		});
	}

	document.querySelectorAll('.toggle').forEach( el => {
		el.addEventListener('click', (e) => {

			// Get the target from the "data-target" attribute
			const target = el.dataset.toggleTarget;
			const $target = document.getElementById(target);

			el.classList.toggle('hidden');
			$target.classList.toggle('hidden');

			e.preventDefault();

		});
	});

});

function toggleModal(id) {
	document.getElementById(id).classList.toggle('is-active');
	document.getElementById('html').classList.toggle('is-clipped');
}
