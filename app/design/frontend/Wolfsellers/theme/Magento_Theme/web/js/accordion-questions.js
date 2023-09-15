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
            openedState: true,
            activeAll: true,
            animate:{
                duration: 700
            }
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
        
        //function menu fixed
        if(window.innerWidth > 768 ) {
            $(window).scroll(function () {
                var menuSidebar = $('.content-menu-questions .banner-items');
                var parentwidth = $('.block.block-banners').width();      
                
                if($(window).scrollTop() > 200) {
                    menuSidebar.css('position','fixed');
                    menuSidebar.css('top','170px');
                    menuSidebar.toggleClass("fixed").width(parentwidth);  
                
                }
            
                else if ($(window).scrollTop() <= 200) {
                    menuSidebar.css('position','');
                    menuSidebar.css('top','');
                }  
                if (menuSidebar.offset().top + menuSidebar.height() > $(".page-footer").offset().top) {
                    menuSidebar.css('top',-(menuSidebar.offset().top + menuSidebar.height() - $(".page-footer").offset().top) + 150);
                }
            });
        }

    }
);