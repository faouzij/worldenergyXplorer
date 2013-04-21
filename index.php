<?php include("OpenWindApi.php") ?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<title>Open Weather Map actual and forecast weather.</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<meta name="keywords" content="weather, world, Openstreetmap, weather, layer, map, weather forecast" />
	<meta name="description" content="Map service with weather maps for everybody. Based on OpenStreetMap. Layers with actual weather data and forecasts are available for any cartographic services. Any weather station can be connected to the service." />
	<meta name="domain" content="OpenWeatherMap.org" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />

	<link rel="shortcut icon" href="http://openweathermap.org//images/sun_mini.png" />
	<link rel="apple-touch-icon" href="http://openweathermap.org//images/sun_mini.png" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="http://openweathermap.org//stylesheets/bootstrap.min.css" rel="stylesheet">
    <link href="http://openweathermap.org//stylesheets/2.0.4/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="http://openweathermap.org//stylesheets/main.css" rel="stylesheet">

    <!-- Le javascript -->
    <script src="http://code.jquery.com/jquery-1.7.min.js" ></script>
    <script src="http://openlayers.org/api/OpenLayers.js"></script>
    <script src="http://openweathermap.org/js/OWM.OpenLayers.1.3.5.js" ></script>
<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>

    <style type="text/css">
#basicMap {
    width: 100%;
    height: 100%;
    padding: 0px;
    position: absolute;
}


#mylinks {
    background: #575757;
    color: white;
    z-index:2000;
    width: 30%;
    font-size: 1em;
    text-align: left;
    position: absolute;
    bottom: 0.2em;
    left: 0.2em;
    padding: 5px;
    width: 33%;
    /* for IE */
    filter:alpha(opacity=90);
    /* CSS3 standard */
    opacity:0.9;
    border-radius: 4px;
}


#mylinksRight {
    background: #575757;
    color: white;
    z-index:1000;
    font-size: 1em;
    text-align: left;
    position: absolute;
	right: 3px;
    bottom: 0.2em;
    padding: 5px;
    width: 400px;
    /* for IE */
    filter:alpha(opacity=90);
    /* CSS3 standard */
    opacity:0.9;
    border-radius: 4px;
}

#myAnnotRight {
    background: #575757;
    color: white;
    z-index:1000;
    font-size: 1em;
    text-align: left;
    position: absolute;
	right: 3px;
    bottom: 40px;
    padding: 4px;
    /* for IE */
    filter:alpha(opacity=90);
    /* CSS3 standard */
    opacity:0.9;
    border-radius: 4px;
}

.olControlLayerSwitcher .layersDiv {
    background-color:#575757 !important;

    /* for IE */
    filter:alpha(opacity=90);
    /* CSS3 standard */
    opacity:0.9;
    border-radius: 4px;
    color: white;

    font-family: sans-serif;
    font-size: smaller;  
    font-weight: bold;
}

.olControlAttribution {
    background: #575757;
    color: white;
    z-index:1000;
    font-size: 1em;
    text-align: left;
    position: absolute;
    right: 3px;
    bottom: 0.2em;
    padding: 4px;
    /* for IE */
    filter:alpha(opacity=90);
    /* CSS3 standard */
    opacity:0.9;
    border-radius: 4px;
}


/*hack*/
.olButton {
	color: white;
	font-family: arial;  
	display: inline;
}

a:link, a:visited, a:hover, a:active {
    color: #CCCCCC;
}
</style>


<script type="text/javascript" charset="utf-8">

function ShowSuccessMess(mess)
{
	var html = '<div class="alert alert-success" ><a class="close" data-dismiss="alert" href="#">&times;</a>'+mess+'</div>';
	$("#alert_body").html(html);
}

function ShowInfoMess(mess)
{
	var html = '<div class="alert alert-info" ><a class="close" data-dismiss="alert" href="#">&times;</a>'+mess+'</div>';
	$("#alert_body").html(html);
}

function ShowAlertMess(mess)
{
	var html = '<div class="alert alert-error" ><a class="close" data-dismiss="alert" href="#">&times;</a>'+mess+'</div>';
	$("#alert_body").html(html);
}

