define([ 
	"jquery", 
	"Magento_Ui/js/modal/modal", 
	"mage/cookies"
], function ($, modal) { 
	"use strict"; 
	
	// Crear una función de inicialización 
	function initializeModalPopup() { 
		// Establecer constantes 
		const COOKIE_MODAL = 'cookiemodal'; 
		const MODAL_ID = 'modal'; 
		const $modalElement = $('.row-popup-home').first(); 
		
		// Configurar id para el elemento modal 
		$modalElement.attr('id', MODAL_ID); 
		
		// Si no existe la cookie 'cookiemodal' y no estamos en la url 'simulador' 
		if (! $.cookie(COOKIE_MODAL) && !window.location.href.includes("simulador")) { 
			// Establecer la cookie 'cookiemodal' 
			$.cookie(COOKIE_MODAL, 'ok'); 
			
			// Opciones para el modal 
			const options = { 
				type: 'popup', 
				responsive: true, 
				innerScroll: true,
				modalClass: 'popup-info-home', 
				buttons: [{ 
					text: $.mage.__('Ok'), 
					class: '', 
					click: function () { 
						this.closeModal(); 
					} 
				}] 
			}; 
			
			// Crear el modal y abrirlo 
			if(jQuery('#modal').length){
				const popup = modal(options, $modalElement); 
				$modalElement.modal('openModal');
				
				// Cerrar el modal al hacer click fuera de él 
				$('.modal-popup').on("click", function(e) {
					const $containerPopup = $('.modal-inner-wrap'); 
					
					if (!$containerPopup.is(e.target) && $containerPopup.has(e.target).length === 0) { 
						$modalElement.modal('closeModal'); 
					} 
				});
			}
		} 
	} 
	
	// Ejecutar la función de inicialización cuando el DOM esté listo 
	$(document).ready(function() { 
		initializeModalPopup(); 
	}); 
});