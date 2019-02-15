<form class="basic-form form" method="post" id="assettype-form">
	<p>
		<span class="required">Code</span>
		<input type="text" autocomplete="off" name="code" maxlength=4 value="<?=htmlentities(ifSet($_POST['code']))?>">
	</p>
	<p>
		<span class="required">Name</span>
		<input type="text" autocomplete="off" name="name" maxlength=30 value="<?=htmlentities(ifSet($_POST['name']))?>">
	</p>
</form>