<?php // Before trying to crack the plugin, please consider buying a pro license at http://www.mapsmarker.com. I have put hundreds of hours into the development of the plugin and without honest customers I will not be able to continue the development and support. Thanks for your understanding and your honesty! Robert
if (basename($_SERVER["\123CRIPT_FILEN\101\115E"]) == "\154eaflet-license\056\160hp") { exit ("Pl\145\141\163e \144\157\040not acc\145\163\163 this fi\154\145\040dire\143\164\154y. Than\153\163\041<br/>\074\141 href='htt\160\072//www.map\163\155\141rker.c\157\155/go'>www.ma\160\163marker.com\074\057a>"); } ?><?php echo "\015\012<di\166\040class=\042\167\162\141p\042>\015\012"; ?><?php $l1r=$lq->lw; if (array_key_exists("user",$l1r)) { $O1r=$l1r["\165ser"][0]["email"]; } else { $O1r=""; } function l1s() { if (in_array("curl",get_loaded_extensions())) { return TRUE; } else { return FALSE; } } if ( isset ($_POST["maps_marker_pro_\155\165ltisite_prop\141\147\141te"])) { $O1s= isset ($_POST["\155\141ps_marker_p\162\157\137license_m\165\154\164isite"]) ? $_POST["\155\141\160s_marker_pr\157\137\154icense_mu\154\164\151site"]: ""; if (!wp_verify_nonce($O1s,"\155\141ps_marker_pro\137\154\151cense_mul\164\151\163ite")) exit ("\074br/>".__("\123ecurity che\143\153\040failed\040\055 please cal\154\040\164his f\165\156\143tion fr\157\155 the accor\144\151\156g Leafl\145\164 Maps Mark\145\162 admin page\041","lmm").""); if (is_multisite()) { if (current_user_can("\141ctivate_plugins")) { global $wpdb; $l1t=$wpdb->get_results( "\123ELECT blog_id\040\106ROM {$wpdb->l1t}" ,ARRAY_A); if ($l1t) { $O1t=(get_option("leafletmapsmar\153\145rpro_license\137\153\145y") == TRUE) ? get_option("\154\145afletmapsmarke\162\160\162o_license\137\153\145y"): ""; $l1u=(get_option("leafletmapsmar\153\145\162pro_lice\156\163\145_local_k\145\171") == TRUE) ? get_option("leafletmapsma\162\153\145rpro_lice\156\163\145_local_ke\171"): ""; foreach ($l1t as $O1u) { switch_to_blog($O1u["blog_id"]); update_option("leafletmaps\155\141\162kerpro_lic\145\156\163e_key",$O1t); update_option("\154eafletmapsmarkerp\162\157\137license_l\157\143\141l_key",$l1u); } restore_current_blog(); } echo "<div class=\042\165\160dated\042\040\163tyle=\042\160\141dding:5px\073\042><p>".__("\114icense key was \163\165\143cessfully\040\160\162opagat\145\144\040to al\154\040subsites","\154\155\155")."\074/p></div>"; } } } include ("\151\156\143".DIRECTORY_SEPARATOR."admin-header.php"); ?><?php echo "\015\012\015\012\074\1503 style=\042\146\157nt-size:23\160\170;\042>"; ?><?php _e("Pro License\040\123ettings","\154\155\155"); ?><?php echo "</\150\063\076\015\012\015\012<div\040\143\154ass=\042\167\162ap\042\076\015\012\015\012\011"; ?><?php if (O3() === TRUE) { echo "<\160\076<div class=\042\165\160dated\042\040\163tyle=\042\160\141dding:10px\073\155argin:0px;\042\076".sprintf(__("You have \151\156\163talled Leaf\154\145\164 Maps Ma\162\153\145r Pro \157\156 a localhos\164\040instance.\040\105ntering a\040\154icense ke\171\040here is no\164\040mandatory \142\165\164 recom\155\145nded as thi\163\040also allo\167\163 you to <a\040\150ref=\042\045\061s\042\040\164arget=\042\137\142lank\042\076\157pen su\160\160ort ticke\164\163</a>. Ple\141\163e be awar\145\040that on\143\145 you use \164\150e plugin\040\157n a liv\145\040domain,\040\145ntering\040\141 licen\163\145 key is \155\141ndator\171\041","\154\155m"),"\150\164\164ps://www.maps\155\141\162ker.com/h\145\154\160desk")."</div></p>"; } ?><?php echo "\015\012\015\012\011"; ?><?php if ($lq->lv && ! isset ($_POST["\155\141ps_marker_p\162\157\137register_f\162\145\145"])):; ?><?php echo "\015\012\011\011<div \151\144\075\042m\145\163\163age\042\040\143lass=\042\165\160dated\042\076\015\012\011\011\011<p><b\076"; ?><?php echo $lq->lv; ?><?php echo "</b\076\074/p>\015\012\011\011</div>\015\012\011"; ?><?php endif; ?><?php echo "\015\012\015\012\011"; ?><?php if ($l13 && !$lq->lv):; ?><?php echo "\015\012\011\011\074\144iv id=\042\155\145ssage\042\040\143lass=\042\165\160dated\042\076\015\012\011\011\011<p><b\076"; ?><?php _e("\131\157\165r license \167\141\163 activate\144\040successfull\171\041","lmm"); ?><?php echo "<\057\142></p>\015\012\011\011</div>\015\012\011"; ?><?php endif; ?><?php echo "\015\012\015\012\011"; ?><?php if (empty($l12) && isset ($O11)):; ?><?php echo "\015\012\011\011<d\151\166\040id=\042\155\145ssage\042\040\143lass=\042\165\160dated\042\076\015\012\011\011\011<p><b>"; ?><?php _e($O11); ?><?php echo "\074/b></p>\015\012\011\011\074\057div>\015\012\011"; ?><?php endif; ?><?php echo "\015\012\015\012\011<fo\162\155\040method=\042\160\157st\042\076\015\012\011"; ?><?php wp_nonce_field("maps_marker_pro_li\143\145\156se","ma\160\163\137marker_pro_l\151\143\145nse"); ?><?php echo "\015\012\011\011"; ?><?php if (!$lq->Ot):; ?><?php echo "\015\012\011\011\011\074div styl\145\075\042float:\154\145ft;margin-\162\151\147ht:15px\073\155argin-top:3\160\170\073\042\076\074img src=\042"; ?><?php echo LEAFLET_PLUGIN_URL; ?><?php echo "inc/img/icon\055\143ertificate.pn\147\042 width=\042\064\070\042 \150\145ight=\042\063\070\042></d\151\166>\015\012\011\011\011<\144\151\166 style\075\042font-size\072\0616px;font-\167\145ight:bold;\042\076"; ?><?php _e("Option A: activa\164\145\040an unex\160\151\162ing lic\145\156\163e key","\154\155\155"); ?><?php echo "\074/div>\015\012\011\011\011<p\040\163tyle=\042\155\141\162gin:0.\064\145\155 0 1em \060\073\042>"; ?><?php echo sprintf(__("\107\145t an unexpir\151\156\147 license \153\145\171 at %1\044\163\040and ac\164\151\166ate the\040\154icense key \142\145\154ow:","lmm"),"\074a href=\042ht\164\160\163://www.ma\160\163\155arker.c\157\155\057order\042\040target=\042\137\142lank\042\076\155apsmarker\056\143om/order<\057\141\076"); ?><?php echo "</\160\076\015\012\011\011"; ?><?php endif; ?><?php echo "\015\012\011\011\074\160>\015\012\011\011"; ?><?php if ($lq->Ot) { if ($lq->lv) { $l1v="\142ackground:#ff000\060\073color:#0000\060\060;"; } else { $l1v="\142\141\143kground:#0\060\106\10600;color\072\043000000;"; } } else { $l1v=""; } if ($lq->Ot) { $O1v=__("\165pdate","\154mm"); } else { $O1v=__("\141ctivate","lmm"); } if (current_user_can("\141ctivate_plugi\156\163")) { $Ot=$lq->Ot; $l1w=""; } else { $Ot=__("visi\142\154\145 for admin\163\040only","lmm"); $l1w="disab\154\145\144=\042d\151\163\141bled\042"; } ?><?php echo "\015\012\011\011\074b>"; ?><?php _e("\114icense Key","lmm"); ?><?php echo "\074\057b> <input n\141\155\145=\042l\145\141\146letmaps\155\141rkerpro_lic\145\156\163e_key\042\040\164ype=\042\164\145xt\042\040\163tyle=\042\167\151dth:265px\073"; ?><?php echo $l1v; ?><?php echo "\042\040\166alue=\042"; ?><?php echo $Ot; ?><?php echo "\042\040/> <input t\171\160\145=\042s\165\142\155it\042\040\143lass=\042\142\165tton-prim\141\162y\042 val\165\145\075\042"; ?><?php echo $O1v; ?><?php echo "\042 "; ?><?php echo $l1w; ?><?php echo " />\015\012\011\011</p>\015\012\011</form\076\015\012\015\012\011<hr nosha\144\145\040size=\042\061\042 sty\154\145\075\042\155\141rgin:20px 0\073\142order-top:\061\160x solid #6\066\066666;\042\040\057>\015\012\015\012\011"; ?><?php if (!$lq->Ot && !Ox()):; ?><?php echo "\015\012\011\011\015\012\011\011"; ?><?php if (!empty($l12)):; ?><?php echo "\015\012\011\011\011\074\144iv id=\042\155\145ssage\042\040\143lass=\042\145\162ror\042>\015\012\011\011\011\011"; ?><?php foreach ($l12 as $e):; ?><?php echo "\015\012\011\011\011\011\011\074\160><b>"; ?><?php _e($e); ?><?php echo "\074\057b></p>\015\012\011\011\011\011"; ?><?php endforeach; ?><?php echo "\015\012\011\011\011</div\076\015\012\011\011"; ?><?php endif; ?><?php echo "\015\012\011\015\012\011\011"; ?><?php $O1w=get_option("\154eafletmapsmar\153\145\162pro_licens\145\137\153ey_tria\154"); if ($O1w != NULL) { $l1x="display:\156\157\156e;"; $O1x="\074p><div class=\042\165\160dated\042\040\163tyle=\042\143\154ear:both;p\141\144\144ing:10px\073\042>".sprintf(__("You \141\154\162eady started\040\141 free 30-day-\164\162\151al for \164\150is site - fr\145\145\040trial\040\154icense key:\040\0451\044s","\154\155m"),$O1w)."\074\057div></p>"; $l1y="\144\151sabled=\042\144\151\163abled\042"; } else { $l1x=""; $O1x=""; $l1y=""; } ?><?php echo "\015\012\011\011\015\012\011\011\074div style=\042\146loat:lef\164\073padding-r\151\147ht:10px;\042\076<img src=\042"; ?><?php echo LEAFLET_PLUGIN_URL; ?><?php echo "\151\156c/img/avatar-\160\145\162sonalize\144\056png\042\040\167idth=\042\064\070\042 he\151\147\150t=\042\064\070\042><\057\144iv>\015\012\011\011<div \163\164yle=\042f\157\156t-size:16p\170\073font-weigh\164\072bold;\042\076"; ?><?php _e("\117\160tion B: get a p\145\162\163onalize\144\040trial licen\163\145 key","lmm"); ?><?php echo "</div>\015\012\011\011\074\160>"; ?><?php echo __("\131\157u can test <i>\115\141\160s Marker \120\162\157</i> fo\162\04030 days for\040\146ree without\040\141ny obligati\157\156s.","lmm"); ?><?php echo "\074\057p>\015\012\011\011"; ?><?php echo $O1x; ?><?php echo "\015\012\011\011<div id=\042\162\145gister_f\162\145\145_trial_\160\145rsonalized\042\040style=\042"; ?><?php echo $l1x; ?><?php echo "\042\076\015\012\011\011\011<form meth\157\144\075\042\160\157st\042>\015\012\011\011\011\074input ty\160\145\075'hidd\145\156' name='m\141\160\163_marke\162\137pro_registe\162\137free' val\165\145\075'y' /\076\015\012\011\011\011\011\074\164able sty\154\145=\042cle\141\162-both;ma\162\147in-top:15p\170\073\042>\015\012\011\011\011\011\011<t\162\076\015\012\011\011\011\011\011\011<td>\074\142>"; ?><?php _e("First nam\145","\154\155\155"); ?><?php echo "\074/b></td>\015\012\011\011\011\011\011\011<t\144\076<input nam\145\075\042maps\137\155\141rker_pr\157\137\146irst_n\141\155e\042 ty\160\145\075\042\164\145xt\042 st\171\154\145=\042\167\151dth:291px;\042\040value=\042"; ?><?php echo l11("\155\141ps_marker_pro_fi\162\163\164_name"); ?><?php echo "\042\040/></td>\015\012\011\011\011\011\011</tr>\015\012\011\011\011\011\011<tr>\015\012\011\011\011\011\011\011<\164\144><b>"; ?><?php _e("Last\040\156\141me","lmm"); ?><?php echo "\074\057b></td>\015\012\011\011\011\011\011\011<\164\144><input na\155\145\075\042m\141\160\163_marker\137\160\162o_last\137\156ame\042 t\171\160\145=\042\164\145xt\042 s\164\171\154e=\042\167\151dth:291px;\042\040value=\042"; ?><?php echo l11("maps_m\141\162\153er_pro_last_\156\141\155e"); ?><?php echo "\042\040\057></td>\015\012\011\011\011\011\011</tr>\015\012\011\011\011\011\011<tr>\015\012\011\011\011\011\011\011<t\144\076<b>"; ?><?php _e("E-mail","lmm"); ?><?php echo "\074/b></td>\015\012\011\011\011\011\011\011<td\076\074input nam\145\075\042maps_\155\141rker_pro_e\155\141\151l\042\040\164ype=\042\164\145\170t\042\040\163tyle=\042\167\151dth:291px\073\042 value=\042"; ?><?php echo l11("maps_marker_pro_em\141\151\154"); ?><?php echo "\042 /></\164\144\076\015\012\011\011\011\011\011\074/tr>\015\012\011\011\011\011\011<tr>\015\012\011\011\011\011\011\011\074\164d></td>\015\012\011\011\011\011\011\011\074\164d><input \164\171pe=\042ch\145\143kbox\042\040\156ame=\042m\141\160s_marker\137\160ro_tos\042\040\166alue=\042\131\145s\042\040\143hecked=\042\143\150ecked\042\040/> "; ?><?php echo sprintf(__("I have r\145\141\144 the <a h\162\145\146=\042\045\061\044s\042\040\164arget=\042\137\142lank\042\076\124erms of\040\123ervice</a>\040\141nd <a hre\146\075\042%2\044\163\042 targe\164\075\042_bla\156\153\042>Pri\166\141\143y Poli\143\171</a>.","\154mm"),"\150\164tp://www.maps\155\141\162ker.com/te\162\155\163-of-ser\166\151ces","http://www.maps\155\141\162ker.com/p\162\151\166acy-poli\143\171"); ?><?php echo "\074\057td>\015\012\011\011\011\011\011\074/tr>\015\012\011\011\011\011"; ?><?php if (l1s()) { echo "<tr><td>\074\057td><td><input t\171\160\145=\042\163\165\142mit\042\040\143lass=\042\142\165tton-prim\141\162\171\042 \166\141lue=\042".__("Start personaliz\145\144\040free 30-\144\141\171 trial pe\162\151od","\154mm")."\042 ".$l1y."\040/></td></tr>"; } else { echo "\074tr><td colspan\075\0422\042><p><\144\151\166 class=\042\145\162ror\042\040\163tyle=\042\160\141dding:10\160\170\073\042>\074\163trong>".sprintf(__("Warning: t\150\145\040PHP exte\156\163\151on curl \155\165\163t be ac\164\151\166e on yo\165\162\040serve\162\040in order t\157\040retrieve a\040\146ree trial \154\151cense key.\040\120lease inst\141\154l this exte\156\163ion or con\164\141ct your ad\155\151n (%1s)","lmm"),get_bloginfo("admin_email"))."\074/strong></div></p\076\074/td></tr>"; echo "<tr><td></\164\144\076<td><input\040\164ype=\042s\165\142\155it\042\040\143lass=\042\142\165tton-primar\171\042 value=\042".__("\123tart anonymous fr\145\145\04030-day \164\162\151al perio\144","l\155\155")."\042 disabled=\042\144\151sabled\042 \057\076</td></tr>"; } ?><?php echo "\015\012\011\011\011\011</\164\141\142le>\015\012\011\011\011\074\057form>\015\012\011\011</\144\151v><!--regis\164\145r_free_tr\151\141\154_perso\156\141lized div-\055\076\015\012\011\015\012\011\011\074hr no\163\150\141de si\172\145\075\042\061\042 style=\042\155argin:20p\170\0400;border\055\164op:1px so\154\151d #666666\073\042 />\015\012\011\011\015\012\011\011\074\144iv styl\145\075\042flo\141\164:left;pa\144\144ing-righ\164\07210px;\042\076<img src\075\042"; ?><?php echo LEAFLET_PLUGIN_URL; ?><?php echo "inc/img/ava\164\141\162-anonymo\165\163\056png\042\040\167idth=\042\064\070\042 \150\145ight=\042\064\070\042></d\151\166>\015\012\011\011<div s\164\171le=\042f\157\156\164-size:\061\066px;font-\167\145\151ght:bo\154\144;\042>"; ?><?php _e("O\160\164\151on C: get an \141\156\157nymous tr\151\141\154 licen\163\145 key","lmm"); ?><?php echo "\074\057div>\015\012\011\011"; ?><?php echo $O1x; ?><?php echo "\015\012\011\011<div id=\042\162\145gister_f\162\145\145_trial\137\141nonym\042\040\163tyle=\042"; ?><?php echo $l1x; ?><?php echo "\042>\015\012\011\011\011\074\146orm method=\042\160\157st\042\076\015\012\011\011\011<input\040\164ype='hidden'\040\156ame='map\163\137\155arker_\160\162o_register\137\146\162ee_ano\156\171\155' val\165\145='y' />\015\012\011\011\011\074p>\015\012\011\011\011"; ?><?php echo sprintf(__("Please no\164\145\040that in c\157\156\164rast to\040\141\040perso\156\141\154ized tr\151\141\154 licen\163\145\040you wi\154\154\040not \142\145\040able t\157\040<a href=\042\0451s\042 ta\162\147et=\042_\142\154\141nk\042\076\157pen supp\157\162\164 ticke\164\163</a> and\040\147et a remi\156\144er when y\157\165\162 tria\154\040license h\141\163 expired!","lmm"),"https:/\057\167\167w.mapsmark\145\162\056com/help\144\145\163k"); ?><?php echo "</p>\015\012\011\011\011<\160\076\074input t\171\160\145=\042\143\150eckbox\042\040\156ame=\042\155\141\160s_marke\162\137pro_tos\042\040\166alue=\042\131\145s\042 \143\150ecked=\042\143\150ecked\042\040\057> "; ?><?php echo sprintf(__("\111\040\150ave read t\150\145\040<a href\075\042%1\044s\042\040target=\042\137\142lank\042\076\124erms of Se\162\166\151ce</a> \141\156d <a href=\042\0452\044s\042\040\164arget=\042\137blank\042\076\120rivacy Pol\151\143y</a>.","\154mm"),"http\072\057/www.mapsmarker.\143\157\155/terms-of\055\163ervices","\150ttp://www.ma\160\163\155arker.com/\160\162\151vacy-pol\151\143y"); ?><?php echo "\074\057p>\015\012\011\011\011"; ?><?php if (l1s()) { echo "\074\151nput type=\042\163\165bmit\042\040\143\154ass=\042\142\165tton-pri\155\141\162y\042\040\166alue=\042".__("\123\164\141rt anonymous \146\162\145e 30-day \164\162\151al peri\157\144","\154mm")."\042 ".$l1y."\040/>"; } else { echo "<div class=\042\145\162ror\042\040\163tyle=\042\160\141\144ding:10\160\170;\042><st\162\157\156g>".sprintf(__("Warning: the PHP \145\170\164ension cu\162\154\040must b\145\040active on y\157\165\162 serve\162\040in order to\040\162etrieve a\040\146ree trial l\151\143\145nse ke\171\056 Please in\163\164\141ll th\151\163\040exten\163\151\157n or c\157\156\164act yo\165\162 admin (\045\061s)","\154\155m"),get_bloginfo("\141\144\155in_email"))."\074/strong></\144\151\166></p>"; echo "<input type=\042\163\165bmit\042\040\143lass=\042\142\165tton-prima\162\171\042 value\075\042".__("\123\164art anonymous f\162\145\145 30-day t\162\151\141l period","lmm")."\042 disabled=\042\144\151sabled\042 \057\076"; } ?><?php echo "\015\012\011\011\011\074\057form>\015\012\011\011<\057\144iv><!--reg\151\163\164er_fre\145\137\164rial_an\157\156\171m div\055\055>\015\012\011\015\012\011\011<hr noshad\145\040size=\042\061\042 style=\042\155\141rgin:2\060\160x 0;borde\162\055top:1px \163\157lid #66666\066\073\042 /\076\015\012\011\015\012\011\011\074div style=\042\146loat:le\146\164;margin-r\151\147\150t:15p\170\073\042><\151\155g src=\042"; ?><?php echo LEAFLET_PLUGIN_URL; ?><?php echo "i\156\143/img/icon-loca\154\150\157st.png\042\040\167idth=\042\064\070\042\040\150eight=\042\064\070\042></\144\151\166>\015\012\011\011<di\166\040style=\042\146\157nt-size:1\066\160x;font-we\151\147\150t:bold\073\042>"; ?><?php _e("Option D\072\040start an unlim\151\164\145d, anony\155\157\165s test \157\156\040a loca\154\150\157st ins\164\141\154lation","lmm"); ?><?php echo "\074/div>\015\012\011\011<p>\015\012\011\011"; ?><?php echo sprintf(__("\111\146\040you insta\154\154\040Leaflet \115\141\160s Marker\040\120ro on a l\157\143\141lhost in\163\164allation (<\141\040href=\042\045\061s\042 t\141\162\147et=\042\137\142lank\042\076\163ee avail\141\142\154e packa\147\145s</a>), r\145\147\151sterin\147\040a free 3\060\055day tria\154\040license \153\145y is not m\141\156datory an\144\040the plug\151\156 can also\040\142e tested\040\167ithout t\151\155e limita\164\151on.","\154mm"),"http:/\057\145\156.wikipedi\141\056\157rg/wiki/\114\151\163t_of_AM\120\137\160ackages"); ?><?php echo "\015\012\011\011</\160\076\015\012\011"; ?><?php endif; ?><?php echo "\015\012\015\012\011<\160\076\015\012\011"; ?><?php if (O9($la=l0,$Oa=FALSE) === TRUE) { if (($lq->Ot) && ($O1r != NULL)) { echo "<p><\163\164\162ong>".__("License registered \164\157","lmm").":</strong> ".$l1r["customer"]["name"]."\074/p>"; } if ((O9($la=FALSE,$Oa=TRUE) === TRUE) && (O9() === TRUE)) { if (!Ox()) { $Ov=$lq->lw["\154icense_expire\163"]; $O1y=abs(floor((time()-$Ov)/(074*074*030))); if ($Ov != NULL) { echo "\074strong>".__("\106ree trial license\040\151s valid unti\154\072","\154\155m")."</strong> ".date("\144/m/Y",$Ov)." (".$O1y." ".__("days left","lmm")."\051\040<span sty\154\145\075\042f\157\156\164-family:s\145\162if;\042>\046\162arr;</span>\040\074a style=\042\164\145xt-deco\162\141tion:none;\042\040href=\042\150\164tps://www.\155\141psmarker.c\157\155\057order\042\040target=\042\137\142lank\042\076".__("click her\145\040\164o get a n\157\156\055expirin\147\040license key","lmm")."\074/a>"; } } else { $Ov=$lq->lw["\144\157\167nload_acce\163\163\137expires"]; $O1y=abs(floor((time()-$Ov)/(074*074*030))); echo "\074\163trong>".__("Access to plugin \165\160\144ates and\040\163upport area v\141\154id until:","\154\155\155")."\074/strong> ".date("d/m/Y",$Ov)." (".$O1y."\040".__("days left","lmm")."\051"; } } else if ((O9($la=FALSE,$Oa=TRUE) === TRUE) && (O9() === FALSE)) { $l5=get_option("l\145\141fletmapsmarker_\166\145\162sion_pro"); echo "\074\144iv id='message' \143\154\141ss='erro\162\047 style='pad\144\151ng:5px;'><s\164\162\157ng>".__("Warning: your ac\143\145\163s to upda\164\145\163 and sup\160\157\162t for \114\145\141flet Ma\160\163 Marker Pro\040\150as expire\144\041","lmm")."\074\057strong><br/\076"; if ($Ob>$l5) { echo __("Latest available v\145\162\163ion:","\154\155\155")." <a href='http://w\167\167\056mapsmarke\162\056\143om/v".$Ob."\160' target='_blank\047\040title='".esc_attr__("c\154\151\143k to show re\154\145\141se notes","\154mm")."\047>".$Ob."\074\057a> "."(<a href='w\167\167\056mapsmarke\162\056\143om/chang\145\154\157g/pro/\047\040target='_b\154\141\156k'>".__("show all ava\151\154\141ble change\154\157\147s","l\155\155")."\074\057a>)<br/>"; } echo sprintf(__("\131ou can continu\145\040\165sing vers\151\157\156 %s with\157\165t any limit\141\164\151ons. Ne\166\145rtheless yo\165\040will not \142\145 able to ge\164\040updates in\143\154uding bugfi\170\145s, new fea\164\165res and op\164\151\155izatio\156\163 as well \141\163 access to\040\157ur suppo\162\164 system. ","lmm"),$l5)."\074/div>"; if (current_user_can("activate_plugins")) { echo "\074a href=\042\150\164tp://www.m\141\160\163marker.co\155\057renew\042\040\164arget=\042\137\142lank\042\040\040style=\042\146\157nt-size:\061\0625%;font-w\145\151\147ht:bol\144\073\042>&raq\165\157; ".__("\160\154\145ase click h\145\162\145 to renew\040\171\157ur acce\163\163 to plugin \165\160\144ates a\156\144\040suppor\164","lm\155")." &la\161\165\157;</a>"; echo "\074p>".__("Important: \160\154\145ase click \164\150\145 update \142\165\164ton nex\164\040to the lice\156\163\145 key a\146\164\145r purch\141\163ing a rene\167\141\154 to fi\156\151\163h your \157\162der.","\154mm")."\074/p>"; } else { echo "<s\160\141\156 style=\042\146\157nt-size:125%\073\146ont-weight:\142\157\154d;\042\076".sprintf(__("\120\154\145ase contact y\157\165\162 administ\162\141\164or (%1s)\040\164o renew you\162\040access to \160\154\165gin upda\164\145s and supp\157\162\164.","\154\155\155"),"<a href\075\042mailto:".get_bloginfo("admin_email")."?subject=".esc_attr__("Maps\040\115arker Pro - rene\167\141\154 for acc\145\163\163 to plu\147\151\156 updat\145\163\040and s\165\160\160ort ne\145\144\145d","lmm")."\042>".get_bloginfo("\141dmin_email")."</a>")."\074/span>"; } } } else if (($lq->Ot) && (O9($la=FALSE,$Oa=TRUE) === TRUE) && (O9($la=l0,$Oa=FALSE) === FALSE)) { if (extension_loaded("\151onCube Loa\144\145\162")) { if (function_exists("\151oncube_loader_\151\166\145rsion")) { $O1o=ioncube_loader_iversion(); $l1p= (int) substr($O1o,0,1); } else { $O1p=ioncube_loader_version(); $l1p= (int) substr($O1p,0,1); } if ($l1p>=4) { $l1z=""; } else { $l1z=strrev("orp-"); } } else { $l1z=strrev("\157\162p-"); } if (current_user_can("\141\143tivate_plugins")) { $O1z="\150ttps://www.maps\155\141\162ker.com/\165\160\144ates".$l1z."/archive"; echo "\074div id='mess\141\147\145' class='\145\162\162or' styl\145\075'padding:5\160\170\073'><st\162\157\156g>".sprintf(__("\105\162\162or: This \166\145\162sion of \164\150\145 plugin \167\141\163 relea\163\145\144 after \171\157\165r downl\157\141\144 acce\163\163\040expire\144\056 Please <\141\040href=\042\045\061\044s\042\040target=\042\137\142lank\042\076\162enew you\162\040download\040\141nd suppor\164\040access</\141\076 or <a hr\145\146=\042%2\044\163\042 ta\162\147et=\042_\142\154ank\042\076\144owngrade\040\164o your p\162\145vious va\154\151d versio\156\074/a>.","lmm"),"\150\164tp://www.maps\155\141\162ker.com/re\156\145\167",$O1z)."\074/strong></div\076"; } else { echo "\074\144iv id='message' \143\154\141ss='erro\162\047\040style\075\047padding:5\160\170\073'><str\157\156g>".sprintf(__("Error: This versio\156\040\157f the pl\165\147\151n was re\154\145\141sed a\146\164\145r your \144\157wnload acc\145\163\163 expir\145\144. Please c\157\156\164act yo\165\162\040admi\156\151\163trator\040\050%1s) to r\145\156ew your a\143\143ess to plu\147\151n update\163\040and suppo\162\164 or to do\167\156grade to \171\157ur previo\165\163 valid ve\162\163ion.","lmm"),"<a href=\042\155\141ilto:".get_bloginfo("admin_email")."?subject=".esc_attr__("Maps Marker Pro - r\145\156\145wal for \141\143\143ess to p\154\165gin updates\040\141nd support \156\145\145ded","\154mm")."\042>".get_bloginfo("\141\144min_email")."</a>")."\074/strong></div\076"; } } ?><?php echo "\015\012\011\074/p>\015\012\015\012\011"; ?><?php if (current_user_can("activate_pl\165\147\151ns")) { if (($lq->Ot) && ($O1r != "\141\156onym@mapsmar\153\145\162.com")) { echo "\074p>".sprintf(__("\111\146 you have any iss\165\145\163 with you\162\040\154icense,\040\074a href=\042\045\061\044s\042\040target=\042\137\142lank\042\076\160lease ope\156\040a new supp\157\162t ticket</a\076\041","lmm"),"\150ttps://www.m\141\160\163marker.com\057\163tore/custome\162\163/index.php?\164\141\163k=logi\156\046email_logi\156\075".$O1r."\042 style=\042te\170\164\055decorati\157\156\072none;")."\074/p>"; } } ?><?php echo "\015\012\015\012\011"; ?><?php if (is_multisite()) { if (current_user_can("activate_plugin\163")) { echo "\074\150\162 noshade si\172\145\075\042\061\042 style=\042\142\157rder-top:1\160\170\040solid \043\06666666;\042\040\057><h3 st\171\154e=\042fo\156\164\055size:1\070\160x;\042>".__("\127ordPress Multisit\145\040\163ettings","lmm")."\074\057h3>"; echo "\074p>".__("Use the butto\156\040below to propa\147\141\164e the li\143\145\156se key\040\145ntered abov\145\040to all Wo\162\144\120ress M\165\154tisite subs\151\164es.","lmm")."\074/p>"; if ((SUBDOMAIN_INSTALL == TRUE) || is_plugin_active("\167ordpress-mu-\144\157\155ain-mappi\156\147\057domain_\155\141\160ping.p\150\160")) { echo "<p>".__("\111\155portant: you see\155\040to be using d\151\146\146erent do\155\141ins for you\162\040subsites. \120\154ease make su\162\145 that you\162\040license ke\171\040is valid f\157\162\040the n\165\155ber of dom\141\151\156s you \167\141\156t to u\163\145 it on a\156\144 update t\150\145\040lice\156\163\145 key o\156\040each sub\163\151te direct\154\171 first be\146\157re propag\141\164ing the\040\154icense k\145\171! This w\151\154l ensure\040\164hat al\154\040these d\157\155ains ar\145\040registe\162\145d on \171\157ur cu\163\164omer pr\157\146ile on\040\155apsmar\153\145r.com \055\040which\040\167ill r\145\163ult in\040\141 vali\144\040licen\163\145 valid\141\164ion o\156\040subsi\164\145s aft\145\162 prop\141\147ating \164\150e li\143\145nse k\145\171.","lm\155")."</p>"; } echo "<form method=\042\160\157st\042>"; wp_nonce_field("\155aps_marker_\160\162\157_license_mu\154\164\151site","\155\141ps_marker_pr\157\137\154icense_mul\164\151\163ite"); echo "\074input type=\042\143\150eckbox\042\040\156ame=\042\155\141\160s_mar\153\145\162_pro_mu\154\164isite_prop\141\147\141te\042\040\057> <labe\154\040for=\042m\141\160s_marker_p\162\157_multisite\137\160\162opagat\145\042>".__("\131es I want to propa\147\141\164e the lic\145\156\163e key to\040\141ll subsites","lmm")."</label>"; echo "\040\074input type=\042\163\165bmit\042\040\143lass=\042b\165\164\164on-pri\155\141\162y\042\040\166alue=\042".__("update","\154mm")."\042 />"; } } ?><?php echo "\015\012</di\166\076\015\012\074\041--wrap-->\015\012"; ?><?php include ("inc".DIRECTORY_SEPARATOR."admin-footer.php"); ?>