function  errorHandler(e)
{
	ShowAlertMess(e.status +' '+e.statusText);
}


function ParseJson(JSONtext)
{
	try{
		JSONobject = JSON.parse(JSONtext); 
	}catch(e){
        console.log(JSONtext);
		ShowAlertMess('JSON Eroor');
		return;
	}

	if(JSONobject.cod != '200') {
		ShowAlertMess('Error code '+ JSONobject.cod + ' ('+ JSONobject.message +')');
		return;
	}
	var mes = JSONobject.cod;
	if(JSONobject.calctime)
		mes = mes + ' ' + JSONobject.calctime;
	if(JSONobject.message)
		mes = mes + ' ' + JSONobject.message;
	console.log( mes );
	return JSONobject;
}

function ShowCalcTime(mess)
{
	$("#stat").html(mess);
}

function set_cookie(name, value, expires)
{
if (!expires)
{
expires = new Date();
}
document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path=/";
}

function set_lang(lang)
{
	expires = new Date();					
	expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24)); // ????????? ???? ???????? cookie ? 24 ???
	set_cookie('lang', lang, expires);
	window.location.reload();
}
else	$("#img_PR").hide();
</script>

</head>

<body>


    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">

          <a class="brand" href="/">World Renewable Energy Xplorer <span class="label label-warning">Alpha</span></a>

          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="thermal.zip">Download Thermal Data</a></li>
              
<!--              <li><a href="/api">API</a></li> -->

              
         <input type="text" id="x" value="Latitude, Longitude Values"  width="150" size="150" />
         <input type="text" id="result" value="Click For Results"  width="150" size="150" />
<!--              <li class="active"><a href="/blog">Blog</a></li> -->
	


            </ul>
          </div><!--/.nav-collapse -->

<!--

<form action="/map" method="get" enctype="multipart/form-data" class="navbar-search pull-right">
 &nbsp;<img src="/images/flags/gb.png" alt="English" title="English" onclick="set_lang('en');" />
&nbsp;<img src="/images/flags/ru.png" alt="Russian" title="Russian" onclick="set_lang('ru');" />
</form>
-->
        </div><!--/container -->

<div id="stat" class="pull-right"></div>

      </div>
    </div>

<hr/>



<div id="basicMap"></div>
<div class="olControlAttribution olControlNoSelect" style="" id="myAnnotRight">
<img src="http://openweathermap.org/img/a/RADAR.2KM.png" id="img_RADAR">
<img src="http://openweathermap.org/img/a/NT.png" id="img_NT">
<br />
<img src="world/files/heatflow-legend_0_0.png" id="img_PR" style="margin: auto; padding-top: 10;">
<img src="world/files/tomographie.JPG" id="img_PR2" style="margin: auto; padding-top: 10;">
</div>


<script type="text/javascript">
function addMarker(x, y){    
    markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(x, y),icon));
}

var map;


   OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
                defaultHandlerOptions: {
                    'single': true,
                    'double': false,
                    'pixelTolerance': 0,
                    'stopSingle': false,
                    'stopDouble': false
                },

                initialize: function(options) {
                    this.handlerOptions = OpenLayers.Util.extend(
                        {}, this.defaultHandlerOptions
                    );
                    OpenLayers.Control.prototype.initialize.apply(
                        this, arguments
                    ); 
                    this.handler = new OpenLayers.Handler.Click(
                        this, {
                            'click': this.trigger
                        }, this.handlerOptions
                    );
                }, 

                trigger: function(e) {
                     var toProjection = new OpenLayers.Projection("EPSG:4326");
                     var lonlat = map.getLonLatFromPixel(e.xy).transform(map.getProjectionObject(), toProjection);
                     var x = lonlat.lat.toFixed(3);
                     var y = lonlat.lon.toFixed(3);
                     var zoom = map.getZoom();
                     //alert(zoom);
                    $("#x").val(x+" N, "+y+" E");
                     jQuery.post("worker.php",{step:1,x:x,y:y,zoom:zoom},function(data){
         //alert(data);
            if (data!=0){
                $("#result").val(data);
            }
            else{
                $("#result").val("No Data For The Selected Region");
            }
           });
                }

            });

