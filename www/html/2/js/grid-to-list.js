$(document).ready(function() {  


$('.sorting-cols').click(function() 
	{  
		$(this).addClass('active');
		$('.sorting-rows').removeClass('active');

		$('.item-cat').removeClass('item-cat2');

		// $('.item-cat2').removeClass('item-cat');

		return false; 

	});


$('.sorting-rows').click(function() 
	{  
		$(this).addClass('active');
		$('.sorting-cols').removeClass('active');

		// $('.item-cat2').addClass('item-cat');

		$('.item-cat').addClass('item-cat2'); 

		return false;

	});


}); 

