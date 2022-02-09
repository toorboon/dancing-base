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

    let actualFigureCounter = 1;
    let figureCounter = 0;
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
    // control textbox on /videos/index so they have fixed height with more link
    // element needs to be "block" and "overflow: hidden", otherwise height cannot be read!
    $('.textbox').each(function() {
        var h = this.scrollHeight;
        var collapsedSize = '80px';
        var div = $(this);
        if (h > 80) {
            div.css('height', collapsedSize);
            div.after('<button type="button" id="more" class="btn btn-sm btn-secondary mt-2 textbox" >more</button><br/>');
            var link = div.next();
            link.click(function(e) {
                e.stopPropagation();

                if (link.text() !== 'Collapse') {
                    link.text('Collapse');
                    div.animate({
                        'height': h
                    });

                } else {
                    div.animate({
                        'height': collapsedSize
                    });
                    link.text('more');
                }
            });
        }
    });

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

        if ($(this).closest('.card').find('video').length > 0) {
            let videoId = $(this).closest('.card').find('video').attr('id').replace(/[^0-9]/g, '');
            saveToDB(videoId, progressIndex);
        } else {
            alert('You cannot rate a video, without a video, dummy!');
        }
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

  //## Trainer feature ##
    //Show trainer box
    $(document).on('click', '#showTrainer', function(){
        const trainerbox = $('#trainerbox');
        trainerbox.collapse('toggle');
        if (trainerbox.is(':visible')){
            document.getElementById('trainerinfo').innerHTML = 'Choose how many figures you want to train!';
        }
    });

    // Sets the event handler for starting the trainer function in Actions dropdown on /videos
    $(document).on('click', '#startTrainer', function(){
        figureCounter = document.getElementById('figureCounter').value;
        if (figureCounter > 0){
            // Reset the actualFigureCounter so you can start over again
            actualFigureCounter = 1;

            // Open the modal so the video can be displayed
            let modal = $('#trainervideo').modal({
                keyboard: false,
                backdrop: 'static',
            });
            modal.modal('show');

            playElement('training', 0);
        } else {
            alert('You have to choose at least one figure with the figure counter!');
        }
    });

    // Handle the closing behaviour of modal for trainer
    $(document).on('click','#closeTrainer', function(){
        const trainerModal = $('#trainervideo');
        if (trainerModal.is(':visible')) {
            trainerModal.modal('hide');
        }
    });

    // Event handler for stopping the trainer
    $(document).on('click', '#stopTrainer', function(){
        let test = document.querySelector('#videobox')
        test.pause();
        test.currentTime = 0;
        document.getElementById('trainerinfo').innerHTML = 'Training interrupted!';
    });

    // Event handler for soundbox
    $(document).on('click', '.soundbox', function(e){
        e.stopPropagation();
        let str = e.target.id;
        playElement('target', str.replace(/\D/g, ""));
    })

// Functions
    //### Videos ###
    // Call the database and fetch one audio file for playing it (for training or just soundbox purpose)
    function playElement(mode, videoId){
        setCSRF();
        $.ajax({
            url: "/admin/videos/fetchElement",
            method: "POST",
            dataType: "text",
            data: {
                mode: mode,
                videoId: videoId,
            },
            success: function(r) {
                if (isJson(r)) {
                    let json = JSON.parse(r);

                    if (mode === 'training') {
                        trainer(json, mode);
                    }
                    if (mode === 'target') {
                        soundCheck(json);
                    }
                } else {
                    alert('No training possible, because no video is in training mode! Set at least one to training mode ("T") in the video overview first!');
                }
            }, error: function(error){
                console.log(error);
            }
        });
    }

    // checks if JSON is valid or broken and returns false if broken
    function isJson(item) {
        item = typeof item !== "string"
            ? JSON.stringify(item)
            : item;

        try {
            item = JSON.parse(item);
        } catch (e) {
            return false;
        }

        if (typeof item === "object" && item !== null) {
            return true;
        }

        return false;
    }

    // plays a number of sounds as requested so the Rueda can be practised with a virtual Cantante
    function trainer(json, mode){
        if (actualFigureCounter <= figureCounter){
            // Play audo/video x times -> '/storage/sounds/' + json['filePath']
            // console.log(json)
            const audio = new Audio('/storage/sounds/' + json['filePath']);
            document.getElementById('modalTitle').innerHTML = 'Cantante habla: ' + json['title'];
            const run = audio.play();

            // init video and make it ready to run
            let video = document.getElementById('videobox');
            video.innerHTML = '';
            let source = document.createElement('source');

            source.setAttribute('src', '/storage/videos/' + json['video']);
            source.setAttribute('type', 'video/mp4');

            video.appendChild(source);
            // console.log({src: source.getAttribute('src')})
            video.load();
            video.play();

            video.addEventListener('ended',function(){
                if (actualFigureCounter > figureCounter){
                    document.getElementById('modalTitle').innerHTML = 'End of training reached!';
                    document.getElementById('trainerinfo').innerHTML = 'End of training reached!';
                }
                playElement(mode)
            });
            actualFigureCounter++;
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
        for (let i=0; i < index; i++) {
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
