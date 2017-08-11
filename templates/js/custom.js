/*
 *
 * @description : Holds the custom JS to the GraceMedia Intergration
 * @author : Curtis Crewe
 * @company : GraceMedia LTD
 *
 */
	//The Current API URL for GraceMedia TV
	var apiURL = "http://api.gracemedia.tv/";
	
	jQuery(document).ready(function(){
    		
    		var Ids = null;
    		
    		jQuery.ajax({
    			type: "GET",
    			url: pluginURL + "/gracemedia-media-player/templates/files/ajax_controller.php",
    			data: "ajaxAction=getIds&cfg=" + configURL + "&rand="+Math.floor(Math.random()*10000),
    			success: function(data) {
    				Ids = jQuery.parseJSON(data);
    				jQuery("#submit").click(function(event) {
    					event.preventDefault();
    					var Username = jQuery('#username').val();
    					if(Username === "") {
    						jQuery("#ajaxResponse").html("<div class='error'><p>Username can't be empty!</p></div>");
    					} else {
    						jQuery.ajax({
    							type: "GET",
    							crossDomain: true,
    							url: apiURL + "video/" + Username + "/views/",
    							success: function(data) {
    								Parsed = jQuery.parseJSON(data);
    								Count = Parsed['user_media'].length;
    								jQuery("#results").html("");
    								if(Count != 0) {
    									jQuery.each(Parsed.user_media, function(i,v){
    										title = v['title'];
  										if(v['title'].length > 20) {
  											title = v['title'].substring(0,30) + "...";
  										}
  										fulltitle = v['title'];
  										id = v['id'];
  										length = videoLength(v['length']);
  										description = v['description'];
  										if(v['description'].length > 20) {
  											description = v['description'].substring(0,20) + "...";
  										}
  										fulldescription = v['description'];
  										views = v['views'];
  										thumbnail = v['thumbnail'];
  										filename = v['filename'];
  										image = v['image'];
    										if(jQuery.inArray(id,Ids['IdList']) !== -1) {
  											jQuery("#results").append("<tr><td>" + title + "</td><td>" + length + "</td><td>" + views + "</td><td>" + description + "</td><td><input type='submit' value='Already added!' class='button' data-id='" + id + "' data-title='" + title + "' data-thumb='" + thumbnail + "' disabled/></td></tr>");
  										} else {
  											jQuery("#results").append("<tr><td>" + title + "</td><td>" + length + "</td><td>" + views + "</td><td>" + description + "</td><td><input type='submit' value='Add to Playlist' class='button' id='addtoPlaylist' data-id='" + id + "' data-title='" + fulltitle + "' data-thumb='" + thumbnail + "' data-length='" + length + "' data-description='" + fulldescription + "' data-file='" + filename + "' data-image='" + image + "'/> <a href='http://www.gracemedia.tv/vault/" + filename + "' class='video button'>Watch</a></td></tr>");
  										}
    									});
    								} else { 
    									jQuery("#results").html("<tr class='no-rows'><td colspan='5'>No Media found for that user!</td></tr>");
    								}
    							}
    						});
    					}
    				});
    			}
    		});
    		
    		jQuery("#results").on("click", "#addtoPlaylist", function(event) {
    			
    			var thisId = jQuery(this).data("id");
    			var thisTitle = jQuery(this).data("title");
    			var thisThumb = jQuery(this).data("thumb");
    			var thisLength = jQuery(this).data("length");
    			var thisDescription = jQuery(this).data("description");
    			var thisFile = jQuery(this).data("file");
    			var thisImage = jQuery(this).data("image");
    			
    			jQuery.ajax({
    				type: "GET",
    				url: pluginURL + "/gracemedia-media-player/templates/files/ajax_controller.php",
    				data: "ajaxAction=AddId&cfg=" + configURL + "&id=" + thisId + "&title=" + thisTitle + "&thumb=" + thisThumb +"&length="+ thisLength + "&description=" + thisDescription + "&filename=" + thisFile + "&image=" + thisImage,
    				success: function(data) {
    					if(data === "success") {
    						
    						var OldId = jQuery("input[data-id='" + thisId + "']");
    						
    						if(OldId.length > 0) {
    							OldId.val("Added!");
    							OldId.attr('disabled', 'disabled');
    						}
    						
    					} else {
    						alert("Stop tampering with the HTML!");
    					}
    				},
    				error: function() {
    					alert("AJAX Call Failed adding ID!");
    				}
    			});
    		});
    		
    		jQuery(".removeId").click(function() {
    			var thisId = jQuery(this).data("id");
    			jQuery.ajax({
    				type: "GET",
    				url: pluginURL + "/gracemedia-media-player/templates/files/ajax_controller.php",
    				data: "ajaxAction=removeId&cfg=" + configURL + "&id=" + jQuery(this).data("id"),
    				success: function(data) {
    					if(data === "success") {
    						var OldId = jQuery("button[data-id='" + thisId + "']");
    						jQuery("#ajaxResponse").html("<div class='updated'><p>Video removed successfully!</p></div>");
    						OldId.closest("tr").remove();
    					} else {
    						jQuery("#ajaxResponse").html("<div class='error'><p>Failed to remove video!</p></div>");
    					}
    				},
    				error: function() {
    					alert("AJAX Call failed on removing ID!");
    				}
    			});
    		});
    		
    		jQuery("#results").on("click", "a.video", function(event) {
    			jQuery.fancybox({
    				'title' : this.title,
    				'content': '<embed src="' + pluginURL + '/gracemedia-media-player/gm_player/player.swf?file='+this.href+'&amp;autostart=true&amp;fs=1&amp;stretching=exactfit" type="application/x-shockwave-flash" width="617" height="324" wmode="opaque" allowfullscreen="true" allowscriptaccess="always"></embed>'               
   			});
   			return false;
   		});
   		
    		jQuery("a.video").click(function() {
   			jQuery.fancybox({
    				'title' : this.title,
    				'content': '<embed src="' + pluginURL + '/gracemedia-media-player/gm_player/player.swf?file='+this.href+'&amp;autostart=true&amp;fs=1&amp;stretching=exactfit" type="application/x-shockwave-flash" width="617" height="324" wmode="opaque" allowfullscreen="true" allowscriptaccess="always"></embed>'               
   			});
   			return false;
  		});
    		
    		function videoLength(secs) {
			var hr = Math.floor(secs / 3600);
			var min = Math.floor((secs - (hr * 3600))/60);
			var sec = secs - (hr * 3600) - (min * 60);
	
			while (min.length < 2) {min = '0' + min;}
			while (sec.length < 2) {sec = '0' + min;}
			if (hr) hr += 'hours, ';
			return hr + min + 'mins and ' + sec + "seconds";
		}
    		
	jQuery(".fancybox").fancybox();});
	
	