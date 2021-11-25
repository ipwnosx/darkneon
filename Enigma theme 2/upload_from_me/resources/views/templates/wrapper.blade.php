<?php

/////SETTINGS//////////////////////////////////////////////////////////////////////////
$db_host = '';                                                                       //
$db_user = '';                                                                       //
$db_pass = '';                                                                       //
$db_base = '';                                                                       //
$enable_create_url = true; // true or false (enable create server button)            //
$create_url = 'https://site.com/create'; // url to create server                     //
																					 //
$enable_our_website = true; // true or false (enable menu link to main website)      //
$url_main_website = 'https://site.com'; // url main website (if enabled)             //
																					 //
$show_graphics = true; // true or false (enable graphics on main page)               //
$show_statistic = true; // true or false (enable statistic on main page)             //
																					 //
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////
//DO NOT TOUCH AFTER THIS//
///////////////////////////
///////////////////////////

function throw_ex($er){  
  throw new Exception($er);  
}  
try {
	$link = mysqli_connect('localhost',$db_user,$db_pass,$db_base);
	$res = mysqli_query($link,"SELECT COUNT(*) FROM servers");
	$row = mysqli_fetch_row($res);
	$servers = $row[0];
	$res = mysqli_query($link,"SELECT COUNT(*) FROM users");
	$row = mysqli_fetch_row($res);
	$users = $row[0];
	
	$i = 0;
	while ($i < 7) {
		$date = date('Y-m-d');
		$date = date('Y-m-d', strtotime($date. " - ".$i." day"));
		$res = mysqli_query($link,"SELECT COUNT(*) FROM `users` WHERE `created_at` <= '".$date." 23:59:59' AND `created_at` >= '".$date." 0:00:01'");
		$row = mysqli_fetch_row($res);
		$stats_s[$i] = $row[0];
		$i++;
	}
	$i = 0;
	while ($i < 7) {
		$date = date('Y-m-d');
		$date = date('Y-m-d', strtotime($date. " - ".$i." day"));
		$res = mysqli_query($link,"SELECT COUNT(*) FROM `servers` WHERE `created_at` <= '".$date." 23:59:59' AND `created_at` >= '".$date." 0:00:01'");
		$row = mysqli_fetch_row($res);
		$stats[$i] = $row[0];
		$i++;
	}
} catch (exception $e) {
	$show_graphics = false;
	$stats[6] = 0; $stats[5] = 0; $stats[4] = 0; $stats[3] = 0; $stats[2] = 0; $stats[2] = 0; $stats[1] = 0; $stats[0] = 0;
	$stats_s[6] = 0; $stats_s[5] = 0; $stats_s[4] = 0; $stats_s[3] = 0; $stats_s[2] = 0; $stats_S[2] = 0; $stats_s[1] = 0; $stats_s[0] = 0;
	$servers = 0; $users = 0;
}

?>