jQuery(document).ready( function() {
    $("#img_PR").hide();


    map = new OpenLayers.Map("basicMap",
		{
      		  units: 'm',
		        projection: new OpenLayers.Projection("EPSG:EPSG:4326"),
		        displayProjection: new OpenLayers.Projection("EPSG:4326")
		}
	);

    var mapnik = new OpenLayers.Layer.OSM();
	var opencyclemap = new OpenLayers.Layer.XYZ(
		"opencyclemap",
		"http://a.tile3.opencyclemap.org/landscape/${z}/${x}/${y}.png",
		{
			numZoomLevels: 18, 
			sphericalMercator: true
		}
	);
    
    var gphy = new OpenLayers.Layer.Google(
    "Google Physical",
    {type: google.maps.MapTypeId.TERRAIN}
    // used to be {type: G_PHYSICAL_MAP}
);
var gmap = new OpenLayers.Layer.Google(
    "Google Streets", // the default
    {numZoomLevels: 20}
    // default type, no change needed here
);
var ghyb = new OpenLayers.Layer.Google(
    "Google Hybrid",
    {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
    // used to be {type: G_HYBRID_MAP, numZoomLevels: 20}
);
var gsat = new OpenLayers.Layer.Google(
    "Google Satellite",
    {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
    // used to be {type: G_SATELLITE_MAP, numZoomLevels: 22}
);

	var stations = new OpenLayers.Layer.Vector.OWMStations("Stations information" );
	stations.setVisibility(false);

	var city = new OpenLayers.Layer.Vector.OWMWeather("Current weather");

	var precipitation = new OpenLayers.Layer.XYZ(
		"Precipitation forecasts",
		"http://${s}.tile.openweathermap.org/map/precipitation/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.6,
			sphericalMercator: true
		}
	);
    precipitation.setVisibility(false);
    
    
    	var wind = new OpenLayers.Layer.XYZ(
		"Wind Speed",
		"http://${s}.tile.openweathermap.org/map/wind/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.3,
			sphericalMercator: true
		}
	);
    wind.setVisibility(false);
    
    	var temp = new OpenLayers.Layer.XYZ(
		"Temperature",
		"http://${s}.tile.openweathermap.org/map/temp/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.6,
			sphericalMercator: true
		}
	);
    temp.setVisibility(false);
    
    
    usageo = new OpenLayers.Layer.Vector("USA Geothermal Data", {
            projection: map.displayProjection,
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "doc.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true,
                    extractAttributes: true
                })
            })
        });
        usageo.events.register('visibilitychanged', precipitation, function (e) {    
		if(usageo.getVisibility())	$("#img_PR").show();
		else	$("#img_PR2").hide();
	}); 
     var switzelandgeo = new OpenLayers.Layer.Vector("Switzerland Geothermal Data", {
            projection: map.displayProjection,
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "doc2.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true,
                    extractAttributes: true
                })
            })
        });
        
             var worldgeo = new OpenLayers.Layer.Vector("World Geothermal Data", {
            projection: map.displayProjection,
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "world/doc.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true,
                    extractAttributes: true
                })
            })
        });
        
        worldgeo.setVisibility(false);
        
         var don = new OpenLayers.Layer.Vector("Canada Geothermal Data", {
            projection: map.displayProjection,
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "doc3.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true,
                    extractAttributes: true
                })
            })
        });
        worldgeo.events.register('visibilitychanged', precipitation, function (e) {    
		if(worldgeo.getVisibility())	$("#img_PR").show();
		else	$("#img_PR").hide();
	}); 
    	
    
    
	precipitation.events.register('visibilitychanged', precipitation, function (e) {    
		if(precipitation.getVisibility())	$("#img_PR").show();
		else	$("#img_PR").hide();
	}); 

	var clouds = new OpenLayers.Layer.XYZ(
		"Clouds forecasts",
		"http://${s}.tile.openweathermap.org/map/clouds/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.7,
			sphericalMercator: true

		}
	);
	clouds.setVisibility(false);
	$("#img_NT").hide();
	clouds.events.register('visibilitychanged', clouds, function (e) {    
		if(clouds.getVisibility())	$("#img_NT").show();
		else	$("#img_NT").hide();
	}); 

	var pressure_contour = new OpenLayers.Layer.XYZ(
		"my pressure",
		"http://${s}.tile.openweathermap.org/map/pressure_cntr/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.4,
			sphericalMercator: true

		}
	);
    
	pressure_contour.setVisibility(false);
	var radar = new OpenLayers.Layer.OWMRadar( "Radar (USA and Canada)",{isBaseLayer: false, opacity: 0.6} );
	radar.setVisibility(false);
	$("#img_RADAR").hide();
	radar.events.register('visibilitychanged', radar, function (e) {    
		if(radar.getVisibility())	$("#img_RADAR").show();
		else	$("#img_RADAR").hide();
	}); 

	map.addLayers([ gmap,gsat,ghyb,temp,switzelandgeo ,usageo,don ,worldgeo, gphy]);

    	map.addLayer(wind);
    
    
	//map.addLayer(radar);
     var toProjection = new OpenLayers.Projection("EPSG:4326");
      var lonlat2 = new OpenLayers.LonLat(31.59,-8.784211).transform(map.getProjectionObject(), toProjection);





//markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(0,0),icon.clone()));

	// need for permalink
	var args = OpenLayers.Util.getParameters();
        if (args.lat && args.lon && args.zoom) {
		var position = new OpenLayers.LonLat(parseFloat(args.lon), parseFloat(args.lat));
		if(args.lon< 181 && args.lat < 181)
			position.transform(
			    new OpenLayers.Projection("EPSG:4326"),
			    new OpenLayers.Projection("EPSG:900913")
			);

		map.setCenter(position, parseFloat(args.zoom));
        } else {
		var lat = 37, lon = 1;
		var centre = new OpenLayers.LonLat(lon, lat);
		centre.transform(
		    new OpenLayers.Projection("EPSG:4326"),
		    new OpenLayers.Projection("EPSG:900913")
		);
	        map.setCenter( centre, 5);
        }


	// Layers switcher
	var ls = new OpenLayers.Control.LayerSwitcher({'ascending':false});
	map.addControl(ls);
	ls.maximizeControl();

	map.addControl(new OpenLayers.Control.Permalink('permalink'));


	// Activate Popup windows 
	selectControl = new OpenLayers.Control.SelectFeature([stations, city]);
	map.addControl(selectControl);
	selectControl.activate(); 
 var click = new OpenLayers.Control.Click();
                map.addControl(click);
                click.activate();
	//Save cookie
	map.events.register('moveend', map, function (e) {    
		var longlat = map.getCenter();
		longlat.transform(
			new OpenLayers.Projection("EPSG:900913"), 
			new OpenLayers.Projection("EPSG:4326")
		);
		expires = new Date();					
		expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24 * 7));	
		set_cookie('lat', longlat.lat, expires);	
		set_cookie('lng', longlat.lon, expires);	
		set_cookie('zoom', map.getZoom(), expires);	

	}); 

}

);

function getSearchData(JSONtext)
{
	console.log( JSONtext  );
	JSONobject = ParseJson(JSONtext);

	city = JSONobject.list;
	if( city.length == 0 ) {
		ShowAlertMess( 'not found' );
		return;
	}

	var centre = new OpenLayers.LonLat(city[0].coord.lon, city[0].coord.lat);
	centre.transform(
		    new OpenLayers.Projection("EPSG:4326"),
		    new OpenLayers.Projection("EPSG:900913")
	);
	map.setCenter( centre, 10);
	
//	alert(JSONobject.cod);
}

function FindCity()
{
	param = document.getElementById('query_str').value;
	
	var jsonurl = "/data/2.1/find/name?q="+param;
	$.get(jsonurl, getSearchData).error(errorHandler);
	return false;	
}

</script>



</body>


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-31601618-1']);
  _gaq.push(['_setDomainName', 'openweathermap.org']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</html>
