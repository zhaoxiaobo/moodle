<?php

$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$knownregionpre = $PAGE->blocks->is_known_region('side-pre');
$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$regions = bootstrap_grid($hassidepre, $hassidepost);
$PAGE->set_popup_notification_allowed(false);
if ($knownregionpre || $knownregionpost) {
    theme_bootstrap_initialise_zoom($PAGE);
}
$setzoom = theme_bootstrap_get_zoom();

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />    
    <?php echo $OUTPUT->standard_head_html(); ?>
    <style type="text/css">
	body{
		background-image:url('../theme/bootstrap/pix/loginpagebg.png');
		background-color:#1992f5!important;
		background-position:center center;
		background-size:cover;
		background-repeat:no-repeat;
	}
    .container-fluid > h1{
		text-align:center;
		color:#FFFFFF;
	}
	.loginpanel > h2{
		display:none;
	}
	.loginform > .form-label{
		display:none;
	}
	.loginbox.onecolumn {
	  position: relative;
	  min-height: 1px;
	  min-height: 20px;
	  padding: 19px;
	  padding-right: 15px;
	  padding-left: 15px;
	  margin-bottom: 20px;
	  background-color:transparent;
	  border: 0px solid #e3e3e3;
	  border-radius: 4px;
	  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
	  box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
  	} 
	#username,#password{
		height:50px;
		line-height:50px;
		color:#FFF;
		padding-left:40px;
		background:url('../theme/bootstrap/pix/username.png') 16px 16px no-repeat rgba(255,255,255,.3);
		border-width:0;
	}
	#password{
		background:url('../theme/bootstrap/pix/pwd.png') 16px 16px no-repeat rgba(255,255,255,.3);
	}
	#username::-webkit-input-placeholder,#password::-webkit-input-placeholder{
		color:#FFF;
	}
	label,.forgetpass > a,.loginbox .desc,.logininfo,.homelink > a{
		color:#FFF;
	}
	#page-footer {
	  border-top-width: 0;
  	}
	#username:-webkit-autofill, #password:-webkit-autofill {
		box-shadow:0 0 0 600px #fff inset;
	}
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
</head>

<body <?php echo $OUTPUT->body_attributes($setzoom); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<nav role="navigation" class="navbar navbar-default hidden">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#moodle-navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo $CFG->wwwroot;?>"><?php echo $SITE->shortname; ?></a>
    </div>

    <div id="moodle-navbar" class="navbar-collapse collapse">
        <?php echo $OUTPUT->custom_menu(); ?>
        <?php echo $OUTPUT->user_menu(); ?>
        <ul class="nav pull-right">
            <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
        </ul>
    </div>
    </div>
</nav>
<header class="moodleheader">
    <div class="container-fluid">
    <a href="<?php echo $CFG->wwwroot ?>" class="logo"></a>
    <?php echo $OUTPUT->page_heading(); ?>
    </div>
</header>

<div id="page" class="container-fluid">
    <header id="page-header" class="clearfix">
        <div id="page-navbar" class="clearfix">
            <nav class="breadcrumb-nav" role="navigation" aria-label="breadcrumb"><?php echo $OUTPUT->navbar(); ?></nav>
            <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
            <?php if ($knownregionpre || $knownregionpost) { ?>
                <div class="breadcrumb-button"> <?php echo $OUTPUT->content_zoom(); ?></div>
            <?php } ?>
        </div>

        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>

    <div id="page-content" class="row">
        <div id="region-main" class="<?php echo $regions['content']; ?>">
            <?php
            echo $OUTPUT->course_content_header();

            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </div>

        <?php
        if ($knownregionpre) {
            echo $OUTPUT->blocks('side-pre', $regions['pre']);
        }?>
        <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>
    </div>

    <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
<script>
var oUsername=document.getElementById('username');
var oPassword=document.getElementById('password');
oUsername.setAttribute("placeholder","用户账号");
oPassword.setAttribute("placeholder","密码");
</script>
</body>
</html>
