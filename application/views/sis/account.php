<? if($this->ion_auth->logged_in()){ ?>
<? if(!empty($message)){ ?><div class="message message-information"><div class="icon margin-right"></div><h3>Information</h3><p><?php echo $message;?></p></div><? } ?>
<? if(!empty($message_information)){ ?><div class="message message-information"><div class="icon margin-right"></div><h3>Information</h3><p><?php echo $message_information;?></p></div><? } ?>
<? if(!empty($message_success)){ ?><div class="message message-success"><div class="icon margin-right"></div><h3>Success</h3><p><?php echo $message_success;?></p></div><? } ?>
<? if(!empty($message_error)){ ?><div class="message message-error"><div class="icon margin-right"></div><h3>Error</h3><p><?php echo $message_error;?></p></div><? } ?>
<? if(!empty($message_warning)){ ?><div class="message message-warning"><div class="icon margin-right"></div><h3>Warning</h3><p><?php echo $message_warning;?></p></div><? } ?>
<h1>Welcome <?=$currentUser->firstName?> <?=$currentUser->lastName?></h1>
<div>
	<div class="widget half profile">
		<a href="/auth/edit_user">
			<div class="widget-title">
				<div class="widget-title-left icon"></div>
				<div class="widget-title-centre">Profile</div>
				<div class="widget-title-right icon"></div>
			</div>
		</a>
		<div class="widget-body">
			<p><b>Name:</b> <?=$currentUser->firstName?> <?=$currentUser->lastName?></p>
			<p><b>Email:</b> <?=$currentUser->email?></p>
			<? if(!empty($currentUser->bio)) { ?>
			<p><b>Bio:</b> <?=$currentUser->bio?></p>
			<? } ?>
			<a href="/auth/edit_user" class="button right normal">Edit Profile</a>
		</div>
	</div>
	<div class="widget half tickets">
		<a href="/sis/userTickets">
			<div class="widget-title">
				<div class="widget-title-left icon"></div>
				<div class="widget-title-centre">Tickets</div>
				<div class="widget-title-right icon"></div>
			</div>
		</a>
		<div class="widget-body">
			<p><b>2012/04/12</b> Ticket</p>
			<p><b>2012/04/13</b> Ticket</p>
			<p><b>2012/04/14</b> Ticket</p>
			<a href="/sis/userTickets" class="button margin-right normal">View All Tickets</a>
			<a href="/sis/tickets" class="button green">Buy Tickets</a>
		</div>
	</div>
	<div class="widget half participation">
		<a href="/sis/enrolment">
			<div class="widget-title">
				<div class="widget-title-left icon"></div>
				<div class="widget-title-centre">Participation</div>
				<div class="widget-title-right icon"></div>
			</div>
		</a>
		<div class="widget-body">
			<p>You aren't signed up in any tournaments.</p>
			<a href="/sis/tickets" class="button right normal">Sign up for Tournaments</a>
		</div>
	</div>
	<? if($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('centreadmin')){ ?>
	<div class="widget half tms">
		<a href="/tms/">
			<div class="widget-title">
				<div class="widget-title-left icon"></div>
				<div class="widget-title-centre">Tournament Management System</div>
				<div class="widget-title-right icon"></div>
			</div>
		</a>
		<div class="widget-body">
			<a href="/tms" class="button right normal">Enter the tournament Management System portal</a>
		</div>
	</div>
	<? } ?>
</div>
<!--<a class="button tickets" href="/sis/tickets"><div class="icon"></div><div class="label">Buy Tickets</div><div class="subtitle">Purchase and view your tickets for tournaments</div></a>
	<a class="button signup" href="/sis/tournaments"><div class="icon"></div><div class="label">Sign up</div><div class="subtitle">Sign up for sports tournaments</div></a>
	<a class="button details" href="/sis/details"><div class="icon"></div><div class="label">Edit My Details</div><div class="subtitle">Change your personal details</div></a>
	<? if($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('centreadmin')){ ?>
		<a class="button tms" href="/tms/"><div class="icon"></div><div class="label">Tournament Management System</div><div class="subtitle">Enter the tournament Management System portal</div></a>
	<? } ?>

	<div style="clear:both;"></div>-->
<? } else { redirect('/','refresh'); } ?>