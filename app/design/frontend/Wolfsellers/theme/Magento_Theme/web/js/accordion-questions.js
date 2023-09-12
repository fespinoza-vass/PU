require([
    'jquery',
    'jquery/ui',
    'domReady!'
    ],
    function($, accordion) {

        //function for accordion
        $(".row-info-questions > div").accordion({
        heightStyle: "content",
        active: true,
        collapsible: true,
        autoHeight: false
        });

        //scroll animation function
        $('.pagebuilder-button-primary').click(function(e){
            e.preventDefault();
            var target = $($(this).attr('href'));
            if(target.length){
                var scrollTo = target.offset().top - 160;
                $('body, html').animate({scrollTop: scrollTo+'px'}, 500);
                $('.content-menu-questions').removeClass('active');
                $('.button-primary-mobile-questions').removeClass('active');
            }
        });

        $(".button-primary-mobile-questions").click(function () {
            $(this).toggleClass("active");
            $('.content-menu-questions').toggleClass("active");
        });
    }
);