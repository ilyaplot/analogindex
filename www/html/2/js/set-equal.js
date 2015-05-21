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

function setEqualHeight2(items)  
	{  

		currentH = $(this).height();
		$('.button_prev, .button_next').height(currentH);

	}    

// function setEqualWidth(item1)  

// 	{  
// 	 	currentWidth = $(this).width();
// 	 	colwidth  = currentWidth;  
// 	 	$(this, .show-on-mouse).width(colwidth);
// 	}  


$(document).ready(function() {  

	setEqualHeight($(".item-cat")); 
	setEqualHeight2($(".nav-arrow")); 
	
	// setEqualWidth($(".item-cat1"));  

}); 