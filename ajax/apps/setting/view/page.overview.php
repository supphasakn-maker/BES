<?php
	global $os;
?>

<div class="row">
	<div class="col">
		<ul class="list-group list-group-horizontal">
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Client OS</div><strong><?php echo $os->getOS();?></strong>
			</li>
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Browser</div><strong><?php echo $os->getBrowser();?></strong>
			</li>
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Server OS</div><strong><?php echo php_uname("s");?></strong>
			</li>
		</ul>
	</div>
</div>
<div class="jumbotron handle bg-primary text-white mt-4" style="margin-bottom:0px;">
	<div class="container">
		<h1>Oceanos</h1>
		<p class="lead">
			<em>Version</em> Beta 1.0
		</p>
		<dl class="row">
			<dt class="col-2"><?php echo $os->tr("overview.code_name");?></dt><dd class="col-10">Oceanos</dd>
			<dt class="col-2">			<?php echo $os->tr("overview.version");?></dt><dd class="col-10">0.1</dd>
			<dt class="col-2"><?php echo $os->tr("overview.license");?></dt><dd class="col-10">5 Users</dd>
			<dt class="col-2">Server Address</dt><dd class="col-10"><?php echo $_SERVER['SERVER_ADDR'];?></dd>
			<dt class="col-2">My Address</dt><dd class="col-10"><?php echo $os->get_client_ip();?></dd>
			<dt class="col-2">Server OS</dt><dd class="col-10"><?php echo php_uname("s");?></dd>
			<dt class="col-2">PHP Version</dt><dd class="col-10"><?php echo phpversion();?></dd>
			<dt class="col-2">Mac Address</dt><dd class="col-10"><?php echo exec("cat /sys/class/net/ens160/address");?></dd>
			
			
		</dl>
		<dl class="row">
			<dt class="col-2">My Address</dt><dd class="col-10"><?php echo $os->get_client_ip();?></dd>
			<dt class="col-2">OS Platform</dt><dd class="col-10"><?php echo $os->getOS();?></dd>
			<dt class="col-2">Browser</dt><dd class="col-10"><?php echo $os->getBrowser();?></dd>
		</dl>
		<p class="text-align-center">
			<a class="btn btn-success btn-lg" onclick="fn.system.check()">
				Lastest Update &nbsp; 
				<i class="fa fa-check"></i>
			</a>
		</p>
		
	</div>
</div>
			