$(document).ready(function(){
    $("#filter .list").on("click", "span.remove", function(){
        var elem = $(this).parent("li");
        var container = elem.parent('ul');
        // По окончании эффекта удаляем элемент
        elem.fadeOut(200, function(){
            if (container.find('li').length == 1) // Удаляем последний элемент списка
                container.append(renderListItem(container.attr("data-title"), 0, 
                    container.attr("data-name")));
            // Отображаем ранее скрытый элемент списка
            container.parent("div.filter-select").find("option[value='"+elem.find('input').val()+"']").attr("selected", false).show();
            elem.remove();
        });
        //elem.find("option:selected").addClass("hidden");
    });
    
    $("#filter .filter-select > select").change(function(){
        var elem = $(this);
        var container = elem.parent("div.filter-select").children('.list');
        if (elem.val() < 1)
        {
            elem.find("option").show();
            /** @todo добавить перевод **/
            container.html(renderListItem(container.attr("data-title"), 0, container.attr("data-name")));
        } else if (!container.find("input[value="+elem.val()+"]").length) {
            elem.find("option:selected").hide();
            container.find("li > input[value=0]").parent('li').remove();
            container.append(renderListItem(
                elem.find("option:selected").text(),
                elem.val(),
                container.attr("data-name")
            ));
        }
    });

    
    // Перехват отправки формы, очистка служебных полей
    $("#filter form").submit(function(){
        $(this).find("input[value=0]").remove();
        return true;
    });
    
    function renderListItem(title, value, name)
    {
        var item =  '<li> '+title;
        item += ' <input type="hidden" name="'+name+'" value="'+value+'" />';
        if (value !== 0)
            item += '<span class="remove" title="Удалить">x</span>';
        
        item += '</li>';
        return item;
    }
});