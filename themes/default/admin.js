document.addEventListener('click',function(event) {
    if (event.target.matches('.mr-layouts')) {
        event.target.addEventListener('change',function(event) {
            if(event.target.value != 'Custom') {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".mr-customoptions"));
            }
            if(event.target.value == 'Custom') {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".mr-customoptions"));
            }
            if(event.target.value != 'Collapsible' && event.target.value != 'Accordion' && event.target.value != 'Slider' && event.target.value != 'Tabs' && event.target.value != 'Mosaic') {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".perlineov"));
            } else {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".perlineov"));
            }
            if(event.target.value != 'Slider' && event.target.value != 'Menu') {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".perpageov"));
            } else {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".perpageov"));
            }
            if(event.target.value == 'Tabs' || event.target.value == 'Mosaic') {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".tabs-paginationov"));
            } else {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".tabs-paginationov"));
            }
            if(event.target.value == 'Slider') {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".slider-optionov"));
            } else {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".slider-optionov"));
            }
            if(event.target.value != 'Menu') {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".menu-optionov"));
            } else {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".menu-optionov"));
            }
            if(event.target.value == 'Tabs') {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".tabs-optionov"));
            } else {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".tabs-optionov"));
            }
            if(event.target.value == 'Mosaic') {
                mrSlideDown(event.target.closest('.mr-admin').querySelector(".mosaic-optionov"));
            } else {
                mrSlideUp(event.target.closest('.mr-admin').querySelector(".mosaic-optionov"));
            }
        });
    }
});