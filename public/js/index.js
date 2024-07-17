document.addEventListener('DOMContentLoaded', (event) => {
    if (document.querySelector('.splide')) {
        if (document.querySelector('.splide01')) {
            var splide1 = new Splide('.splide01', {
                arrows: false,
                snap: true,
                perPage: 3,
                focus: 0,
                omitEnd: true
            })
            splide1.mount()
        }
        if (document.querySelector('.splide02')) {
            var splide2 = new Splide('.splide02', {
                gap: '1em',
                perPage: 2,
                focus: 0
            })
            splide2.mount()
        }
    }
});