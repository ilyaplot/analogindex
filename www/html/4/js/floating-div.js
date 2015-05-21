$(document).ready(function() {  
	if (screen.width > '992') 
	{
		(function($) 
			{
				$.lockfixed("#floatingToolbar",{offset: {top: 0, bottom: 10}});
			})(jQuery);

		(function($) 
			{
				$.lockfixed("#floatingToolbar-right",{offset: {top: 0, bottom: 10}});
			})(jQuery);
	}
}); 