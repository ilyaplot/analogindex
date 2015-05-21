$(document).ready(function(){

// Show full product item-card on event MOUSEOVER



/* Задействовано 8 блоков, каждый реагирует на наведение мыши.

Выставляем блоку категории товаров тень, рамку и наивысший слой видимости, при наведении мыши 

+ абсолютное позиционирование. Так как позиционируемый блок уже находиться в контейнере, то смещения нет. 

Описаине категории товаров получает свойство display: block, и теряет его, когда удбираем указатель мыши. */

	$('.item-cat').mouseover(function(){

		$(this).css({ 'box-shadow': '7px 7px 1px 0px #cdd3d9', 'border': '3px solid #b1cfed', 'z-index' : '99999', 'background-color' : '#ccc'});
		
		$(this).children('.show-on-mouse').css({'display' : 'block', 'color' : 'red'});

	});


	$('.item-cat').mouseout(function(){

		$(this).css({'position' : 'relative', 'box-shadow': 'none', 'border': '3px solid transparent', 'z-index' : '1', 'background-color' : 'transparent'});

		$(this).children('.show-on-mouse').css({'display' : 'none'});

	});



});





