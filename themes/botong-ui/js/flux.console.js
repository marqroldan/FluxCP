
$.fn.isInViewport = function(element) {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();
    var viewportTop = $(element).scrollTop() + 76;
    var viewportBottom = viewportTop + $(element).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};

$(document).ready(function() {
let text = `
 ______ _     _    ___   _______ _____  
|  ____| |   | |  | \\ \\ / / ____|  __ \\ 
| |__  | |   | |  | |\\ V / |    | |__) |
|  __| | |   | |  | | > <| |    |  ___/ 
| |    | |___| |__| |/ . \\ |____| |     
|_|    |______\\____//_/ \\_\\_____|_|     
                                        
-----------------------------------------------
Botong-ui Theme by Marq Roldan (Hyvraine)
-----------------------------------------------
`;
console.log(text+$('.fluxDetails').html());
});