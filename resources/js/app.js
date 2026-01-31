import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Manhwa reader: compute crop height per-image so top-X% is consistent
(function(){
	function updateCropForImage(img, cropRatio){
		const parent = img.closest('.page-image-crop');
		if(!parent) return;
		const renderedWidth = img.clientWidth || img.width;
		const naturalWidth = img.naturalWidth || renderedWidth;
		const naturalHeight = img.naturalHeight || (renderedWidth * 1.5);
		const renderedHeight = (renderedWidth / naturalWidth) * naturalHeight;
		const visibleHeight = renderedHeight * cropRatio;
		parent.style.height = visibleHeight + 'px';
	}

	function updateAll(){
		const container = document.querySelector('.pages-container');
		if(!container) return;
		const cssRatio = getComputedStyle(container).getPropertyValue('--image-crop-ratio').trim();
		const attrRatio = parseFloat(container.getAttribute('data-image-crop-ratio'));
		const cropRatio = (!isNaN(attrRatio) && attrRatio > 0) ? attrRatio : (parseFloat(cssRatio) || 0.5);

		document.querySelectorAll('.page-image-crop img').forEach(img => {
			if(img.complete){
				updateCropForImage(img, cropRatio);
			} else {
				img.addEventListener('load', function onload(){
					img.removeEventListener('load', onload);
					updateCropForImage(img, cropRatio);
				});
			}
		});
	}

	window.addEventListener('resize', () => requestAnimationFrame(updateAll));
	document.addEventListener('DOMContentLoaded', updateAll);
	// In case this file is loaded after DOMContentLoaded
	setTimeout(updateAll, 100);
})();
