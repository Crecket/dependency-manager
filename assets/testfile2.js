if (typeof ScrollReveal !== "undefined") {
    window.sr = ScrollReveal({reset: true});
    sr.reveal('.scrollReveal', {
        duration: 350,
        distance: '30px',
        easing: 'ease'
    });
}

$('.slimScroll').slimScroll({
    position: 'right',
    height: '190px',
    width: 'auto'
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});


