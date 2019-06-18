
function _valChange(selector, next, past='') {
    $({ Counter: (past=='') ? selector.html() : past }).animate(
    {   Counter: next },
    {
            duration: 1000,
            easing: 'swing',
            queue: false,
            step: function() {selector.text(Math.ceil(this.Counter));},
            complete: function() {selector.text(next);},
    });
}

$.fn.isInViewport = function(element) {
    if(!$(this).is(':visible')) return false;
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