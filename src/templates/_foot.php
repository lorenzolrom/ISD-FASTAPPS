				</div>
			</div>
			<?php
				if(!isset($_GET['POPUP']))
					require_once(dirname(__FILE__) . "/footer.php");
				else
					require_once(dirname(__FILE__) . "/footer-popup.php");
				
				$conn->close();
			?>
		</div>
	</body>
</html>