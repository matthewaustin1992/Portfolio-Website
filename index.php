<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="title" content="Matthew Austin's Portfolio Website">
    <meta name="description" content="Front End UX/UI Web Developer from Marlton, NJ.">
    <meta name="keywords" content="HTML, CSS, JavaScript">
    <meta name="author" content="Matthew Austin">
    <link href="<?php echo get_template_directory_uri();?>/css/styles.css?v=<?php echo(filemtime(get_template_directory().'/css/styles.css'));?>" rel="stylesheet" as="style"></link>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="<?php echo get_template_directory_uri();?>/js/scripts.js?v=<?php echo(filemtime(get_template_directory().'/js/scripts.js'));?>" type="text/JavaScript"></script>
    <script src="https://kit.fontawesome.com/036d5cbd88.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div class="top container">
            <ul class="nav flex-container space-between">
                <li data-target="about">About</li>
                <li data-target="skills">Skills</li>
                <li data-target="portfolio">Portfolio</li>
                <li data-target="contact">Contact</li>
            </ul>
        </div>
    </header>
    <div class="content-header container">
        <h1>Matthew Austin, Web Developer</h1>
        <p>UI/UX, Full Stack</p>
        <div class="scroll-down">
            <span class="target fas fa-arrow-circle-down"></span>
        </div>
    </div>
    <div class="content-section about container">
        <h2>About Me</h2>
        <div class="flex-container space-between align-start">
            <div class="images flex-container wrap space-between">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery1.jpg" width="480" height="480" alt="Gallery Image 1">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery2.jpg" width="480" height="480" alt="Gallery Image 2">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery3.jpg" width="480" height="480" alt="Gallery Image 3">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery4.jpg" width="480" height="480" alt="Gallery Image 4">
            </div>
            <div class="text">
                <p>I am a website developer located in Marlton, NJ that specializes in full stack website development, but primarily focuses on front end User Interface (UI) and User Experience (UX). After entering into the field in early 2015 I have worked on a variety of projects using Content Management systems such as as Wordpress and Joomla, static HTML pages, and Web Applications using the React framework. Much of my professional experience has been focused on JavaScript and CSS development, with some use of PHP in the context of CMS web development, and with a particular focus on using newer frameworks, elements of these languages, and libraries to help innovate pre-existing websites and modernize them.</p>
                <p>Growing up I had a strong interest in video games and went into a major in college focused on programming to learn more about how they were made. Between the years of 2011 and 2014 I attended the College of New Jersey (TCNJ) and undertook its Interative Multimedia program, graduating a year early with a Bachelor of the Arts degree. Towards the end of my education and after graduation I pivoted into web development and have worked in that field ever since. I still maintain a passion for game design and development, for both digital and physical games.</p>
                <p>When I'm not programming I am often practicing guitar, working on writing or playing in any number of tabletop role-playing games, painting miniatures, or riding my bike when the weather allows it. I have a passion for writing and music that is often worked into other aspects of my life, whether directly or indirectly.
            </div>
        </div>
    </div>
    <div class="content-section skills container">
        <h2>Skills</h2>
        <div class="flex-container">
            <div class="one-third"></div>
        </div>
    </div>
    <div class="content-section portfolio container">
        <h2>Portfolio</h2>
        <div class="flex-container">
        <div class="item"></div>
        <div class="item"></div>
        <div class="item"></div>
        <div class="item"></div>
        <div class="item"></div>
        <div class="item"></div>
        <div class="item"></div>
        </div>
    </div>
    <div class="content-section contact container">
        <h2>Contact</h2>
        <div class="flex-container">

        </div>
    </div>
    <footer>
        <div class="copyright container flex-container">
            <div class="left">
                <a href="https://www.linkedin.com/in/matt-austin-085b89b0" target="_blank"><span class="fab fa-linkedin-in"></span></a>
                <a href="mailto:maustin92@gmail.com" target="_blank"><span class="fas fa-envelope"></span></a>
            </div>
            <div class="right">
                <span>&copy; Matthew Austin, <?php echo Date('Y')?></span>
            </div>
        </div>
    </footer>
</body>
</html>