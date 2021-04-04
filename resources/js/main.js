$(document).ready(function(){

// Init
    controlAccordion();
    $('.video').hover( hoverVideo, hideVideo );

// Save session to localStorage so the Video dropdown can be prefilled everytime you come back to _/videos/index
//     test = navigationType()
//     console.log('navigationType' + test)
//     console.log('oldCategory: ' + localStorage.getItem('oldCategory'))
//     console.log('categoryIndex: ' + $('#categoryindex').val());

    var oldCategory = localStorage.getItem('oldCategory');
    var categoryElement = $('#categoryindex');

    if (oldCategory && categoryElement.length && oldCategory != categoryElement.val()) {
        $('#categoryindex').val(localStorage.getItem('oldCategory')).parent().submit();
    }

// Set Event Handler
    // Necessary for table redrawing on Dashboard view
    $(window).bind("resize",function(){
        console.log($(window).width());
        tackleClasses($(this));
    });

    // event-handler vor displaying Actions container if hovering over it
    $(document).on('click', '.actions', function(e){
        e.stopPropagation();
        $(this).children('div').toggleClass('d-flex');
    });

    // event-handler to fetch Delete click and introduce an alert before deleting the video
    $(document).on('click', 'button:contains("Delete")', function(e){
        e.preventDefault();
        if (getConfirmation()) {
            $(this).parent().submit();
        }
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
    $(document).on('change', '#categoryindex', function(e){
        localStorage.setItem('oldCategory',$(this, "option:selected").val());
        this.parentNode.submit();
    })

// Functions
    // removes table related bootstrap classes if screen gets to small
    function tackleClasses(window){
        if($(window).width() <760){
            $('.table_card').removeClass('table-sm')
        }
        else{
            $('.table_card').addClass('table-sm')
        }
    }

    // checks for confirmation, before deleting a video
    function getConfirmation() {
        var retVal = confirm("Do you really want to delete this element?");
        return retVal === true;
    }

    // analyse if the page was refreshed by a event as listed below
      function navigationType() {

        var result;
        var p;

        if (window.performance.navigation) {
            result = window.performance.navigation;
            if (result == 255) {
                result = 4
            } // 4 is my invention!
        }

        if (window.performance.getEntriesByType("navigation")) {
            p = window.performance.getEntriesByType("navigation")[0].type;

            if (p == 'navigate') {
                result = 0
            }
            if (p == 'reload') {
                result = 1
            }
            if (p == 'back_forward') {
                result = 2
            }
            if (p == 'prerender') {
                result = 3
            } //3 is my invention!
        }
        return result;
    }

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
