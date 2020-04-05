let isDragging = false;
let wasMoved = false;
let zoom = 1;
let start = {};
let beginPosition = {};
let newPosition = {};
let game;

function repeatOften() {
	if (game) {
		game.style.left = newPosition.x + 'px';
		game.style.top = newPosition.y + 'px';
	}
	requestAnimationFrame(repeatOften);
}
requestAnimationFrame(repeatOften);

function down(e) {

	start.x = e.clientX || e.touches[0].clientX;
	start.y = e.clientY || e.touches[0].clientY;

	isDragging = true;
	wasMoved = false;

	beginPosition.x = parseInt(game.style.left);
	beginPosition.y = parseInt(game.style.top);

	if (!e.touches){
		e.preventDefault();
	}

}

function up(e) {

	isDragging = false;

	let x = e.clientX || e.changedTouches[0].clientX
	let y = e.clientY || e.changedTouches[0].clientY

	if (!e.touches && Math.abs(x - start.x) > 5 || Math.abs(y - start.y) > 5) {
		wasMoved = true;
	}

}

function leave(e) {
	isDragging = false;
	e.preventDefault();
}

function move(e) {
	if (isDragging) {
		let x = e.clientX || e.changedTouches[0].clientX;
		let newX = beginPosition.x + x - start.x;
		newX = Math.min(newX, 0);
		newX = Math.max(newX, -(zoom - 1) * document.documentElement.clientWidth);

		let y = e.clientY || e.changedTouches[0].clientY;
		let newY = beginPosition.y + y - start.y;
		newY = Math.min(newY, 0);
		newY = Math.max(newY, -(zoom - 1) * 0.64 * document.documentElement.clientWidth);

		newPosition.x = newX;
		newPosition.y = newY;
	}

}

window.addEventListener('load', (e) => {

	game = document.getElementById('game');

	game.style.top = '0px';
	game.style.left = '0px';

	game.addEventListener('mousedown', down);
	game.addEventListener('touchstart', down);

	game.addEventListener('mouseup', up);
	game.addEventListener('touchend', up);

	game.addEventListener('mouseleave', leave);
	game.addEventListener('touchcancel', leave);

	game.addEventListener('mousemove', move);
	game.addEventListener('touchmove', move);

	document.querySelectorAll('.zoom').forEach((zoomElement) => {
		zoomElement.addEventListener("click", (e) => {
			zoom = parseInt(e.target.dataset.zoom);
			game.style.width = (100 * zoom) + '%';
			game.style.height = (63 * zoom) + 'vw';
			newPosition.x = 0;
			newPosition.y = 0;
		});
	});

	document.querySelectorAll('.square').forEach((square) => {
		square.addEventListener("click", (e) => {

			if (wasMoved) {
				return;
			}

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

	/**
	 * Toggler
	 */
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
	let ele = document.getElementById(id);
	if (ele) {
		ele.classList.toggle('is-active');
		document.getElementById('html').classList.toggle('is-clipped');
	}
}
