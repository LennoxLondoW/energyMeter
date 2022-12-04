$(function () {
    $("body").on("click", ".help", function () {
        $('[data-help_text]').not($('[data-popover_main]')).each(function () {
            Popx($(this));
        })
        var first = $('[data-popover_main]:first');
        if (first.attr('data-popover_main') !== undefined) {
            first.popover('show');
            $('html,body').animate({
                scrollTop: first.offset().top - 100
            }, 500);
        }
    });
});
var COUNTER = 0;
function Popx(elem) {
    // make use of ID selector 
    var id = 'popover_' + (++COUNTER);
    var next = 'popover_' + (COUNTER + 1);
    var prev = 'popover_' + (COUNTER - 1);
    elem.attr({ 'data-placement': 'bottom', 'data-popover_main': id }).popover({

        html: true,
        trigger: 'click',
        content: function () {
            return `<div style="cursor:pointer; font-size:20px; text-align:right;"  onclick="close_popover('` + id + `')" >&times;</div>` +
                elem.attr('data-help_text') +
                `<br><button type="button"  style="` + (prev == 'popover_0' ? 'display:none;' : '') + ` cursor:pointer; padding:5px; margin:5px; border:solid .5px #fff; border-radius:5px;color:#fff; background:#09c; outline:none; "  onclick="next_popover('` + id + `','` + prev + `')" >Prev</button>` +
                `<button type="button"  style="cursor:pointer; padding:5px; margin:5px; border:solid .5px #fff; border-radius:5px;color:#fff; background:#09c; outline:none; "  onclick="next_popover('` + id + `','` + next + `')" >Next</button>`;
        }
    }).on("click", function () {
        $(this).popover("hide");
    });
}

function close_popover(id) {
    $('[data-popover_main="' + id + '"]').popover('hide');

}

function next_popover(id, id2) {
    $('[data-popover_main="' + id + '"]').popover('hide');
    var next_elem = $('[data-popover_main="' + id2 + '"]:first');
    if (next_elem.attr('data-popover_main') !== undefined) {
        next_elem.popover('show');
        $('html,body').animate({
            scrollTop: next_elem.offset().top - 100
        }, 500);
    }

}
