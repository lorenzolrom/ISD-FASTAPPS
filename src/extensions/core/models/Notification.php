<?php
	/**
	* Representation of a notification
	*/
	class Notification
	{
		private $id;
		private $user;
		private $title;
		private $data;
		private $read;
		private $deleted;
		private $important;
		private $time;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		public function getId(){return $this->id;}
		public function getUser(){return $this->user;}
		public function getData(){return $this->data;}
		public function getTitle(){return $this->title;}
		public function getRead(){return $this->read;}
		public function getDeleted(){return $this->deleted;}
		public function getImportant(){return $this->important;}
		public function getTime(){return $this->time;}
		
		public function setId($id){$this->id = $id;}
		public function setUser($user){$this->user = $user;}
		public function setData($data){$this->data = $data;}
		public function setTitle($title){$this->title = $title;}
		public function setRead($read){$this->read = $read;}
		public function setDeleted($deleted){$this->deleted = $deleted;}
		public function setImportant($important){$this->important = $important;}
		public function setTime($time){$this->time = $time;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			$fetch = $conn->prepare("SELECT user, title, data, `read`, deleted, important, time FROM Notification WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$notification = $fetch->fetch();
			
			$this->user = $notification['user'];
			$this->title = $notification['title'];
			$this->data = $notification['data'];
			$this->read = $notification['read'];
			$this->time = $notification['time'];
			$this->deleted = $notification['deleted'];
			$this->important = $notification['important'];
			
			return TRUE;
			
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO Notification (user, title, data, `read`, deleted, important, time) VALUES (?, ?, ?, 0, 0, ?, NOW())");
			$post->bindParam(1, $this->user);
			$post->bindParam(2, $this->title);
			$post->bindParam(3, $this->data);
			$post->bindParam(4, $this->important);
			$post->execute();
			
			if($post->rowCount() == 1)
				return TRUE;
			
			$this->id = $conn->lastInsertId();
			
			return FALSE;
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE Notification SET user = ?, title = ?, data = ?, `read` = ?, deleted = ?, important = ? WHERE id = ?");
			$put->bindParam(1, $this->user);
			$put->bindParam(2, $this->title);
			$put->bindParam(3, $this->data);
			$put->bindParam(4, $this->read);
			$put->bindParam(5, $this->deleted);
			$put->bindParam(6, $this->important);
			$put->bindParam(7, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		
		/**
		* Used for the mass-message page under 'Settings'
		*/
		public function sendToRoles($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$errs = [];
				
			$validator = new Validator();
			
			if(!isset($vars['roles']))
				$vars['roles'] = [];
			
			// Validation
			if(!isset($vars['title']) OR !$validator->validLength($vars['title'], 1, 64))
				$errs[] = "Title Must Be Between 1 And 64 Characters";
			
			if(!isset($vars['important']) OR !$validator->isValidOption($vars['important'], ['no', 'yes']))
				$errs[] = "Invalid Selection For Important";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			$this->title = $vars['title'];
			$this->data = $vars['data'];
			$this->important = ($vars['important'] == "no") ? 0 : 1;
			
			$recipients = [];
			
			// Send to all recipients
			global $conn;
			$conn->beginTransaction();
			
			foreach(getUsers(0) as $user)
			{
				$isRecipient = FALSE;
				
				// Check all roles submitted
				foreach($vars['roles'] as $id)
				{
					$role = new Role($id);
					if($role->load())
					{
						foreach($user->getRoles() as $ur)
						{
							if($role->getId() == $ur->getId())
							{
								$isRecipient = TRUE;
								break;
							}
						}
					}
				}
				
				if($isRecipient)
				{
					$recipients[] = $user;
					$this->setUser($user->getId());
					if(!$this->post())
					{
						$conn->rollback();
						return FALSE;
					}
				}
			}
			
			// Send out email, if checked
			if(isset($vars['email']) AND EMAIL_ENABLED === TRUE)
			{
				$mailer = new Mailer($recipients, "New " . SITE_TITLE . " Notification: " . $vars['title'], "You have a new notification from " . SITE_TITLE . ":\n\n" . $vars['data']);
				$mailer->send();
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Sends out a message to users, roles, or permissions
		* @param $vars is an array that should consist of
		* 	['users'] OR ['roles'] OR ['permissions'], all containing numerical IDs (or code for permissions)
		*	['title']
		*	['message']
		*/
		public function send(&$vars)
		{
			$recipients = [];
			$this->title = $vars['title'];
			$this->data = $vars['message'];
			$this->important = (ifSet($vars['important']) == "yes") ? 1 : 0;
			
			if(isset($vars['users']) AND is_array($vars['users']) AND !empty($vars['users']))
			{
				foreach($vars['users'] as $userId)
				{
					$user = new User($userId);
					if($user->load())
						$recipients[] = $user;
				}
			}
			else if(isset($vars['roles']) AND is_array($vars['roles']) AND !empty($vars['roles']))
			{
				foreach(getUsers(0) as $user)
				{
					$isRecipient = FALSE;
					
					// Check all roles submitted
					foreach($vars['roles'] as $id)
					{
						$role = new Role($id);
						if($role->load())
						{
							foreach($user->getRoles() as $ur)
							{
								if($role->getId() == $ur->getId())
								{
									$isRecipient = TRUE;
									break;
								}
							}
						}
					}
					
					if($isRecipient)
						$recipients[] = $user;
				}
			}
			else if(isset($vars['permissions']) AND is_array($vars['permissions']) AND !empty($vars['permissions']))
			{
				foreach(getUsers(0) as $user)
				{
					$isRecipient = FALSE;
					
					// Check all roles submitted
					foreach(vars['permissions'] as $permission)
					{
						if($user->hasPermission($permission))
							$isRecipient = TRUE;
					}
					
					if($isRecipient)
						$recipients[] = $user;
				}
			}
			else
				return FALSE;
			
			foreach($recipients as $recipient)
			{
				$this->user = $recipient->getId();
				$this->post();
			}
			
			// Send out email, if checked
			if(isset($vars['email']) AND EMAIL_ENABLED === TRUE)
			{
				$mailer = new Mailer($recipients, $vars['title'], $vars['message']);
				$mailer->send();
			}
		}
		
		/**
		* Mark notification as read
		*/
		public function read()
		{
			$this->read = 1;
			return $this->put();
		}
		
		/**
		* Marks notification as deleted
		*/
		public function delete()
		{
			$this->deleted = 1;
			return $this->put();
		}
	}
