$(document).ready(function(){

// Init
    controlAccordion();
    $('.video').hover( hoverVideo, hideVideo );

// Set Event Handler
    // event-handler, if clicked on single video in /videos/show
    // $(document).on('click', '.single_video', function(){
    //     console.log('video_id: ' + this.id);
    //     console.log(this)
    //     toggleFullscreen();
    // })

    // event-handler vor displaying Actions container if hovering over it
    $(document).on('click', '.actions', function(e){
        e.stopPropagation();
        $(this).children('div').toggleClass('d-flex');
    });

    // event-handler for making the video clickable in the video overview
    $(document).on('click', 'div[data-href]', function(){
        window.location = $(this).data('href');
    });

    // this checks if in the /dashboard an input was changed
    $(document).on('focusout', '.input_change', function(e){
        if (!gotChanged(this)) {
            return false;
        }
        this.parentNode.submit();
    });

    // this checks if in the /videos/index the select for choosing the category was changed
    $(document).on('change', '#categoryindex', function(){
        this.parentNode.submit();
    })

// Functions
    // make videos full screen mode or exit, if necessary
    // function toggleFullscreen(e) {
    //     let test = e.querySelector("video");
    //     console.log(test)
    //     if (!document.fullscreenElement) {
    //         test.requestFullscreen().catch(err => {
    //             alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
    //         });
    //     } else {
    //         document.exitFullscreen();
    //     }
    // }

    // checks if something was changed inside an input field
    function gotChanged(element){
        const initialValue = element.getAttribute('value');
        return element.value !== initialValue;
    }

    // get the current URL and check if there is a fragment for controlling the accordions
    function controlAccordion(){
        let accordionTrigger = window.location.hash

        if (accordionTrigger) {
            $(accordionTrigger).collapse('toggle');
        } else {
            $('#user').collapse('toggle');
        }
    }

    // make videos play when hovered over them
    function hoverVideo(e) {
        $('video', this).get(0).play();
    }
    function hideVideo(e) {
        $('video', this).get(0).pause();
    }

})//end of $(document).ready(function(){});
