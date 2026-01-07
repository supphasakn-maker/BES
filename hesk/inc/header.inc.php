<?php
/**
 *
 * This file is part of HESK - PHP Help Desk Software.
 *
 * (c) Copyright Klemen Stirn. All rights reserved.
 * https://www.hesk.com
 *
 * For the full copyright and license agreement information visit
 * https://www.hesk.com/eula.php
 *
 */

/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');}

// We'll use this later
$onload='';
?>
<!DOCTYPE html>
<html lang="<?php echo $hesk_settings['languages'][$hesk_settings['language']]['folder'] ?>">
<head>
	<title><?php echo (isset($hesk_settings['tmp_title']) ? $hesk_settings['tmp_title'] : $hesk_settings['hesk_title']); ?></title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0">
    <?php include(HESK_PATH . 'inc/favicon.inc.php'); ?>
    <meta name="format-detection" content="telephone=no">

    <?php
    // Do we need to load JS/CSS for attachments? Needs to go before our app.css
    if (defined('ATTACHMENTS')) {
        ?>
        <link rel="stylesheet" href="<?php echo HESK_PATH; ?>css/dropzone.min.css?<?php echo $hesk_settings['hesk_version']; ?>" type="text/css" />
        <script src="<?php echo HESK_PATH; ?>js/dropzone.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
        <?php
    }
    ?>

    <link rel="stylesheet" media="all" href="<?php echo HESK_PATH; ?>css/app<?php echo $hesk_settings['debug_mode'] ? '' : '.min'; ?>.css?<?php echo $hesk_settings['hesk_version']; ?>">
    <script src="<?php echo HESK_PATH; ?>js/jquery-3.5.1.min.js"></script>
    <?php
    // Do we need to load CSV parsing?
    if (defined('CSV')) {
        ?>
        <script src="<?php echo HESK_PATH; ?>js/jquery.csv.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
        <?php
    }
    ?>
    <script src="<?php echo HESK_PATH; ?>js/selectize.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
    <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/hesk_javascript<?php echo $hesk_settings['debug_mode'] ? '' : '.min'; ?>.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>

    <?php
	/* Tickets shouldn't be indexed by search engines */
	if (defined('HESK_NO_ROBOTS'))
	{
		?>
		<meta name="robots" content="noindex, nofollow" />
		<?php
	}

	/* If page requires WYSIWYG editor include TinyMCE Javascript */
	if (defined('WYSIWYG') && ($hesk_settings['staff_ticket_formatting'] == 2 || $hesk_settings['kb_wysiwyg'] || defined('HTML_EMAIL_TEMPLATE')))
	{
        require(HESK_PATH . 'inc/tiny_mce/tinymce.inc.php');
		?>
		<script type="text/javascript" src="<?php echo HESK_PATH; ?>inc/tiny_mce/7.9.1/tinymce.min.js"></script>
		<?php
	}

    /* If page styles <code> blocks */
    if (defined('STYLE_CODE'))
    {
        ?>
        <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/prism.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
        <link rel="stylesheet" media="all" href="<?php echo HESK_PATH; ?>css/prism.css?<?php echo $hesk_settings['hesk_version']; ?>">
        <?php
    }

	/* If page requires timer load Javascript */
	if (defined('TIMER'))
	{
		?>
		<script type="text/javascript" src="<?php echo HESK_PATH; ?>inc/timer/hesk_timer.js"></script>
		<?php

        /* Need to load default time or a custom one? */
        if ( isset($_SESSION['time_worked']) )
        {
        	$t = hesk_getHHMMSS($_SESSION['time_worked']);
			$onload .= "load_timer('time_worked', " . $t[0] . ", " . $t[1] . ", " . $t[2] . ");";
            unset($t);
        }
        else
        {
        	$onload .= "load_timer('time_worked', 0, 0, 0);";
        }

		/* Autostart timer? */
		if ( ! empty($_SESSION['autostart']) )
		{
			$onload .= "ss();";
		}
	}

	// Use ReCaptcha
	if (defined('RECAPTCHA'))
	{
		echo '<script src="https://www.google.com/recaptcha/api.js?hl='.$hesklang['RECAPTCHA'].'" async defer></script>';
        echo '<script language="Javascript" type="text/javascript">
        function recaptcha_submitForm() {
            document.getElementById("form1").submit();
        }
        </script>';
	}

	// Auto reload
	if (defined('AUTO_RELOAD') && hesk_checkPermission('can_view_tickets',0))
	{
		?>
		<script type="text/javascript">
		var count = <?php echo empty($_SESSION['autoreload']) ? 30 : intval($_SESSION['autoreload']); ?>;
		var reloadcounter;
		var countstart = count;

		function heskReloadTimer()
		{
			count=count-1;
			if (count <= 0)
			{
				clearInterval(reloadcounter);
				window.location.reload();
				return;
			}

			document.getElementById("timer").innerHTML = "(" + count + ")";
		}

		function heskCheckReloading()
		{
			if (<?php if ($_SESSION['autoreload']) echo "getCookie('autorefresh') == null || "; ?>getCookie('autorefresh') == '1')
			{
				document.getElementById("reloadCB").checked=true;
				document.getElementById("timer").innerHTML = "(" + count + ")";
				reloadcounter = setInterval(heskReloadTimer, 1000);
			}
		}

		function toggleAutoRefresh(cb)
		{
			if (cb.checked)
			{
				setCookie('autorefresh', '1');
				document.getElementById("timer").innerHTML = "(" + count + ")";
				reloadcounter = setInterval(heskReloadTimer, 1000);
			}
			else
			{
				setCookie('autorefresh', '0');
				count = countstart;
				clearInterval(reloadcounter);
				document.getElementById("timer").innerHTML = "";
			}
		}

		</script>
		<?php
	}

    // Timeago
    if (defined('TIMEAGO'))
    {
        ?>
        <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/timeago/jquery.timeago.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
        <?php
        // Load language file if not English
        if ($hesklang['TIMEAGO_LANG_FILE'] != 'jquery.timeago.en.js')
        {
            ?>
            <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/timeago/locales/<?php echo $hesklang['TIMEAGO_LANG_FILE']; ?>?<?php echo $hesk_settings['hesk_version']; ?>"></script>
            <?php
        }
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function() {
            $("time.timeago").timeago();
        });
        </script>
        <?php
    }

    // Back to top button
    if (defined('BACK2TOP'))
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function() {
            var offset = 800;
            var duration = 250;
            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.back-to-top').fadeIn(duration);
                } else {
                    jQuery('.back-to-top').fadeOut(duration);
                }
            });

            jQuery('.back-to-top').click(function(event) {
                event.preventDefault();
                jQuery('html, body').animate({scrollTop: 0}, duration);
                return false;
            })
        });
        </script>
        <?php
    }
	?>

    <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/zebra_tooltips.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
    <link rel="stylesheet" href="<?php echo HESK_PATH; ?>css/zebra_tooltips.css">
    <script type="text/javascript">
    $(document).ready(function() {
        // show tooltips for any element that has a class named "tooltip"
        // the content of the tooltip will be taken from the element's "title" attribute
        new $.Zebra_Tooltips($('.tooltip'), {animation_offset: 0, animation_speed: 100, hide_delay: 0, show_delay: 0, vertical_alignment: 'above', vertical_offset: 5});
    });
    </script>

    <?php if ($hesk_settings['admin_css']): ?>
    <link rel="stylesheet" href="<?php echo $hesk_settings['admin_css_url']; ?>">
    <?php endif; ?>

    <?php if ($hesk_settings['admin_js']): ?>
    <script type="text/javascript" src="<?php echo $hesk_settings['admin_js_url']; ?>"></script>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a').forEach(function(link) {
        if (!link.href) return;

        if (link.href.includes('download_attachment.php')) {
            const name = link.textContent.toLowerCase();

            if (name.match(/\.(jpg|jpeg|png|gif|webp)$/)) {
                const img = document.createElement('img');
                img.src = link.href;
                img.style.maxWidth = '250px';
                img.style.border = '1px solid #ccc';
                img.style.display = 'block';
                img.style.margin = '5px 0';

                link.after(img);
            }
        }
    });
});
</script>


