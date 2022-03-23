require([
	'jquery',
	'slick',
	'domReady!'
	], function($){

	$(window).on('load', function() {
	     
	   	//carrouseles de productos en el home
	   	$('.carrousel-productos ol.product-items').slick({
	   		dots: false,
	   		arrows: true,
	   		infinite: false,
	   		speed: 300,
	   		autoplay: false,
	   		slidesToShow: 5,
	   		slidesToScroll: 1,
	   		responsive: [
	   			{
	   				breakpoint: 768,
	   				settings: {
	   					slidesToShow: 2,
	   					slidesToScroll: 1,
	     					arrows: true,
	   					dots: false
	   				}
	   			}
	       	]
	   	});

	   	//slider de marcas en el home
	   	$('.marcas-block').slick({
	   		dots: false,
	   		arrows: true,
	   		infinite: false,
	   		speed: 300,
	   		autoplay: false,
	   		slidesToShow: 7,
	   		slidesToScroll: 1
	   	});

      	$('.carousel-releases, .carousel-category').slick({
      		dots: false,
      		arrows: true,
      		infinite: false,
      		speed: 300,
      		autoplay: false,
      		slidesToShow: 5,
      		slidesToScroll: 1,
      		responsive: [
   	        {
   	          breakpoint: 1025,
   	          settings: {
   	            slidesToShow: 4
   	          }
   	        },
   	        {
   	          breakpoint: 769,
   	          settings: {
   	            slidesToShow: 3
   	          }
   	        },
   	        {
   	          breakpoint: 666,
   	          settings: {
   	            slidesToShow: 2
   	          }
   	        }
   	    ]
      	});

	});

});
