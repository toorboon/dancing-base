$(document).ready(function(){

// Init
    //### Dashboard ###
    setAccordion();

    $('.ckeditor').each(function(i, obj) {
        ClassicEditor
            .create(document.querySelector('#'+$(obj).attr('id')))
            .catch(error => {
                console.error( error );
            });
    });

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

    let cycle = 0;
    let expectedCycle = 0;
    let timeout = 0;
    let waitingTime = 0;

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
    $(document).on('focusout', '.input_change', function(e){
        if (!gotChanged(this)) {
            return false;
        }
        this.parentNode.submit();
    });

    // Set localStorage variable if clicked on an accordion
    $(document).on('click', '.card-header', function(){
        window.localStorage.setItem('accordion_id', $(this).attr('id'));
    })

    // ### Videos ###
    //Draw stars for video rating
    $('.voting_stars').mouseover(function() {
        let progressIndexElement = $(this).siblings('.progress_index');
        //Fetch the index data attribute from the element you are hovering over
        let currentIndex = parseInt($(this).data('index'));

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

    // this checks if in the /videos/index the select for searching the category was changed
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
        resetSearch();
        window.location = '/admin/videos?resetSearch=yes';
    });

    //Show trainer box
    $(document).on('click', '#showTrainer', function(){
        $('#trainerbox').collapse('toggle');
    });

    // Sets the event handler for the trainer function in Actions on /videos
    $(document).on('click', '#startTrainer', function(){
        expectedCycle = document.getElementById('expectedCycle').value;
        cycle = 1;
        playSound('training', 0);
    });

    // Event handler for stopping the trainer
    $(document).on('click', '#stopTrainer', function(){
       clearTimeout(timeout);
       document.getElementById('trainerinfo').innerHTML = 'Training interrupted!';
    });

    // Event handler for soundbox
    $(document).on('click', '.soundbox', function(e){
        e.stopPropagation();
        let str = e.target.id;
        playSound('target', str.replace(/\D/g, ""));
    })

// Functions

    //### Videos ###
    // Call the database and fetch one audio file for playing it
    function playSound(mode, videoId){
        setCSRF();
        $.ajax({
            url: "/admin/videos/playSound",
            method: "POST",
            dataType: "text",
            data: {
                mode: mode,
                videoId: videoId,
            },
            success: function(r) {
                let json = JSON.parse(r);
                if (mode === 'training') {
                    trainer(json, mode);
                }
                if (mode === 'target') {
                    soundCheck(json);
                }
            }, error: function(error){
                console.log(error);
            }
        });
    }

    function trainer(json, mode){
        if (cycle <= expectedCycle){
            // Play audio x times
            const audio = new Audio('/storage/sounds/' + json['filePath']);
            if (cycle === 1){
                document.getElementById('trainerinfo').innerHTML = 'Cantante habla: ' + json['title'];
                const run = audio.play();
                waitingTime = json['duration'];
            } else {
                timeout = setTimeout(function () {
                    document.getElementById('trainerinfo').innerHTML = 'Cantante habla: ' + json['title'];
                    const run = audio.play();
                    waitingTime = json['duration'];
                }, waitingTime * 1000);
            }
            cycle++;
            audio.addEventListener('ended',function(){playSound(mode)});
        } else {
            document.getElementById('trainerinfo').innerHTML = 'End of training reached!';
        }
    }

    // function to play just one sound
    function soundCheck(json) {
        const audio = new Audio('/storage/sounds/' + json['filePath']);
        const run = audio.play();
    }

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
                console.log(r);
                $('#video_'+videoId).closest('.card').find('.progress_index').data('index', progressIndex);
            }, error: function (error){
                console.log(error);
            }
        });
    }

    // Clears the PHP session variable for storing the Search
    function resetSearch(){
        setCSRF();
        $.ajax({
            url: "/admin/videos/resetSearch",
            method: "POST",
            success: function(r) {
                console.log(r);
            }, error: function(error){
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

    // checks if something was changed inside an input field
    function gotChanged(element){
        const initialValue = element.getAttribute('value');
        return element.value !== initialValue;
    }

    // Get the current accordion setting and set it for the dashboard
    // redo that and use localStorage for saving the state of the accordion
    function setAccordion(){
        let accordionTrigger = window.localStorage.getItem('accordion_id');

        if (accordionTrigger){
            $('#' + accordionTrigger).siblings('.collapse').collapse('toggle');
        }
    }

    // make videos play when hovered over them
    function hoverVideo(e) {
        if ($('video', this).length){
            $('video', this).get(0).play();
        }
    }
    function hideVideo(e) {
        if ($('video',this).length) {
            $('video', this).get(0).pause();
        }
    }

})//end of $(document).ready(function(){});
