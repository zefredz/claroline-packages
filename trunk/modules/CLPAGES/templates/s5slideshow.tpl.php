<!-- $Id$ -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title><?php echo $this->page->getTitle(); ?></title>
        
        <!-- metadata -->
        <meta name="generator" content="S5" />
        <meta name="version" content="S5 1.1" />
        <meta name="presdate" content="20050728" />
        <meta name="author" content="Eric A. Meyer" />
        <meta name="company" content="Complex Spiral Consulting" />
            
        <!-- configuration parameters -->
        <meta name="defaultView" content="slideshow" />
        <meta name="controlVis" content="hidden" />

        <!-- style sheet links -->
        <link rel="stylesheet" href="ui/default/slides.css" type="text/css" media="projection" id="slideProj" />
        <link rel="stylesheet" href="ui/default/outline.css" type="text/css" media="screen" id="outlineStyle" />
        <link rel="stylesheet" href="ui/default/print.css" type="text/css" media="print" id="slidePrint" />
        <link rel="stylesheet" href="ui/default/opera.css" type="text/css" media="projection" id="operaFix" />

        <!-- S5 JS -->
        <script src="ui/default/slides.js" type="text/javascript"></script>
    </head>
    
    <body>
        <div class="layout">
            <div id="controls"></div>
            <div id="currentSlide"></div>
            <div id="header">
            </div>
            <div id="footer">
                <h1><?php echo $this->s5Date; ?></h1>
                <h2><?php echo $this->page->getTitle(); ?></h2>
            </div>
        </div>
        
        <div class="presentation">
            
            <?php if ( $this->displaySlideZero ): ?>
            
            <div class="slide">
                <h1><?php echo $this->page->getTitle(); ?></h1>
                <h2><?php echo get_lang('Claroline S5 viewer'); ?></h2>
            </div>
            
            <?php else: ?>
            
            <div class="slide">
                <h1><?php echo $this->page->getTitle(); ?></h1>
                <h2><?php echo get_lang('Claroline S5 viewer'); ?></h2>
                <h3><?php echo get_lang('Click to see your slide preview'); ?></h3>
            </div>
            
            <?php endif; ?>
            
            <?php foreach ( $this->page->getComponentList() as $component ): ?>
            
            <?php if ( $this->displayAllSlides || $this->componentId == $component->getId() ): ?>
            
            <div class="slide">
            <h1><?php echo htmlspecialchars( $component->getTitle() ); ?></h1>
            
            <?php echo $component->render(); ?>
            
            <div class="handout">
            </div>
            </div>
            
            <?php endif; ?>
            
            <?php endforeach; ?>
        </div>
    </body>
</html>