</head>
<body onload="<?php echo $onload; unset($onload); ?>">
<a href="#maincontent" class="skiplink"><?php echo $hesklang['skip_to_main_content']; ?></a>


<!-- Image Preview Modal -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('hesk-img-modal');
    const modalImg = document.getElementById('hesk-img-full');

    const btnClose = document.getElementById('hesk-img-close');
    const btnPrev  = document.getElementById('hesk-img-prev');
    const btnNext  = document.getElementById('hesk-img-next');

    // เก็บรูปทั้งหมดในหน้า
    const images = [];
    let currentIndex = 0;
    let zoom = 1;

    document.querySelectorAll('img[src*="download_attachment.php"]').forEach(function(img) {

        images.push(img.src);
        const index = images.length - 1;

        img.style.cursor = 'zoom-in';
        img.style.maxWidth = '200px';
        img.style.maxHeight = '200px';
        img.style.objectFit = 'contain';

        img.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(index);
        });
    });

    function openModal(index) {
        currentIndex = index;
        zoom = 1;
        modalImg.style.transform = 'scale(1)';
        modalImg.src = images[currentIndex];
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
        modalImg.src = '';
    }

    function showNext() {
        if (images.length < 2) return;
        currentIndex = (currentIndex + 1) % images.length;
        openModal(currentIndex);
    }

    function showPrev() {
        if (images.length < 2) return;
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        openModal(currentIndex);
    }

    // Buttons
    btnClose.onclick = closeModal;
    btnNext.onclick = showNext;
    btnPrev.onclick = showPrev;

    // Click background to close
    modal.onclick = function (e) {
        if (e.target === modal) closeModal();
    };

    // Keyboard (Jira-like)
    document.addEventListener('keydown', function (e) {
        if (modal.style.display !== 'flex') return;

        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowRight') showNext();
        if (e.key === 'ArrowLeft') showPrev();
    });

    // Zoom with mouse wheel
    modalImg.addEventListener('wheel', function (e) {
        e.preventDefault();
        zoom += e.deltaY < 0 ? 0.1 : -0.1;
        zoom = Math.min(Math.max(zoom, 1), 3);
        modalImg.style.transform = 'scale(' + zoom + ')';
    });

    // Double click zoom
    modalImg.addEventListener('dblclick', function () {
        zoom = zoom === 1 ? 2 : 1;
        modalImg.style.transform = 'scale(' + zoom + ')';
    });

});
</script>

<div id="hesk-img-modal"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.85);
        z-index:999999;
        overflow:hidden;
        align-items:center;
        justify-content:center;
     ">

    <span id="hesk-img-close"
          style="position:fixed;top:20px;right:30px;font-size:40px;color:#fff;cursor:pointer;">
        &times;
    </span>

    <span id="hesk-img-prev"
          style="position:fixed;left:20px;top:50%;font-size:50px;color:#fff;cursor:pointer;">
        ‹
    </span>

    <span id="hesk-img-next"
          style="position:fixed;right:20px;top:50%;font-size:50px;color:#fff;cursor:pointer;">
        ›
    </span>

    <img id="hesk-img-full"
         style="
            max-width:90vw;
            max-height:90vh;
            width:auto;
            height:auto;
            object-fit:contain;
            cursor:zoom-in;
         ">
</div>


<div class="wrapper">
