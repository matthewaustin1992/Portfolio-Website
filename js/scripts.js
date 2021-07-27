console.log('initialized correctly');
//initialize base variables and functions
var vars = {
    headerFix: 68,
    screen: {
        'xs': 320,
        'sm' : 640,
        'md' : 960,
        'lg' : 1280
    }
}
jQuery(document).ready(function(){
    screenSize();
    nav();
    //scrollDown();
    imageHover();
    //checkSection();
    skillsHeaders();
});
jQuery(window).scroll(function(){
    headerControls();
    //checkSection();
});
jQuery(window).resize(function(){
    //checkSection();
    screenSize();
    skillsHeaders();
});
//basic listener for the pseudo links in the main navigation.
var nav = function() {
    //console.log('header function initialized');
    var old_hash = window.location.hash;
    if (old_hash.length !== 0) {
        var section= old_hash.substr(1);
        var selector = '[data-loaded'+section+']';
        var target = '.' + section;
        jQuery(selector).addClass('active');
        jQuery(target).addClass('loaded').addClass('active');
        var scroll = jQuery(target).offset().top - 144;
        //console.log(item, target, scroll);
        jQuery('body,html').animate({
            scrollTop: scroll
        }, 1000);
    }
    jQuery('header ul li').click(function(){
        jQuery('header ul li.active, .content-section.active').removeClass('active');
        var item = '.'+ jQuery(this).attr('data-target');
        var hash = jQuery(this).attr('data-target');
        var target = jQuery(item);
        window.location.hash = hash;
        jQuery(this).addClass('active');
        jQuery(target).addClass('loaded').addClass('active');
        var scroll = jQuery(target).offset().top - 144;
        //console.log(item, target, scroll);
        jQuery('body,html').animate({
            scrollTop: scroll
        }, 1000);
    });
}
//listener in intro text section to move down to About section
/*var scrollDown = function() {
    jQuery('.scroll-down').click(function(){
        var hash="about";
        window.location.hash = hash;
        jQuery('header ul li.active, .content-section.active').removeClass('active');
        jQuery('[data-target=about]').addClass('active');
        jQuery('.about').addClass('loaded').addClass('active');
        jQuery('body,html').animate({
            scrollTop: jQuery('.about').offset().top - 144
        }, 1000);
    }); 
}*/
//control the fix/natural state of the header while scrolling down the pagee
var headerControls = function(){
    var offset = jQuery('body,html').scrollTop();
    //console.log(offset);
    if (offset > vars.headerFix) {
        jQuery('header').addClass('attached');
        jQuery('body').addClass('header-attached');
    } else {
        jQuery('header').removeClass('attached');
        jQuery('body').removeClass('header-attached')
    }
}
//faux hover effect on the images. Most of this is handled with CSS but a little JS is needed to make the transition look good in regard to z-indeces due to timing with transitions
var imageHover = function() {
    jQuery('.about .images img').mouseenter(function(e){
        console.log(e);
        jQuery(e.target).addClass('active');
    });
    jQuery('.about .images img').mouseleave(function(e){
        setTimeout(function(){
            jQuery(e.target).removeClass('active');
        }, 500);
    });
}
//check where the user is on the page and apply classes to the section or header item accordingly
/*
var checkSection = function() {
    var window_offset = jQuery('body,html').scrollTop();
    jQuery('.content-section').each(function(){
        var section_offset = jQuery(this).offset().top;
        console.log(window_offset, section_offset);
        if (section_offset < window_offset) {
            jQuery(this).addClass('loaded').addClass('active');
        }
    });
}*/
//script for ensuring that the skills headers evenly align
var skillsHeaders = function() {
    var height = 0;
    jQuery('.content-section.skills .skillset h3').css('height','');
    jQuery('.content-section.skills .skillset h3').each(function(){
        var header_height = jQuery(this).outerHeight(false);
        if (header_height > height) height = header_height;
    });
    jQuery('.content-section.skills .skillset h3').css('height', height+'px');
}
/*screen size script */
var screenSize = function() {
    var width = jQuery(window).width();
    switch (true) {
        case (vars.screen.sm > width): 
            jQuery('body').attr('screen','xs');
            break;
        case (vars.screen.md > width && width >= vars.screen.xs):
            jQuery('body').attr('screen','sm');
            break;
        case (vars.screen.lg > width && width >= vars.screen.md):
            jQuery('body').attr('screen','md');
            break;
        case (width >= vars.screen.lg):
            jQuery('body').attr('screen','lg');
            break;
    }
}
