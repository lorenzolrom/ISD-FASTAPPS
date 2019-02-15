<?php
	use itsmmonitor as itsmmonitor;
?>
<div class="monitor-countdown">Refreshing in <span id="monitor-countdown-timer"></span> seconds!</div>
<?php
	if($faCurrentUser->hasPermission('itsmmonitor-hosts-write'))
	{
		?>
		<div class="button-bar">
			<a class="button" href="<?=SITE_URI?>monitor/hosts/categories">Manage Categories</a>
		</div>
		<?php
	}
?>
<div class="monitor-tile-container">
	<?php
		foreach(itsmmonitor\getCategories(TRUE) as $category)
		{
		?>
			<div class="monitor-tile" id="host-monitor-<?=$category->getId()?>">
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
		url:'<?=SITE_URI?>monitor/hosts/api?POPUP=yes&category=' + category,
		type:'GET',
		success: function(data)
		{
			var monitorData = JSON.parse($(data).find('#encoded-data').html());
			
			// Generate the offline count list
			var offlineDisplay = $(monitor).find(".monitor-notice")[0];
			offlineDisplay.appendChild(document.createTextNode("Hosts Offline: " + monitorData.offlineCount));
			
			// Change indicator
			var indicator = $(monitor).find(".monitor-indicator")[0];
			$(indicator).attr("src", "<?=URI_THEME?>itsmmonitor/media/" + monitorData.indicator);
			
			var hostList = $(monitor).find("ul")[0];
			
			// Generate the host list
			$.each(monitorData.hosts, function(index, value){
				var host = document.createElement("li");
				host.appendChild(document.createTextNode(value.name));
				
				var status = document.createElement("span");
				
				if(value.online == "1")
					status.appendChild(document.createTextNode("Online"));
				else
				{
					$(status).addClass("monitor-host-offline");
					status.appendChild(document.createTextNode("Offline"));
				}
				
				host.appendChild(status);
				
				hostList.appendChild(host);
			});
		}
	});
}

$(document).ready()
{
	$(document).find(".monitor-tile").each(function(index, value){
		loadMonitor(value);
	});
	
	$('#view').css('background-image', 'url(<?=URI_THEME?>itsmmonitor/media/globe.jpg)');
	$('#view').css('background-size', '100% auto');
	$('#view').css('background-repeat', 'no-repeat');
}
</script>