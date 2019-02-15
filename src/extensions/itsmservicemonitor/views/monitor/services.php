<?php
	use itsmservicemonitor as sm;
?>
<div class="monitor-countdown">Refreshing in <span id="monitor-countdown-timer"></span> seconds!</div>
<?php
	if($faCurrentUser->hasPermission('itsmmonitor-services-write'))
	{
		?>
		<div class="button-bar">
			<a class="button" href="<?=SITE_URI?>monitor/services/categories">Manage Categories</a>
		</div>
		<?php
	}
?>
<div class="monitor-tile-container">
	<?php
		foreach(sm\getCategories(TRUE) as $category)
		{
		?>
			<div class="monitor-tile" id="service-monitor-<?=$category->getId()?>">
				<h3><?=htmlentities($category->getName())?></h3>
				<img class="monitor-indicator" src="<?=URI_THEME?>itsmmonitor/media/loading.gif" alt="">
				<span class="monitor-notice"></span>
				<ul class="monitor-list">
				</ul>
			</div>
		<?php
		}
	?>
</div>
<script>
/**
* Refresh Timer for Dashboard
*/
(function countdown(remaining) {
    if(remaining === 0)
	{
		veil();
        location.reload(true);
	}
    document.getElementById('monitor-countdown-timer').innerHTML = remaining;
    setTimeout(function(){ countdown(remaining - 1); }, 1000);
})(<?=ITSM_MONITOR_REFRESH?>);

/**
* Load monitor tiles
*/
function loadMonitor(monitor)
{	
	var category = monitor.id.split("-")[2];
	$.ajax({
		url:'<?=SITE_URI?>monitor/services/api?POPUP=yes&category=' + category,
		type:'GET',
		success: function(data)
		{
			var monitorData = JSON.parse($(data).find('#encoded-data').html());
			
			// Generate the offline count list
			var troubleDisplay = $(monitor).find(".monitor-notice")[0];
			troubleDisplay.appendChild(document.createTextNode("Trouble Count: " + monitorData.troubleCount));
			
			// Change indicator
			var indicator = $(monitor).find(".monitor-indicator")[0];
			$(indicator).attr("src", "<?=URI_THEME?>itsmmonitor/media/" + monitorData.indicator);
			
			var appList = $(monitor).find("ul")[0];
			
			// Generate the app list
			$.each(monitorData.applications, function(index, value){
				var app = document.createElement("li");
				app.appendChild(document.createTextNode(value.name));
				
				var status = document.createElement("span");
				
				// Status for WEB, DATA, and APP hosts
				if(value.apph == "1" && value.webh == "1" && value.data == "1")
					status.appendChild(document.createTextNode("Online"));
				else if(value.apph == "0" && value.webh == "0" && value.data == "0")
				{
					$(status).addClass("monitor-app-offline");
					status.appendChild(document.createTextNode("Offline"));
				}
				else
				{
					$(status).addClass("monitor-app-trouble");
					status.appendChild(document.createTextNode("Trouble"));
				}
				
				app.appendChild(status);
				
				appList.appendChild(app);
			});
		}
	});
}

$(document).ready()
{
	$(document).find(".monitor-tile").each(function(index, value){
		loadMonitor(value);
	});
}
</script>