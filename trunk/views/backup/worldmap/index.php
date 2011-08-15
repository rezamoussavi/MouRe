<?php
	/*
	*	This file provided for VIEWS project
	*	to show video views distribution on the world map
	*/
	include_once "../db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;

	if ($osdbcon && isset($_GET['video'])){
		mysql_select_db($DataBase,$osdbcon);
		$video=$_GET['video'];
		$title=$_GET['title'];
		query("SELECT * FROM publink_stat WHERE adUID=".$video);
		$html="";
		$min=-1;
		$max=-1;
		while($row=fetch()){
			$html.="'".$row['countryCode']."': {'c': '".$row['views']."', 'n':'".$row['countryName']."'},";
			if($min<0)$min=$row['views'];
			else if($row['views']<$min) $min=$row['views'];
			if($max<0)$max=$row['views'];
			else if($row['views']>$max) $max=$row['views'];
		}
		if($html!=""){
			$html.="{X}";
			$html=str_replace(",{X}","",$html);
		}
	}

	/*
	*	Database functions
	*/
	function query($s){
		global $osdbcon,$result;
		$result= $osdbcon? mysql_query($s,$osdbcon):false;
		return $result;
	}
	function fetch(){
		global $result;
		if(!$result) return false;
		return mysql_fetch_assoc($result);
	}
?>
<!doctype html> 
<html> 
<head> 
	<title>Statistics</title> 
	<script src="scripts/raphael.js" type="text/javascript"></script> 
	<script src="scripts/jquery.js" type="text/javascript"></script> 
	<script src="scripts/worldpaths.js" type="text/javascript"></script> 	

	<style type="text/css"> 
		html, body {padding: 0; margin: 0; }
		body {	background: #333;
				color: #fff;
				font-family: Tahoma;  }
		
		h1 { font-size: 1em; display: none }
		p {font-size: 0.8em; padding: 3px 10px}
		.footer { position: absolute; bottom: 10px; right: 10px}
		#mapHolder {width: 980px; height: 350px; margin: 70px auto 0 auto; }
		
	</style> 
	
	<script type="text/javascript"> 
		var visitedCountries = {<?=$html?>};
		var choropletshValues = {
            '0' : '#99FF99',
            '1' : '#99FF99',
			'2' : '#66FF66',
			'3' : '#33CC33',
			'4' : '#009933'};
			
		var minVisits = <?=$min?>;
		var maxVisits = <?=$max?>;	
		
		$(document).ready(function()
		{
			function lon2x(lon) 
			{
				var xfactor = 2.6938;
				var xoffset = 465.4;
				var x = (lon * xfactor) + xoffset;
				return x;
			}
			function lat2y(lat) 
			{
				var yfactor = -2.6938;
				var yoffset = 227.066;
				var y = (lat * yfactor) + yoffset;
				return y;
			}
			
			function assignedColor(visits){
				var scale = Math.ceil((maxVisits - minVisits) / 4);
				var normalized = Math.floor( (visits - minVisits) / scale );
				return choropletshValues[normalized];
			}
			
			var paper = Raphael('mapHolder');
			var map = getPaths(paper, { fill: "#333", stroke: "#666", "stroke-width": .5, "stroke-linejoin": "round" });
			var galleriaThemeLoaded = false;
			var blackShim;
			
			for (var countryCode in map) {							        
    	        
	            (function (countryPath, countryCode) 
	            {
						
					if (visitedCountries[countryCode])
					{
						countryPath.color = Raphael.getColor();
						var relativeColor = assignedColor(visitedCountries[countryCode].c);
						countryPath.attr({fill: relativeColor} );
						countryPath[0].style.cursor = "pointer";
						countryPath[0].onmouseover = function() 
						{
							document.getElementById('country').innerHTML = visitedCountries[countryCode].n+" ("+visitedCountries[countryCode].c+")";
							countryPath.animate({fill: countryPath.color, stroke: countryPath.color }, 300);
							paper.safari();
						};
						countryPath[0].onmouseout = function() 
						{
							document.getElementById('country').innerHTML = "";
							countryPath.animate({fill: relativeColor, stroke: countryPath.color }, 300);
							paper.safari();
						};
						
							
					}else{
					countryPath.attr({opacity: 0.6});
						countryPath.color = Raphael.getColor();
						
						countryPath[0].onmouseover = function() 
						{
							countryPath.animate({fill: countryPath.color, stroke: countryPath.color }, 300);
							paper.safari();
						};
						countryPath[0].onmouseout = function() 
						{
							countryPath.animate({fill: "#333", stroke: "#666"}, 300);
							paper.safari();
						};
	
					}
				})(map[countryCode], countryCode);
			}; 
			
			function showPopup(country, colour)
			{
				$('#imageGalleryHolder span').html(visitedCountries[country].n);
				
				if (!galleriaThemeLoaded)
				{
					Galleria.loadTheme('/scripts/galleria/src/themes/classic/galleria.classic.js');
					blackShim = paper.rect(0, 0, $('#mapHolder').width(), $('#mapHolder').height()).attr({fill: '#000', stroke: 'none', opacity: 0.8}).hide();
					galleriaThemeLoaded = true;
				}
				var data = [];
				if (imageData[country])
				{
					data = imageData[country];
				}
				$('#imageGallery').galleria({	data_source: data });
				$('#imageGalleryHolder').show();
				
				blackShim.show();
			}
			
			function closePopup()
			{
				$('#imageGalleryHolder').hide();
				blackShim.hide();
			}
				
			$('#closeImageGallery').click(function()
			{
				closePopup();
			});
		});
	</script> 
</head> 
<body> 
	<h1>Countries I have been to.</h1> 
	
	<div id="mapHolder"></div> 
	
	<p class="footer"><strong><?=$title?></strong></p> 
	<div id="imageGalleryHolder"> 
		<p><span id='country'></span></p> 
		<div id="closeImageGallery" class="galleria-info-close" style="display: block;"></div> 
		<div id="imageGallery" style="width:500px; height:300px;"></div> 
	</div> 
</body> 
</html>