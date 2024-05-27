require([
     'jquery',
     'jquery/ui',
     'domReady!'
     ],
     function($, accordion) {
        window.onload = function(){
            if(window.innerWidth < 768) {
                $("#footerPU > div").accordion({
                  heightStyle: "content",
                  active: true,
                  collapsible: true,
                  autoHeight: false
                });
            }
        }
     }
);