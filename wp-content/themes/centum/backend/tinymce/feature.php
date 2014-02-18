<?php
// Call WP Load
$wp_include = "../wp-load.php";$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {$wp_include = "../$wp_include";} require($wp_include);
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__("You are not allowed to be here","purepress"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Feature box creator</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/backend/css/tinymce.css" type="text/css" media="screen" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js?v=3211"></script>
    <script type="text/javascript" >
    tinyMCEPopup.requireLangPack();


    var AccordionDialog = {
        init : function() {
          var f = document.forms[0];
          output = '';

		// Get the selected contents as text and place it in the input
		//f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
    },
    insert : function() {

        var title = jQuery('#title').val();


        var color = jQuery('#iconcolor option:selected').val();
        var text = jQuery('#content').val();
        var icon = jQuery('#tab-icon option:selected').val();
        var iconcolor = jQuery('#iconcolor option:selected').val();

        if (text) {
            output = '[feature color="'+color+'" title="'+title+'" icon="'+icon+'"  iconcolor="'+iconcolor+'"  ]'+ text + '[/feature]';
        } else {
            output = '[feature color="'+color+'" title="'+title+'" icon="'+icon+'" ]'+ ListsDialog.local_ed.selection.getContent() + '[/feature]';
        }
        // Insert the contents from the input into the document
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(AccordionDialog.init, AccordionDialog);

</script>
</head>
<body>

    <form onsubmit="AccordionDialog.insert();return false;" action="#">
        <div id="tabs" class="dialogbox">

            <h3>Feature box</h3>
            <p>
                <label>Title:</label>
                <input id="title" name="tab" type="text" class="text" />
            </p>

            <p>
                <label>Preset Icon</label>
                <select id="tab-icon">
                    <option value="none">NONE</option>
                    <option value="ico-glass">glass</option>
                    <option value="ico-leaf">leaf</option>
                    <option value="ico-dog">dog</option>
                    <option value="ico-user">user</option>
                    <option value="ico-girl">girl</option>
                    <option value="ico-car">car</option>
                    <option value="ico-user-add">user-add</option>
                    <option value="ico-user-remove">user-remove</option>
                    <option value="ico-film">film</option>
                    <option value="ico-magic">magic</option>
                    <option value="ico-envelope">envelope</option>
                    <option value="ico-camera">camera</option>
                    <option value="ico-heart">heart</option>
                    <option value="ico-beach-umbrella">beach-umbrella</option>
                    <option value="ico-train">train</option>
                    <option value="ico-print">print</option>
                    <option value="ico-bin">bin</option>
                    <option value="ico-music">music</option>
                    <option value="ico-note">note</option>
                    <option value="ico-cogwheel">cogwheel</option>
                    <option value="ico-home">home</option>
                    <option value="ico-snowflake">snowflake</option>
                    <option value="ico-fire">fire</option>
                    <option value="ico-cogwheels">cogwheels</option>
                    <option value="ico-parents">parents</option>
                    <option value="ico-binoculars">binoculars</option>
                    <option value="ico-road">road</option>
                    <option value="ico-search">search</option>
                    <option value="ico-cars">cars</option>
                    <option value="ico-notes-2">notes-2</option>
                    <option value="ico-pencil">pencil</option>
                    <option value="ico-bus">bus</option>
                    <option value="ico-wifi-alt">wifi-alt</option>
                    <option value="ico-luggage">luggage</option>
                    <option value="ico-old-man">old-man</option>
                    <option value="ico-woman">woman</option>
                    <option value="ico-file">file</option>
                    <option value="ico-credit">credit</option>
                    <option value="ico-airplane">airplane</option>
                    <option value="ico-notes">notes</option>
                    <option value="ico-stats">stats</option>
                    <option value="ico-charts">charts</option>
                    <option value="ico-pie-char">pie-char</option>
                    <option value="ico-group">group</option>
                    <option value="ico-keys">keys</option>
                    <option value="ico-calendar">calendar</option>
                    <option value="ico-router">router</option>
                    <option value="ico-camera-small">camera-small</option>
                    <option value="ico-dislikes">dislikes</option>
                    <option value="ico-star">star</option>
                    <option value="ico-link">link</option>
                    <option value="ico-eye-open">eye-open</option>
                    <option value="ico-eye-close">eye-close</option>
                    <option value="ico-alarm">alarm</option>
                    <option value="ico-clock">clock</option>
                    <option value="ico-stopwatch">stopwatch</option>
                    <option value="ico-projector">projector</option>
                    <option value="ico-history">history</option>
                    <option value="ico-truck">truck</option>
                    <option value="ico-cargo">cargo</option>
                    <option value="ico-compass">compass</option>
                    <option value="ico-keynote">keynote</option>
                    <option value="ico-attach">attach</option>
                    <option value="ico-power">power</option>
                    <option value="ico-lightbulb">lightbulb</option>
                    <option value="ico-tag">tag</option>
                    <option value="ico-tags">tags</option>
                    <option value="ico-cleaning">cleaning</option>
                    <option value="ico-ruller">ruller</option>
                    <option value="ico-gift">gift</option>
                    <option value="ico-umbrella">umbrella</option>
                    <option value="ico-book">book</option>
                    <option value="ico-bookmark">bookmark</option>
                    <option value="ico-signal-alt">signal-alt</option>
                    <option value="ico-cup">cup</option>
                    <option value="ico-stroller">stroller</option>
                    <option value="ico-headphones">headphones</option>
                    <option value="ico-headset">headset</option>
                    <option value="ico-warning-sign">warning-sign</option>
                    <option value="ico-signal">signal</option>
                    <option value="ico-retweet">retweet</option>
                    <option value="ico-refresh">refresh</option>
                    <option value="ico-roundabout">roundabout</option>
                    <option value="ico-random">random</option>
                    <option value="ico-heat">heat</option>
                    <option value="ico-repeat">repeat</option>
                    <option value="ico-display">display</option>
                    <option value="ico-log-book">log-book</option>
                    <option value="ico-adress-book">adress-book</option>
                    <option value="ico-magnet">magnet</option>
                    <option value="ico-table">table</option>
                    <option value="ico-adjust">adjust</option>
                    <option value="ico-tint">tint</option>
                    <option value="ico-crop">crop</option>
                    <option value="ico-vector-path-square">vector-path-square</option>
                    <option value="ico-vector-path-circle">vector-path-circle</option>
                    <option value="ico-vector-path-polygon">vector-path-polygon</option>
                    <option value="ico-vector-path-line">vector-path-line</option>
                    <option value="ico-vector-path-curve">vector-path-curve</option>
                    <option value="ico-vector-path-all">vector-path-all</option>
                    <option value="ico-font">font</option>
                    <option value="ico-italic">italic</option>
                    <option value="ico-bold">bold</option>
                    <option value="ico-text-underline">text-underline</option>
                    <option value="ico-text-strike">text-strike</option>
                    <option value="ico-text-height">text-height</option>
                    <option value="ico-text-width">text-width</option>
                    <option value="ico-text-resize">text-resize</option>
                    <option value="ico-left-indent">left-indent</option>
                    <option value="ico-right-indent">right-indent</option>
                    <option value="ico-align-left">align-left</option>
                    <option value="ico-align-center">align-center</option>
                    <option value="ico-align-right">align-right</option>
                    <option value="ico-justify">justify</option>
                    <option value="ico-list">list</option>
                    <option value="ico-text-smaller">text-smaller</option>
                    <option value="ico-text-bugger">text-bugger</option>
                    <option value="ico-embed">embed</option>
                    <option value="ico-embed-close">embed-close</option>
                    <option value="ico-adjust-alt">adjust-alt</option>
                    <option value="ico-message-full">message-full</option>
                    <option value="ico-message-empty">message-empty</option>
                    <option value="ico-message-in">message-in</option>
                    <option value="ico-message-out">message-out</option>
                    <option value="ico-message-plus">message-plus</option>
                    <option value="ico-message-minus">message-minus</option>
                    <option value="ico-message-ban">message-ban</option>
                    <option value="ico-message-flag">message-flag</option>
                    <option value="ico-message-lock">message-lock</option>
                    <option value="ico-message-new">message-new</option>
                    <option value="ico-inbox">inbox</option>
                    <option value="ico-inbox-plus">inbox-plus</option>
                    <option value="ico-inbox-minus">inbox-minus</option>
                    <option value="ico-inbox-lock">inbox-lock</option>
                    <option value="ico-inbox-in">inbox-in</option>
                    <option value="ico-inbox-out">inbox-out</option>
                    <option value="ico-computer-locked">computer-locked</option>
                    <option value="ico-computer-service">computer-service</option>
                    <option value="ico-computer-process">computer-process</option>
                    <option value="ico-phone">phone</option>
                    <option value="ico-database-lock">database-lock</option>
                    <option value="ico-database-plus">database-plus</option>
                    <option value="ico-database-minus">database-minus</option>
                    <option value="ico-database-ban">database-ban</option>
                    <option value="ico-folder-open">folder-open</option>
                    <option value="ico-folder-plus">folder-plus</option>
                    <option value="ico-folder-minus">folder-minus</option>
                    <option value="ico-folder-lock">folder-lock</option>
                    <option value="ico-folder-flag">folder-flag</option>
                    <option value="ico-folder-new">folder-new</option>
                    <option value="ico-check">check</option>
                    <option value="ico-edit">edit</option>
                    <option value="ico-new-window">new-window</option>
                    <option value="ico-more-windows">more-windows</option>
                    <option value="ico-show-big-thumbnails">show-big-thumbnails</option>
                    <option value="ico-show-thumbnails">show-thumbnails</option>
                    <option value="ico-show-thumbnails-lines">show-thumbnails-lines</option>
                    <option value="ico-show-lines">show-lines</option>
                    <option value="ico-playlist">playlist</option>
                    <option value="ico-picture">picture</option>
                    <option value="ico-imac">imac</option>
                    <option value="ico-macbook">macbook</option>
                    <option value="ico-ipad">ipad</option>
                    <option value="ico-iphone">iphone</option>
                    <option value="ico-iphone-transfer">iphone-transfer</option>
                    <option value="ico-iphone-exchange">iphone-exchange </option>
                    <option value="ico-ipod">ipod</option>
                    <option value="ico-ipod-shuffle">ipod-shuffle</option>
                    <option value="ico-ear-plugs">ear-plugs</option>
                    <option value="ico-albums">albums</option>
                    <option value="ico-step-backward">step-backward</option>
                    <option value="ico-fast-backward">fast-backward</option>
                    <option value="ico-rewind">rewind</option>
                    <option value="ico-play">play</option>
                    <option value="ico-pause">pause</option>
                    <option value="ico-stop">stop</option>
                    <option value="ico-forward">forward</option>
                    <option value="ico-fast-forward">fast-forward</option>
                    <option value="ico-step-forward">step-forward</option>
                    <option value="ico-eject">eject</option>
                    <option value="ico-facetime-video">facetime-video</option>
                    <option value="ico-download-alt">download-alt</option>
                    <option value="ico-mute">mute</option>
                    <option value="ico-volume-up">volume-up</option>
                    <option value="ico-volume-down">volume-down</option>
                    <option value="ico-screenshot">screenshot</option>
                    <option value="ico-move">move</option>
                    <option value="ico-more">more</option>
                    <option value="ico-brightness-reduce">brightness-reduce</option>
                    <option value="ico-brightness-increase">brightness-increase</option>
                    <option value="ico-circle-plus">circle-plus</option>
                    <option value="ico-circle-minus">circle-minus</option>
                    <option value="ico-circle-remove">circle-remove</option>
                    <option value="ico-circle-ok">circle-ok</option>
                    <option value="ico-circle-question-mark">circle-question-mark</option>
                    <option value="ico-circle-info">circle-info</option>
                    <option value="ico-circle-exclamation-mark">circle-exclamation-mark</option>
                    <option value="ico-remove">remove</option>
                    <option value="ico-ok">ok</option>
                    <option value="ico-ban">ban</option>
                    <option value="ico-download">download</option>
                    <option value="ico-upload">upload</option>
                    <option value="ico-shopping-cart">shopping-cart</option>
                    <option value="ico-lock">lock</option>
                    <option value="ico-unlock">unlock</option>
                    <option value="ico-electricity">electricity</option>
                    <option value="ico-ok-2">ok-2</option>
                    <option value="ico-remove-2">remove-2</option>
                    <option value="ico-cart-out">cart-out</option>
                    <option value="ico-cart-in">cart-in</option>
                    <option value="ico-left-arrow">left-arrow</option>
                    <option value="ico-right-arrow">right-arrow</option>
                    <option value="ico-down-arrow">down-arrow</option>
                    <option value="ico-up-arrow">up-arrow</option>
                    <option value="ico-resize-small">resize-small</option>
                    <option value="ico-resize-full">resize-full</option>
                    <option value="ico-circle-arrow-left">circle-arrow-left</option>
                    <option value="ico-circle-arrow-right">circle-arrow-right</option>
                    <option value="ico-circle-arrow-top">circle-arrow-top</option>
                    <option value="ico-circle-arrow-down">circle-arrow-down</option>
                    <option value="ico-play-button">play-button</option>
                    <option value="ico-unshare">unshare</option>
                    <option value="ico-share">share</option>
                    <option value="ico-thin-right-arrow">thin-right-arrow</option>
                    <option value="ico-thin-left-arrow">thin-left-arrow</option>
                    <option value="ico-bluetooth">bluetooth</option>
                    <option value="ico-euro">euro</option>
                    <option value="ico-usd">usd</option>
                    <option value="ico-bp">bp</option>
                    <option value="ico-retweet-2">retweet-2</option>
                    <option value="ico-moon">moon</option>
                    <option value="ico-sun">sun</option>
                    <option value="ico-cloud">cloud</option>
                    <option value="ico-direction">direction</option>
                    <option value="ico-brush">brush</option>
                    <option value="ico-pen">pen</option>
                    <option value="ico-zoom-in">zoom-in</option>
                    <option value="ico-zoom-out">zoom-out</option>
                    <option value="ico-pin">pin</option>
                    <option value="ico-riflescope">riflescope</option>
                    <option value="ico-rotation-lock">rotation-lock</option>
                    <option value="ico-flash">flash</option>
                    <option value="ico-google-maps">google-maps</option>
                    <option value="ico-anchor">anchor</option>
                    <option value="ico-conversation">conversation</option>
                    <option value="ico-chat">chat</option>
                    <option value="ico-male">male</option>
                    <option value="ico-female">female</option>
                    <option value="ico-asterisk">asterisk</option>
                    <option value="ico-divide">divide</option>
                    <option value="ico-snorkel-diving">snorkel-diving</option>
                    <option value="ico-scuba-diving">scuba-diving</option>
                    <option value="ico-oxygen-vottle">oxygen-vottle</option>
                    <option value="ico-fins">fins</option>
                    <option value="ico-fishes">fishes</option>
                    <option value="ico-boat">boat</option>
                    <option value="ico-delete-point">delete-point</option>
                    <option value="ico-sheriffs-star">sheriffs-star</option>
                    <option value="ico-qrcode">qrcode</option>
                    <option value="ico-barcode">barcode</option>
                    <option value="ico-pool">pool</option>
                    <option value="ico-buoy">buoy</option>
                    <option value="ico-spade">spade</option>
                    <option value="ico-bank">bank</option>
                    <option value="ico-vcard">vcard</option>
                    <option value="ico-electircal-plug">electircal-plug</option>
                    <option value="ico-flag">flag</option>
                    <option value="ico-credit-card">credit-card</option>
                    <option value="ico-keyboard-wirelsss">keyboard-wirelsss</option>
                    <option value="ico-keyboard-wired">keyboard-wired</option>
                    <option value="ico-shield">shield</option>
                    <option value="ico-ring">ring</option>
                    <option value="ico-cake">cake</option>
                    <option value="ico-drink">drink</option>
                    <option value="ico-beer">beer</option>
                    <option value="ico-fast-food">fast-food</option>
                    <option value="ico-cutlery">cutlery</option>
                    <option value="ico-pizza">pizza</option>
                    <option value="ico-birthday-cake">birthday-cake</option>
                    <option value="ico-tablet">tablet</option>
                    <option value="ico-settings">settings</option>
                    <option value="ico-bullets">bullets</option>
                    <option value="ico-cardio">cardio</option>
                    <option value="ico-t-shirt">t-shirt</option>
                    <option value="ico-pants">pants</option>
                    <option value="ico-sweater">sweater</option>
                    <option value="ico-fabric">fabric</option>
                    <option value="ico-leather">leather</option>
                    <option value="ico-scissors">scissors</option>
                    <option value="ico-podium">podium</option>
                    <option value="ico-skull">skull</option>
                    <option value="ico-celebration">celebration</option>
                    <option value="ico-tea-kettle">tea-kettle</option>
                    <option value="ico-french-press">french-press</option>
                    <option value="ico-coffe-cup">coffe-cup</option>
                    <option value="ico-pot">pot</option>
                    <option value="ico-grater">grater</option>
                    <option value="ico-kettle">kettle</option>
                    <option value="ico-hospital">hospital</option>
                    <option value="ico-hospital-h">hospital-h</option>
                    <option value="ico-microphone">microphone</option>
                    <option value="ico-webcam">webcam</option>
                    <option value="ico-temple-church">temple-church</option>
                    <option value="ico-temple-islam">temple-islam</option>
                    <option value="ico-temple-hindu">temple-hindu</option>
                    <option value="ico-temple-buddhist">temple-buddhist</option>
                    <option value="ico-electrical-socket-eu">electrical-socket-eu</option>
                    <option value="ico-electrical-socket-us">electrical-socket-us</option>
                    <option value="ico-bomb">bomb</option>
                    <option value="ico-comments">comments</option>
                    <option value="ico-flower">flower</option>
                    <option value="ico-baseball">baseball</option>
                    <option value="ico-rugby">rugby</option>
                    <option value="ico-ax">ax</option>
                    <option value="ico-table-tennis">table-tennis</option>
                    <option value="ico-bowling">bowling</option>
                    <option value="ico-tree-conifer">tree-conifer</option>
                    <option value="ico-tree-deciduous">tree-deciduous</option>
                    <option value="ico-more-items">more-items</option>
                    <option value="ico-sort">sort</option>
                    <option value="ico-filter">filter</option>
                    <option value="ico-gamepad">gamepad</option>
                    <option value="ico-playing-dices">playing-dices</option>
                    <option value="ico-calculator">calculator</option>
                    <option value="ico-tie">tie</option>
                    <option value="ico-wallet">wallet</option>
                    <option value="ico-share">share</option>
                    <option value="ico-sampler">sampler</option>
                    <option value="ico-piano">piano</option>
                    <option value="ico-web-browser">web-browser</option>
                    <option value="ico-blog">blog</option>
                    <option value="ico-dashboard">dashboard</option>
                    <option value="ico-certificate">certificate</option>
                    <option value="ico-bell">bell</option>
                    <option value="ico-candle">candle</option>
                    <option value="ico-pin-classic">pin-classic</option>
                    <option value="ico-iphone-shake">iphone-shake</option>
                    <option value="ico-pin-flag">pin-flag</option>
                    <option value="ico-turtle">turtle</option>
                    <option value="ico-rabbit">rabbit</option>
                    <option value="ico-globe">globe</option>
                    <option value="ico-briefcase">briefcase</option>
                    <option value="ico-hdd">hdd</option>
                    <option value="ico-thumbs-up">thumbs-up</option>
                    <option value="ico-thumbs-down">thumbs-down</option>
                    <option value="ico-hand-right">hand-right</option>
                    <option value="ico-hand-left">hand-left</option>
                    <option value="ico-hand-up">hand-up</option>
                    <option value="ico-hand-down">hand-down</option>
                    <option value="ico-fullscreen">fullscreen</option>
                    <option value="ico-shopping-bag">shopping-bag</option>
                    <option value="ico-book-open">book-open</option>
                    <option value="ico-nameplate">nameplate</option>
                    <option value="ico-vases">vases</option>
                    <option value="ico-announcement">announcement</option>
                    <option value="ico-dumbbell">dumbbell</option>
                    <option value="ico-suitcase">suitcase</option>
                    <option value="ico-file-import">file-import</option>
                    <option value="ico-file-export">file-export</option>


                </select>
            </p>
            <p>
                <label>Preset Icon Color</label>
                <select id="iconcolor">
                    <option value="ico-black">black</option>
                    <option value="ico-white">white</option>
                </select>
            </p>
            <p>
                <label>Content:</label>
                <textarea id="content" name="content" class="text" ></textarea>
            </p>


        </div>
        <div class="mceActionPanel">
            <input type="button" id="insert" name="insert" value="{#insert}" onclick="AccordionDialog.insert();" />
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
        </div>
    </form>

</body>
</html>
