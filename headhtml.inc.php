<html>
	<head>
    <meta name="keywords" content="Usenet, SITENAME, SITE, Anmeldung, registration">
		<title><?php echo $title; ?></title>
    <meta name="author" content="Arnold Schiller, Mike Lischke" />
    <link rel="stylesheet" type="text/css" href="main.css" />
    <link rel="stylesheet" type="text/css" href="common.css" />
	<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="https://<?php echo DOMAIN_CURRENT_SITE ?>/register/screen.css">
	</head>
  <body>

<div id="header">
<div id="logo"><a href="https://<?php echo DOMAIN_CURRENT_SITE ?>/"><?php echo SITENAME ?></a></div>

<ul id="navibar">
<li class="wikilink current"><a href=<?php echo  $_SERVER["PHP_SELF"]?> ><?php echo $title ?></a></li>
<li class="wikilink"><a href="https://<?php echo DOMAIN_CURRENT_SITE ?>/register/">Anmeldung</a></li>
<li class="wikilink"><a href="https://<?php echo DOMAIN_CURRENT_SITE ?>/register/newpasswort.php">Neues Passwort</a></li>
<li class="wikilink"><a href="https://<?php echo DOMAIN_CURRENT_SITE ?>/index.php">Homepage</a></li>
<li class="wikilink"><a href="https://<?php echo DOMAIN_CURRENT_SITE ?>/owncloud/index.php">BGE Cloud</a></li>
</ul>
<div id="pageline"><hr style="display:none;"></div>
</div>


		<table bgcolor="#cbcbcb" style="padding: 5px; spacing: 0px; margin: 0px; width: 100%; height: 100%">
			<tbody>
				<tr>
					<td style="background-image: url(images/html_bg_left.gif); background-position: right top; background-repeat: repeat-y;" align="right">
					</td>
					<td align="left" valign="middle" width="600">
						<table style="padding: 0px; spacing: 0px; margin: 0px; width: 100%; border: none">
							<tbody>
								<tr>
									<td valign="top">
									</td>
								</tr>
								<tr>
									<td valign="top">

										<!-- frame -->
										<table cellspacing=0 cellpadding=0 style="padding: 0px; spacing: 0px; margin: 0px; width: 100%; border: none">
											<tbody>
												<tr>
													<td style="background: url(images/frame_007.gif) no-repeat; height: 3px; width: 3px"></td>
													<td style="background: url(images/frame_004.gif) repeat-x; height: 3px; width: 1px"></td>
													<td style="background: url(images/frame_005.gif) no-repeat; height: 3px; width: 3px"></td>
												</tr>
												<tr>
													<td style="background-image: url(images/frame.gif); height: 1px; width: 3px; background-repeat: repeat-y"></td>
													<td bgcolor="#FFFFFF">

	                          <!-- panel -->
														<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
															<tbody>
																<tr>
																	<td height="27">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<tbody>
																				<tr>
																					<td style="background-image: url(images/panel.gif); height: 3px; width: 3px"></td>
																					<td style="background-image: url(images/panel_005.gif)"></td>
																					<td style="background-image: url(images/panel_006.gif); height: 3px; width: 3px"></td>
																				</tr>
																				<tr>
																					<td style="background-image: url(images/panel_004.gif)"></td>
																					<td style="background-image: url(images/panel_004.gif); height: 23px">&nbsp;&nbsp;&nbsp;<span id="panel-common"><?php echo $title?></span></td>
																					<td style="background-image: url(images/panel_004.gif)"></td>
																				</tr>
																				<tr>
																					<td style="background-image: url(images/panel_007.gif); height: 1px"></td>
																					<td style="background-image: url(images/panel_007.gif)"></td>
																					<td style="background-image: url(images/panel_007.gif)"></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td valign="top">
																		<table cellpadding="0" cellspacing="10" height="100%" width="100%">
																			<tbody>
																				<tr>
																					<td valign="top">
																						<span id="standard">

																							<!-- box -->
																							<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px; width: 100%">
																								<tbody>
																									<tr>
																										<td>

																											<!-- box header start -->
																											<table border="0" cellpadding="0" cellspacing="0" width="100%" height="20">
																												<tbody>
																													<tr>
																														<td style="background: url(images/box_006.gif) no-repeat; width: 25px"></td>
																														<td align="center" style="background: url(images/box.gif) repeat-x">
																															<b id="box-common" style="color: <?php echo $messageColor?>"><?php echo $message ?></b>
																														</td>
																														<td align="center" style="background: url(images/box_003.gif) no-repeat; width: 48px">
																														</td>
																													</tr>
																												</tbody>
																											</table>
																											<!-- box header end -->

																										</td>
																									</tr>
																									<tr>
																										<td>

																											<!-- box content start -->
																											<table border="0" cellpadding="0" cellspacing="0" width="100%">
																												<tbody>
																													<tr>
																														<td style="background: url(images/box_004.gif); width: 6px"></td>
																														<td bgcolor="#f4f4f4">
																															<table cellpadding="5" width="100%">
																																<tbody>
																																	<tr>
																																		<td>
																																			<span id="content">
