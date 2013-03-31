<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function($){
		$('.meta-box-sortables .head-wrap').click(function(){
			$(this).next().toggle();
			return false;
		}).next().hide();
		
		// Toggle all
		$('.toggle-all').click(function(){
			$('.meta-box-sortables .head-wrap').next().toggle();
			return false;
		}).next().hide();
		
		// New social media icon fields
		count = 0;
		$('#social-media-new').click(function(){
			$('.social-media-table tbody').append('\
					<tr class="new-social-media new-social-media-name">\
						<th scope="row"><label><?php _e('Social Media name', 'graphene'); ?></label></th>\
						<td>\
							<input type="text" name="graphene_settings[social_media_new]['+count+'][name]" value="" size="60" class="widefat code" /><br />\
							<span class="description"><?php _e('Name of the social media, e.g. LinkedIn, etc.', 'graphene'); ?></span>\
						</td>\
					</tr>\
					<tr class="new-social-media">\
						<th scope="row"><label><?php _e('Social Media profile URL', 'graphene'); ?></label></th>\
						<td>\
							<input type="text" name="graphene_settings[social_media_new]['+count+'][url]" value="" size="60" class="widefat code" /><br />\
							<span class="description"><?php _e('URL to your page for the social media.', 'graphene'); ?></span>\
						</td>\
					</tr>\
					<tr class="new-social-media">\
						<th scope="row"><label><?php _e('Social Media icon URL', 'graphene'); ?></label></th>\
						<td>\
							<input type="text" name="graphene_settings[social_media_new]['+count+'][icon]" value="" size="60" class="widefat code" /><br />\
							<span class="description"><?php printf(__('URL to the social media icon. <strong>Note:</strong> the theme uses the %s icon set for the social media icons. Please do not hotlink the icons on the site. Download the icons you need and upload them to your server instead.', 'graphene'), '<a href="http://www.iconfinder.com/search/?q=iconset%3Asocialmediabookmark">Social Media Bookmark</a>'); ?></span>\
						</td>\
					</tr>\
			');
			count++;
			return false;
		});
		
		// Delete social media
		$('.social-media-del').click(function(){
			
			social_media = $(this).attr('id');
			social_media = social_media.replace('del', 'opt');
			social_media = '.'+social_media;
			$(social_media).css('background-color', '#A61C09');
			$(social_media).remove();
			
			return false;	
		});
		
		
		<?php if ( strstr( $_SERVER["REQUEST_URI"], 'tab=display' ) ) : ?>
		/* Farbtastic colour picker */ 
		<?php for ($i = 1; $i < 22; $i++) : ?>
		$('#colorpicker-<?php echo $i; ?>').hide();
		color_<?php echo $i; ?> = $.farbtastic('#colorpicker-<?php echo $i; ?>', ".color-<?php echo $i; ?>");
		$(".color-<?php echo $i; ?>").focusin(function(){$('#colorpicker-<?php echo $i; ?>').show()});
		$(".color-<?php echo $i; ?>").focusout(function(){$('#colorpicker-<?php echo $i; ?>').hide()});
		<?php endfor; ?>
		$('.clear-color').click(function(){
			$(this).prev().attr('value', '');
			$(this).prev().removeAttr('style');
			return false;
		});
		<?php endif; ?>
		
		// The widget background preview
		$('#colorpicker-8 div, #colorpicker-9 div, #colorpicker-10 div, #colorpicker-11 div, #colorpicker-12 div, .color-8, .color-9, .color-10, .color-11, .color-12').bind('mouseup keyup', function(){
			$('.sidebar-wrap h3').attr('style', '\
				background: ' + color_12.color + ';\
				background: -moz-linear-gradient(' + color_12.color + ', ' + color_11.color + ');\
				background: -webkit-linear-gradient(' + color_12.color + ', ' + color_11.color + ');\
				background: linear-gradient(' + color_12.color + ', ' + color_11.color + ');\
				border-color: ' + color_8.color + ';\
				color: ' + color_9.color + ';\
				text-shadow: 0 -1px 0 ' + color_10.color + ';\
			');
		});
		$('#colorpicker-6 div, .color-6').bind('mouseup keyup', function(){
			$('.sidebar-wrap').attr('style', 'background: ' + color_6.color + ';');
		});
		$('#colorpicker-7 div, .color-7').bind('mouseup keyup', function(){
			$('.sidebar ul li').attr('style', 'border-color: ' + color_7.color + ';');
		});
		
		// The slider background preview
		$('#colorpicker-13 div, #colorpicker-14 div, .color-13, .color-14').bind('mouseup keyup', function(){
			$('#grad-box').attr('style', '\
				background: ' + color_13.color + ';\
				background: linear-gradient(left top, ' + color_13.color + ', ' + color_14.color + ');\
				background: -moz-linear-gradient(left top, ' + color_13.color + ', ' + color_14.color + ');\
				background: -webkit-linear-gradient(left top, ' + color_13.color + ', ' + color_14.color + ');\
			');
		});
		
		// Block button preview
		$('#colorpicker-15 div, #colorpicker-16 div, #colorpicker-17 div, .color-15, .color-16, .color-17').bind('mouseup keyup', function(){
			
			R = hexToR(color_15.color) - 26;
			G = hexToG(color_15.color) - 26;
			B = hexToB(color_15.color) - 26;
			color_bottom = 'rgb(' + R + ', ' + G + ', ' + B + ')';
			
			$('.block-button').attr('style', '\
					background: ' + color_15.color + ';\
					background: -moz-linear-gradient(' + color_15.color + ', ' + color_bottom + ');\
					background: -webkit-linear-gradient(' + color_15.color + ', ' + color_bottom + ');\
					background: linear-gradient(' + color_15.color + ', ' + color_bottom + ');\
					border-color: ' + color_bottom + ';\
					text-shadow: 0 -1px 1px ' + color_17.color + ';\
					color: ' + color_16.color + ';\
			');
		});
	
	});
	
	function hexToR(h) {
		if ( h.length == 4 )
			return parseInt((cutHex(h)).substring(0,1)+(cutHex(h)).substring(0,1),16);
		if ( h.length == 7 )
			return parseInt((cutHex(h)).substring(0,2),16);
	}
	function hexToG(h) {
		if ( h.length == 4 )
			return parseInt((cutHex(h)).substring(1,2)+(cutHex(h)).substring(1,2),16);
		if ( h.length == 7 )
			return parseInt((cutHex(h)).substring(2,4),16);
	}
	function hexToB(h) {
		if ( h.length == 4 )
			return parseInt((cutHex(h)).substring(2,3)+(cutHex(h)).substring(2,3),16);
		if ( h.length == 7 )
			return parseInt((cutHex(h)).substring(4,6),16);
	}
	function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}	
	//]]>
</script>