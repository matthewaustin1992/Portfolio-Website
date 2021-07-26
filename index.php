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
    <div class="content-header">
        <h1>Matthew Austin, Web Developer</h1>
        <p>UI/UX, Full Stack</p>
        <div class="contact-icons flex-container justify-center">
            <a href="https://www.linkedin.com/in/matt-austin-085b89b0" target="_blank"><span class="fab fa-linkedin-in"></span></a>
            <a href="https://github.com/matthewaustin1992" target="_blank"><span class="fab fa-github"></span></a>
            <a href="mailto:maustin92@gmail.com" target="_blank"><span class="fas fa-envelope"></span></a>
        </div>
    </div>
    <div class="content-section about container loaded active">
        <h2>About Me</h2>
        <div class="flex-container wrap">
            <!--<div class="images flex-container wrap space-between">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery1.jpg" width="480" height="480" alt="Gallery Image 1">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery2.jpg" width="480" height="480" alt="Gallery Image 2">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery3.jpg" width="480" height="480" alt="Gallery Image 3">
                <img src="<?php echo get_template_directory_uri();?>/images/gallery4.jpg" width="480" height="480" alt="Gallery Image 4">
            </div>-->
            <div class="text-section flex-container space-between align-center">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/gallery2.jpg" width="240" height="240" alt="Gallery Image 2">
                </div>    
                <div class="text">
                    <p>I am a website developer located in Marlton, NJ that specializes in full stack website development, but primarily focuses on front end User Interface (UI) and User Experience (UX). After entering into the field in early 2015 I have worked on a variety of projects using Content Management systems such as as Wordpress and Joomla, static HTML pages, and Web Applications using the React framework. Much of my professional experience has been focused on JavaScript and CSS development, with some use of PHP in the context of CMS web development, and with a particular focus on using newer frameworks, elements of these languages, and libraries to help innovate pre-existing websites and modernize them.</p>
                </div>
            </div>
            <div class="text-section flex-container space-between align-center">
                <div class="text">
                    <p>Growing up I had a strong interest in video games and went into a major in college focused on programming to learn more about how they were made. Between the years of 2011 and 2014 I attended the College of New Jersey (TCNJ) and undertook its Interative Multimedia program, graduating a year early with a Bachelor of the Arts degree. Towards the end of my education and after graduation I pivoted into web development and have worked in that field ever since. I still maintain a passion for game design and development, for both digital and physical games.</p>
                </div>
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/gallery3.jpg" width="240" height="240" alt="Gallery Image 3">
                </div>
            </div>
            <div class="text-section flex-container space-between align-center">   
                <div class="image">
                <img src="<?php echo get_template_directory_uri();?>/images/books.jpg" width="240" height="240" alt="Playbooks">
                </div>    
                <div class="text">
                    <p>When I'm not programming I am often practicing guitar, working on writing or playing in any number of tabletop role-playing games, painting miniatures, or riding my bike when the weather allows it. I have a passion for writing and music that is often worked into other aspects of my life, whether directly or indirectly.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="content-section skills container loaded">
        <h2>Skills</h2>
        <div class="flex-container justify-center wrap">
            <div class="skillset">
                <h3>CSS</h3>
                <ul>
                    <li>SASS</li>
                    <li>LESS</li>
                    <li>Flexbox and CSS Grid</li>
                </ul>
            </div>
            <div class="skillset">
                <h3>JavaScript</h3>
                <ul>
                    <li>jQuery</li>
                    <li>Vue.Js</li>
                    <li>React</li>
                    <li>TypeScript</li>
                </ul>
            </div>
            <div class="skillset">
                <h3>HTML</h3>
                <ul>
                    <li>Static HTML</li>
                    <li>HTML Generated from Content Management Systems</li>
                </ul>
            </div>
            <div class="skillset">
                <h3>CMS Environments</h3>
                <ul>
                    <li>Joomla</li>
                    <li>Wordpress</li>
                </ul>
            </div>
            <div class="skillset">
                <h3>Backend Website Development</h3>
                <ul>
                    <li>PHP</li>
                    <li>MySql</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-section portfolio container loaded">
        <h2>Portfolio</h2>
        <div class="flex-container space-between wrap">
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/rothman.JPG" alt="Rothman Orthopaedics"/>
                    <a href="https://rothmanortho.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Rothman Orthopaedics</h3>
                    <h4>2015-Present</h4>
                    <p>Assisted with implementing redesign to website along with long term maintenance and additions of new sections.</p>
                    <a href="https://rothmanortho.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/woodmere.JPG" alt="Woodmere Art Museum"/>
                    <a href="https://woodmereartmuseum.org/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Woodmere Art Museum</h3>
                    <h4>2016-Present</h4>
                    <p>Constructed site's backend structure and frontend UX/UI from provided design and assisted with long term maintenance and implementing new sections using Joomla CMS.</p>
                    <a href="https://woodmereartmuseum.org/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/bls.JPG" alt="Brinjac Lighting Studios"/>
                    <a href="https://www.bls-design.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Brinjac Lighting Studios</h3>
                    <h4>2017</h4>
                    <p>Constructed site's backend structure and frontend UX/UI from provided design using Joomla CMS.</p>
                    <a href="https://www.bls-design.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/ips.JPG" alt="IPS"/>
                    <a href="https://www.ipsdb.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>IPS - Integrated Project Services, LLC</h3>
                    <h4>2017-Present</h4>
                    <p>Assisted with constructing backend structure and frontend UX/UI from provided design and assisted with long term maintenance and implementing new sections using Joomla CMS.</p>
                    <a href="https://www.ipsdb.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/htk.JPG" alt="HTK"/>
                    <a href="https://www.htk.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Horner, Townsend, &amp; Kent, LLC</h3>
                    <h4>2019</h4>
                    <p>Assisted with constructing data structure and frontend UX/UI from provided design using React.js.</p>
                    <a href="https://www.htk.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/greyhawk.JPG" alt="Greyhawk"/>
                    <a href="https://greyhawk.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Greyhawk</h3>
                    <h4>2021</h4>
                    <p>Implemented frontend redesign of website and backend content structure updates in Wordpress CMS.</p>
                    <a href="https://greyhawk.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/tricentury.JPG" alt="Tricentury Eyecare"/>
                    <a href="https://tricenturyeye.com/" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Tricentury Eyecare</h3>
                    <h4>2020-Present</h4>
                    <p>Ported site from a previously developed incarnation with different branding to a new provided design as well as reworked existing data structure through Joomla CMS.</p>
                    <a href="https://tricenturyeye.com/" target="_blank">View Project</a>
                </div>
            </div>
            <div class="item">
                <div class="image">
                    <img src="<?php echo get_template_directory_uri();?>/images/ur.JPG" alt="Undoing Ruin Character Sheet"/>
                    <a href="https://github.com/matthewaustin1992/Undoing-Ruin-Character-Sheet" target="_blank">View Project</a>
                </div>
                <div class="text">
                    <h3>Undoing Ruin Character Sheet</h3>
                    <h4>2020-Present</h4>
                    <p>Created a character sheet using HTML and CSS for use on roll20 for a personal game development project.</p>
                    <a href="https://github.com/matthewaustin1992/Undoing-Ruin-Character-Sheet" target="_blank">View Project</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content-section contact container loaded">
        <h2>Contact</h2>
        <p>Want to get in touch? Use one of the links below:</p>
        <div class="flex-container column">
            <a href="https://www.linkedin.com/in/matt-austin-085b89b0" target="_blank">LinkedIn<span class="fab fa-linkedin-in"></span></a>
            <a href="https://github.com/matthewaustin1992" target="_blank">GitHub<span class="fab fa-github"></span></a>
            <a href="mailto:maustin92@gmail.com" target="_blank">Email<span class="fas fa-envelope"></span></a>
            <a href="https://matt-austin-developer.com/wp-content/uploads/2021/07/Matthew-Austin-resume-2021.pdf" target="_blank">Resume<span class="fas fa-scroll"></span></a>
        </div>
    </div>
    <footer>
        <div class="copyright container flex-container justify-center">
            <a href="https://www.linkedin.com/in/matt-austin-085b89b0" target="_blank"><span class="fab fa-linkedin-in"></span></a>
            <a href="https://github.com/matthewaustin1992" target="_blank"><span class="fab fa-github"></span></a>
            <a href="mailto:maustin92@gmail.com" target="_blank"><span class="fas fa-envelope"></span></a>
            <span>&copy; Matthew Austin, <?php echo Date('Y')?></span>
        </div>
    </footer>
</body>
</html>