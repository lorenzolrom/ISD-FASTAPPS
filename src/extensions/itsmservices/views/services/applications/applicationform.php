<?php
	if(!isset($_POST['appHosts']) OR !is_array($_POST['appHosts']))
		$_POST['appHosts'] = [];
	if(!isset($_POST['webHosts']) OR !is_array($_POST['webHosts']))
		$_POST['webHosts'] = [];
	if(!isset($_POST['vhosts']) OR !is_array($_POST['vhosts']))
		$_POST['vhosts'] = [];
	if(!isset($_POST['dataHosts']) OR !is_array($_POST['dataHosts']))
		$_POST['dataHosts'] = [];
?>
<form class="table-form form" method="post" id="application-form">
	<h2 class="region-title">Application Profile</h2>
	<table class="table-display application-display">
		<tr>
			<td class="required">Name</td>
			<td><input type="text" name="name" maxlength=64 value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
			<td class="required">Owner Username</td>
			<td><input type="text" name="ownerUsername" value="<?=htmlentities(ifSet($_POST['ownerUsername']))?>"></td>
		</tr>
		<tr>
			<td class="required">Application Type</td>
			<td>
				<select name="applicationType">
					<option value="">--SELECT--</option>
				<?php
					foreach(getAttributes('itsm', 'aitt') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=($attribute->getId() == ifSet($_POST['applicationType'])) ? " selected" : ""?>><?=htmlentities($attribute->getName())?></option>
						<?php
					}
				?>
				</select>
			</td>
			<td class="required">Life Expectancy</td>
			<td>
				<select name="lifeExpectancy">
					<option value="">--SELECT--</option>
				<?php
					foreach(getAttributes('itsm', 'aitl') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=($attribute->getId() == ifSet($_POST['lifeExpectancy'])) ? " selected" : ""?>><?=htmlentities($attribute->getName())?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Authentication Type</td>
			<td>
				<select name="authType">
					<option value="">--SELECT--</option>
				<?php
					foreach(getAttributes('itsm', 'aita') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=($attribute->getId() == ifSet($_POST['authType'])) ? " selected" : ""?>><?=htmlentities($attribute->getName())?></option>
						<?php
					}
				?>
				</select>
			</td>
			<td>App Host Server(s)</td>
			<td>
				<select name="appHosts[]" multiple size=3>
				<?php
					foreach(itsmcore\getHosts() as $host)
					{
						?>
						<option value="<?=$host->getId()?>"<?=in_array($host->getId(), $_POST['appHosts']) ? " selected" : ""?>><?=htmlentities($host->getSystemName()) . " (" . htmlentities($host->getIpAddress()) . ")"?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Description</td>
			<td colspan=3><textarea name="description"><?=htmlentities(ifSet($_POST['description']))?></textarea></td>
		</tr>
	</table>
	<h2 class="region-title">Web Details</h2>
	<table class="table-display application-display">
		<tr>
			<td class="required">Public Facing</td>
			<td>
				<select name="publicFacing">
					<option value="0">No</option>
					<option value="1"<?=ifSet($_POST['publicFacing']) == 1 ? " selected" : ""?>>Yes</option>
				</select>
			</td>
			<td>Port</td>
			<td><input type="text" name="port" maxlength=5 value="<?=htmlentities(ifSet($_POST['port']))?>"></td>
		</tr>
		<tr>
			<td>Web Host Server(s)</td>
			<td>
				<select name="webHosts[]" multiple size=3>
				<?php
					foreach(itsmcore\getHosts() as $host)
					{
						?>
						<option value="<?=$host->getId()?>"<?=in_array($host->getId(), $_POST['webHosts']) ? " selected" : ""?>><?=htmlentities($host->getSystemName()) . " (" . htmlentities($host->getIpAddress()) . ")"?></option>
						<?php
					}
				?>
				</select>
			</td>
			<td>VHost(s)</td>
			<td>
				<select name="vhosts[]" multiple size=3>
				<?php
					foreach(itsmwebmanager\getVhosts() as $vhost)
					{
						?>
						<option value="<?=$vhost->getId()?>"<?=in_array($vhost->getId(), $_POST['vhosts']) ? " selected" : ""?>><?=htmlentities($vhost->getSubdomain()) . "." . htmlentities($vhost->getDomain())?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
	</table>
	<h2 class="region-title">Data Details</h2>
	<table class="table-display application-display">
		<tr>
			<td>Data Host Server(s)</td>
			<td>
				<select name="dataHosts[]" multiple size=3>
				<?php
					foreach(itsmcore\getHosts() as $host)
					{
						?>
						<option value="<?=$host->getId()?>"<?=in_array($host->getId(), $_POST['dataHosts']) ? " selected" : ""?>><?=htmlentities($host->getSystemName()) . " (" . htmlentities($host->getIpAddress()) . ")"?></option>
						<?php
					}
				?>
				</select>
			</td>
			<td class="required">Data Volume</td>
			<td>
				<select name="dataVolume">
					<option value="">--SELECT--</option>
				<?php
					foreach(getAttributes('itsm', 'aitd') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=($attribute->getId() == ifSet($_POST['dataVolume'])) ? " selected" : ""?>><?=htmlentities($attribute->getName())?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
	</table>
</form>