<html>
    <head>
        <title>{{ config('app.name', 'Pterodactyl') }}</title>

        @section('meta')
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="robots" content="noindex">
			<link rel="stylesheet" href="/assets/enigma_theme_2/style.css">
            <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="/favicons/manifest.json">
            <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#bc6e3c">
            <link rel="shortcut icon" href="/favicons/favicon.ico">
            <meta name="msapplication-config" content="/favicons/browserconfig.xml">
            <meta name="theme-color" content="#0e4688">
        @show

        @section('user-data')
            @if(!is_null(Auth::user()))
                <script>
                    window.PterodactylUser = {!! json_encode(Auth::user()->toVueObject()) !!};
                </script>
            @endif
            @if(!empty($siteConfiguration))
                <script>
                    window.SiteConfiguration = {!! json_encode($siteConfiguration) !!};
                </script>
            @endif
        @show
        <style>
            @import url('//fonts.googleapis.com/css?family=Rubik:300,400,500&display=swap');
            @import url('//fonts.googleapis.com/css?family=IBM+Plex+Mono|IBM+Plex+Sans:500&display=swap');
        </style>

        @yield('assets')

        @include('layouts.scripts')
    </head>
    <body class="{{ $css['body'] ?? 'bg-neutral-50' }}">
	
		<div class="cust_load"><center><div style="margin-top:calc(50vh - 50px);">
			<div class="cssload-loader">
				<div class="cssload-flipper">
					<div class="cssload-front"></div>
					<div class="cssload-back"></div>
				</div>
			</div>
			<br><p>Please wait...</p></div></center>
		</div>
				
		<input onclick="open_menu()" type="checkbox" id="overlay-input"></input>
		<label for="overlay-input" id="overlay-button"><span></span></label>
		
		<div id="left_1" class="left-side">
				<a id="l_1_1" href="/"><i class="fas fa-terminal"></i></a>
				<a id="l_1_2" href="/files"><i class="fas fa-folder-open"></i></a>
				<a id="l_1_3" href="/databases"><i class="fas fa-database"></i></a>
				<a id="l_1_4" href="/schedules"><i class="fas fa-clock"></i></a>
				<a id="l_1_5" href="/users"><i class="fas fa-users-cog"></i></a>
				<a id="l_1_6" href="/backups"><i class="fas fa-ambulance"></i></a>
				<a id="l_1_7" href="/network"><i class="fas fa-network-wired"></i></a>
				<a id="l_1_8" href="/startup"><i class="fas fa-play"></i></a>
				<a id="l_1_9" href="/settings"><i class="fas fa-cogs"></i></a>
		</div>
		<div id="left_2" class="left-side">
				<a id="l_2_1" href="/account"><i class="fas fa-cogs"></i></a>
				<a id="l_2_2" href="/account/api"><i class="fas fa-puzzle-piece"></i></a>
		</div>
		
		<h2 id="server_title"></h2>
  
        @section('content')
            @yield('above-container')
			
            @yield('container')
            @yield('below-container')
        @show
        @section('scripts')
            {!! $asset->js('main.js') !!}
        @show
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
		<link rel="stylesheet" href="/assets/enigma_theme_2/style.css">

		<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

		<script>
		$('.left-side').hide();
		$('.custom_content').hide();
		$('#server_title').hide;

		function open_menu() {
			if($('#overlay')[0]) {
				$('#overlay')[0].classList.toggle('visible');
			}
		}
		
		setInterval(function(){
			if($('.cust_load')) $('.cust_load').fadeOut();
			var url = window.location.pathname;
			var surl = url.split('/');
			
			if($('#our_website')[0]) $('#our_website')[0].href = '<?php echo $url_main_website; ?>';
			
			if(url.includes('/auth')) {
				if($('.header')[0]) $('.header').hide();
				if($('#overlay-button')[0]) $('#overlay-button').hide();
			} else {
				if($($('#overlay-button')[0]).css('display') == 'none') { if($('.header')[0]) {$('.header').css('display','flex');} } else { if($('.header')[0]) {$('.header').css('display','none');} }
			}
			
			if(url == '/') {
				if($('.cZTZeB')[0]) $('.cZTZeB')[0].style.background = 'transparent';
				if($('#server_link')[0]) { $('#server_link')[0].classList = 'header-link active'; }
				$('.custom_content').show();	
				if($('#server-counter')[0]) $('#server-counter')[0].innerHTML = '<?php echo $servers; ?>';
				if($('#users-counter')[0]) $('#users-counter')[0].innerHTML = '<?php echo $users; ?>';
				
				/*
				if($('.sc-1xo9c6v-0')[0]) {
					var l = $('.sc-1xo9c6v-0').length;
					var i = 0;
					if($('#table')[0]) $('#table')[0].innerHTML = '';
					while (i < l) {
						var i_1 = $('.sc-1ibsw91-5')[i].textContent;
						var i_2 = $('.sc-1ibsw91-9')[i].textContent;
						var i_3 = $('.sc-1xo9c6v-0')[i].href;
						var i_4 = $($('.status-bar')[i]).css("background-color");
						if(i_4 == 'rgb(225, 45, 57)') { var status = '<div class="status is-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></svg>Offline</div>'; } else if (i_4 == 'rgb(24, 154, 28)') { var status = '<div class="status is-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>Active</div>'; }
						if($('#table')[0]) $('#table')[0].innerHTML += '<tr><td>'+i_1+'</td><td>'+i_2+'</td><td></td><td>'+status+'</td><td><a href="'+i_3+'"><button class="btn"><i class="fas fa-arrow-right"></i></button></a></td></tr>';
						i++;
					}
				}
				*/
				//if($('.cZTZeB')[0]) $('.cZTZeB').hide();
			}
			if(surl[1] == 'server') {
				$('#left_1').show();
				$('#server_title').show;
				$('#server_title')[0].textContent = document.title;
				if($('#l_1_1')[0]) $('#l_1_1')[0].href = '/server/'+surl[2];
				if($('#l_1_2')[0]) $('#l_1_2')[0].href = '/server/'+surl[2]+'/files';
				if($('#l_1_3')[0]) $('#l_1_3')[0].href = '/server/'+surl[2]+'/databases';
				if($('#l_1_4')[0]) $('#l_1_4')[0].href = '/server/'+surl[2]+'/schedules';
				if($('#l_1_5')[0]) $('#l_1_5')[0].href = '/server/'+surl[2]+'/users';
				if($('#l_1_6')[0]) $('#l_1_6')[0].href = '/server/'+surl[2]+'/backups';
				if($('#l_1_7')[0]) $('#l_1_7')[0].href = '/server/'+surl[2]+'/network';
				if($('#l_1_8')[0]) $('#l_1_8')[0].href = '/server/'+surl[2]+'/startup';
				if($('#l_1_9')[0]) $('#l_1_9')[0].href = '/server/'+surl[2]+'/settings';
			}
			if((surl[1] == 'server') && ((surl.length == 3) || (surl[3] == ''))) {
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','flex');
				if($('.cZTZeB')[0]) $('.cZTZeB').css('background','none');
				if($('.ifNwiE')[0]) $('.ifNwiE').css('display','flex');
				if($('.ifNwiE')[0]) $('.ifNwiE').css('padding','0');
				if($('.ifNwiE')[0]) $('.ifNwiE').css('justify-content','space-evenly');
				if($('#l_1_1')[0]) $('#l_1_1')[0].classList = 'active';
			} else
			if(surl[3] == 'files') {
				if($('#l_1_2')[0]) $('#l_1_2')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'databases') {
				if($('#l_1_3')[0]) $('#l_1_3')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'schedules') {
				if($('#l_1_4')[0]) $('#l_1_4')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'users') {
				if($('#l_1_5')[0]) $('#l_1_5')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'backups') {
				if($('#l_1_6')[0]) $('#l_1_6')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'network') {
				if($('#l_1_7')[0]) $('#l_1_7')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'startup') {
				if($('#l_1_8')[0]) $('#l_1_8')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			} else
			if(surl[3] == 'settings') {
				if($('#l_1_9')[0]) $('#l_1_9')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			}
			if((url.includes('/account')) && (!url.includes('/api'))) {
				$('#left_2').show();
				if($('#l_2_1')[0]) $('#l_2_1')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			}
			if(url.includes('/account/api')) {
				$('#left_2').show();
				if($('#l_2_2')[0]) $('#l_2_2')[0].classList = 'active';
				if($('.cZTZeB')[0]) $('.cZTZeB').css('display','block');
			}
			if($('.fNmetC')[0]) $('.fNmetC')[0].innerHTML = '<p class="kbxq2g-3 fNmetC">© 2015 - 2021&nbsp;<a rel="noopener nofollow noreferrer" href="https://pterodactyl.io" target="_blank" class="kbxq2g-4 hcJQtJ">Pterodactyl Software</a><br>Theme by <img style="height:14px;display:inline-block;" src="/assets/enigma_theme_2/enigma_logo.png"> <a style="display:inline-block;" href="https://discord.gg/C5Ex7cJU5r" class="kbxq2g-4 hcJQtJ">Enigma prod.</a></p>';
			if($('.fFcOT')[0]) $('.fFcOT')[0].innerHTML = '<p class="kbxq2g-3 fNmetC">© 2015 - 2021&nbsp;<a rel="noopener nofollow noreferrer" href="https://pterodactyl.io" target="_blank" class="kbxq2g-4 hcJQtJ">Pterodactyl Software</a><br>Theme by <img style="height:14px;display:inline-block;" src="/assets/enigma_theme_2/enigma_logo.png"> <a style="display:inline-block;" href="https://discord.gg/C5Ex7cJU5r" class="kbxq2g-4 hcJQtJ">Enigma prod.</a></p>';
		},500);

		</script>

		<script>
		var c1 = false;
		setInterval(function(){
			if(($('#chart_1')[0]) && (!c1)) {
				c1 = true;
				var ctx = document.getElementById("chart_1").getContext("2d");
				var gradient = ctx.createLinearGradient(0, 0, 0, 200);
				gradient.addColorStop(0, 'rgba(72,100,198,0.6)');
				gradient.addColorStop(1, 'rgba(72,100,198,0)');

				new Chart(document.getElementById("chart_1"), {
						type: 'line',
						data: {
						  labels: ["", "", "", "", "", "", ""],
						  datasets: [
							{
								label: "",
								fill: true,
								backgroundColor: gradient,
								borderColor: "rgb(95,118,232)",
								pointBorderColor: "#fff",
								pointBackgroundColor: "rgb(72,100,198)",
								pointBorderColor: "#fff",
								data: [<?php echo $stats[6]; ?>,<?php echo $stats[5]; ?>,<?php echo $stats[4]; ?>,<?php echo $stats[3]; ?>,<?php echo $stats[2]; ?>,<?php echo $stats[1]; ?>,<?php echo $stats[0]; ?>],
								tension: 0.5
							}
						  ]
						},
						
						options: {
							responsive: true,
							plugins: {
								legend: false,
							},
							scales: {
								yAxes: [{
									display: true,
									ticks: {
										suggestedMin: 0,
										suggestedMax: 1000
									}
								}],
								xAxes: [{
									display:false,
									ticks: {
										display: false
									}
								}]
							}
						}
					});
			}
		},500);
		</script>

		<script>
		var c2 = false;
		setInterval(function(){
			if(($('#chart_1')[0]) && (!c2)) {
				c2 = true;
				var ctx = document.getElementById("chart_2").getContext("2d");
				var gradient = ctx.createLinearGradient(0, 0, 0, 200);
				gradient.addColorStop(0, 'rgba(254,135,158,0.6)');
				gradient.addColorStop(1, 'rgba(254,135,158,0)');

				new Chart(document.getElementById("chart_2"), {
						type: 'line',
						data: {
						  labels: ["", "", "", "", "", "", ""],
						  datasets: [
							{
								label: "",
								fill: true,
								backgroundColor: gradient,
								borderColor: "rgb(254,135,158)",
								pointBorderColor: "#fff",
								pointBackgroundColor: "rgb(254,135,158)",
								pointBorderColor: "#fff",
								data: [<?php echo $stats_s[6]; ?>,<?php echo $stats_s[5]; ?>,<?php echo $stats_s[4]; ?>,<?php echo $stats_s[3]; ?>,<?php echo $stats_s[2]; ?>,<?php echo $stats_s[1]; ?>,<?php echo $stats_s[0]; ?>],
								tension: 0.5
							}
						  ]
						},
						
						options: {
							responsive: true,
							plugins: {
								legend: false,
							},
							scales: {
								yAxes: [{
									display: true,
									ticks: {
										suggestedMin: 0,
										suggestedMax: 1000
									}
								}],
								xAxes: [{
									display:false,
									ticks: {
										display: false
									}
								}]
							}
						}
					});
			}
		},500);
		</script>

		<?php
			if(!$enable_create_url) echo '<style>#create_server { display:none; }</style>';
			if(!$enable_our_website) echo '<style>#our_website { display:none; }</style>';
			if(!$show_graphics) echo '<style>#graphics { display:none; }</style>';
			if(!$show_statistic) echo '<style>.transection { display:none; }</style>';
		?>
	</body>
</html>