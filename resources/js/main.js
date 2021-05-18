$(document).ready(function(){

// Init
    controlAccordion();

    //### Videos ###
    $('.video').hover( hoverVideo, hideVideo );

    if ($('.progress_index')){
        $('.progress_index').each(function(i, obj) {
            let progressIndex = parseInt($(this).data('index'));
            drawStars(progressIndex, obj);
        });
    }

    $('#tags').select2({
        tags: true,
        tokenSeparators: [",", " "],
    });

    // if localStorage toolboxOpen is set you want to display the toolbox open. Only the user can
    // close the toolbox, once it was opened
    if (localStorage.getItem('toolboxOpen')){
        $('#toolbox').collapse('toggle');
    }

// Set Event Handler

    //### Misc ###
    // event-handler to fetch Delete click and introduce an alert before deleting any element
    $(document).on('click', 'button:contains("Delete")', function(e){
        e.preventDefault();
        if (getConfirmation()) {
            $(this).parent().submit();
        }
    });

    //### Dashboard ###
    // Necessary for table redrawing on Dashboard view
    $(window).bind("resize",function(){
        tackleClasses($(this));
    });

    // this checks if in the /dashboard an input was changed
    $(document).on('focusout', '.input_change', function(){
        if (!gotChanged(this)) {
            return false;
        }
        this.parentNode.submit();
    });

    // ### Videos ###
    //Draw stars for video rating
    $('.voting_stars').mouseover(function() {
        let progressIndexElement = $(this).siblings('.progress_index');
        //Fetch the index data attribute from the element you are hovering over
        let currentIndex = parseInt($(this).data('index'));

        // console.log(ratedIndexElement)
        // console.log(currentIndex)
        clearStarColor(progressIndexElement);
        drawStars(currentIndex, progressIndexElement);
    });

    $('.voting_stars').mouseleave(function() {
        let progressIndexElement = $(this).siblings('.progress_index');
        //Fetch the index from the associated .progress_index element
        let progressIndex = parseInt(progressIndexElement.data('index'));

        clearStarColor(progressIndexElement);
        if (progressIndex){
            drawStars(progressIndex, progressIndexElement);
        }
    });

    //Save selected progress_index and get the videoId for saving it to db
    $('.voting_stars').on('click', function(e){
        e.stopPropagation();
        let progressIndex = parseInt($(this).data('index'));
        let videoId = $(this).closest('.card').find('video').attr('id').replace(/[^0-9]/g,'');
        // console.log('ratedIndex: ' + ratedIndex)
        // console.log('video_id: ' + videoId)
        saveToDB(videoId, progressIndex);
    });

    // event-handler for collapsing toolbox in video.index
    $(document).on('click', '#toolboxtoggler', function(){
        $('#toolbox').collapse('toggle');
    });

    $('#toolbox').on('shown.bs.collapse', function(){
        localStorage.setItem('toolboxOpen', '1');
    });

    $('#toolbox').on('hide.bs.collapse', function(){
        localStorage.removeItem(('toolboxOpen'));
    });

    // event-handler vor opening actions container if clicking on it
    $(document).on('click', '.actions', function(e){
        e.stopPropagation();
        $(this).children('div').toggleClass('d-flex');
    });

    // event-handler for making the video clickable at /videos/index
    $(document).on('click', 'div[data-href]', function(){
        window.location = $(this).data('href');
    });

    // this checks if in the /videos/index the select for choosing the category was changed
    $(document).on('change', '#categoryindex', function(){
        this.closest('form').submit();
    });

    // this checks if in the /videos/index the select for choosing the progress was changed
    $(document).on('change', '#progress_index', function(){
        this.closest('form').submit();
    });

    // Clears the search field on Video overview
    $(document).on('click', '#clear_search', function(){
        clearElementValue('search');
    });

    // Resets the search field on Video overview
    $(document).on('click', '#reset_video_search', function(){
        window.location = '/admin/videos?resetSearch=yes';
    });

// Functions
    //### Videos ###
    // Clear searches
    function clearElementValue(element){
        document.getElementById(element).value = '';
    }

    // Draw stars in /videos/show and /videos/index
    function drawStars(index, element){
        // console.log('in drawStars')
        for (let i=0; i < index; i++) {
            // console.log($(element).siblings('.voting_stars').eq(i))
            $(element).siblings('.voting_stars').eq(i).removeClass('text-secondary').addClass('text-warning');
        }
    }

    // clears color of stars in /videos/show
    function clearStarColor(element){
        // console.log(element.siblings('.voting_stars'))
        element.siblings('.voting_stars').removeClass('text-warning').addClass('text-secondary');
    }

    //### Ajax stuff ###
    // Init Ajax with setting CSRF Token
    function setCSRF(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    // Save the actual progress vote from video to the database
    function saveToDB(videoId, progressIndex){
        setCSRF();
        $.ajax({
            url: "/admin/videos/rate-video",
            method: "POST",
            dataType: "text",
            data: {
                videoId: videoId,
                progressIndex: progressIndex
            }, success: function(r){
                console.log(r)
                $('#video_'+videoId).closest('.card').find('.progress_index').data('index', progressIndex);
            }, error: function (error){
                console.log(error);
            }
        });
    }

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
    //   function navigationType() {
    //
    //     var result;
    //     var p;
    //
    //     if (window.performance.navigation) {
    //         result = window.performance.navigation;
    //         if (result == 255) {
    //             result = 4
    //         } // 4 is my invention!
    //     }
    //
    //     if (window.performance.getEntriesByType("navigation")) {
    //         p = window.performance.getEntriesByType("navigation")[0].type;
    //
    //         if (p == 'navigate') {
    //             result = 0
    //         }
    //         if (p == 'reload') {
    //             result = 1
    //         }
    //         if (p == 'back_forward') {
    //             result = 2
    //         }
    //         if (p == 'prerender') {
    //             result = 3
    //         } //3 is my invention!
    //     }
    //     return result;
    // }

    // checks if something was changed inside an input field
    function gotChanged(element){
        const initialValue = element.getAttribute('value');
        return element.value !== initialValue;
    }

    // get the current URL and check if there is a fragment for controlling the accordions
    // redo that and use localStorage for saving the state of the accordion
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
