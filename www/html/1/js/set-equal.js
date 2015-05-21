function setEqualHeight(items)  
	{  

		var tallestcolumn = 0;  

		items.each(  

			function()  

				{  

					currentHeight = $(this).height();  

					if(currentHeight > tallestcolumn)  

						{  

							tallestcolumn  = currentHeight;  

						}  

				}  

			);  

		items.height(tallestcolumn);  

	}  

// function setEqualWidth(item1)  

// 	{  
// 	 	currentWidth = $(this).width();
// 	 	colwidth  = currentWidth;  
// 	 	$(this, .show-on-mouse).width(colwidth);
// 	}  


$(document).ready(function() {  

	setEqualHeight($(".item-cat")); 
	
	// setEqualWidth($(".item-cat1"));  

}); 