(function ($) {
    var WidgetElements_AdvancedVideoHandler = function ($scope, $) {
        //console.log( $scope );
        var elementSettings = get_Dyncontel_ElementSettings($scope);
        var id_scope = $scope.attr('data-id');
        var customControls = elementSettings.dce_video_custom_controls;



        if (customControls) {

            var showImageOverlay = Boolean(elementSettings.show_image_overlay);
            var videoType = elementSettings.video_type;
            var autoplay = Boolean(elementSettings.autoplay);
            var lightbox = Boolean(elementSettings.lightbox);

            var muted = Boolean(elementSettings.mute);
            var loop = Boolean(elementSettings.loop);
            var controls = elementSettings.dce_video_controls;




            // In caso di iframe Youtube o Vimeo
            /*if(videoType != 'hosted'){
             $(videoSelector).find('iframe').wrap('<div class="iframe-wrapper"></div>');
             //videoSelector = $scope.find('.iframe-wrapper'); 
             }*/
            /*var player = new Plyr(videoSelector, {
             controls: controls, 
             }); */

            //alert('showImageOverlay: '+showImageOverlay+' muted: '+muted+' loop: '+loop+' controls: '+controls+' lightbox: '+lightbox+' autoplay: '+autoplay+' videoType: '+videoType);
            var generatePlyrVideo = function () {
                var videoContainer = '.elementor-element-' + id_scope;
                var videoSelector = videoContainer + ' .elementor-wrapper';
                if (videoType == 'hosted') {
                    videoSelector = videoContainer + ' .elementor-video';
                }


                // LIGHBOX
                if (lightbox) {
                    videoContainer = '#elementor-lightbox-' + id_scope;
                    videoSelector = videoContainer + ' .elementor-video-container > div';
                }


                // In caso di iframe Youtube o Vimeo
                /*if(videoType != 'hosted'){
                 videoContainer.find('iframe').wrap('<div class="iframe-wrapper"></div>');
                 //videoSelector = $scope.find('.iframe-wrapper'); 
                 }*/


                // -----------------------------------------------
                /*if (videoType == 'hosted') {
                 videoSelector = videoContainer.find('.elementor-video');                                
                 }*/
                /*
                 if (videoType == 'hosted') {
                 videoSelector = $(videoContainer).find('video');
                 } else {
                 videoSelector = videoContainer.find('.iframe-wrapper');
                 }*/
                //var video_id = 'dce_plyr_'+id_scope;
                //videoSelector.attr('id', video_id);
                // -----------------------------------------------
                //console.log(lightbox);  
                /*
                if (lightbox)
                    console.log(videoSelector);
                */

                var player = new Plyr(videoSelector, {
                    //title: 'Example Title',
                    controls: controls, //['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
                    //quality: { default: 240, options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240] },
                    autoplay: autoplay,
                    muted: muted,
                    loop: {active: loop},
                    disableContextMenu: false,
                    hideControls: false
                            //resetOnEnd, true
                            //previewThumbnails: { enabled: true }
                            //previewThumbnails:{thumbContainer:"plyr__preview-thumb",thumbContainerShown:"plyr__preview-thumb--is-shown",imageContainer:"plyr__preview-thumb__image-container",timeContainer:"plyr__preview-thumb__time-container",scrubbingContainer:"plyr__preview-scrubbing",scrubbingContainerShown:"plyr__preview-scrubbing--is-shown"}
                            //ratio:
                });

            }


            if ($scope.find('.elementor-custom-embed-image-overlay').length) {
                $scope.on('mouseup', '.elementor-custom-embed-image-overlay', function () {
                    if (lightbox) {
                        setTimeout(function () {
                            generatePlyrVideo();
                        }, 1000);

                    } else {
                        setTimeout(function () {
                            generatePlyrVideo();
                        }, 100);
                    }
                });
            } else {
                generatePlyrVideo();
            }


        }



        //speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] }
        //quality: { default: 576, options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240] }
    };


    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/video.default', WidgetElements_AdvancedVideoHandler);
    });
})(jQuery);