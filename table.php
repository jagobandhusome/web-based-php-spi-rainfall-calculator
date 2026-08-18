<?php
$conn = mysqli_connect("localhost","boolkyos_spi","boolkyos_spi","boolkyos_spi");
 
					    if (mysqli_connect_errno()){
					        echo "Failed to connect to MySQL: " . mysqli_connect_error();
					      }
					      ?>
<!DOCTYPE html>
<html lang="en">
    <head><!--  Header starts from here -->
        <title>SPI Calculator</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>      <!-- adding ahortcur favicon icon  -->
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">    <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css"/>  <!-- Latest font-awesome.min.css CSS -->
        <link rel="stylesheet" type="text/css" href="css/normalize.css">        <!-- loaded normalize css for cross browser -->
        <link rel="stylesheet" type="text/css" href="css/custom.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
                 <!-- my custom css code are here -->
    </head>                                                                     <!--  /head -->

    <body>
        <script src="scripts/bootstrap.min.js"></script>                        <!-- loaded bootstrap jquery code -->

        <div id="wrap">                                                         <!-- wrap div -->

            <!-- <div class="container-fluid"> -->                              <!-- top bar black div  -->
                <!-- <div class="row">
                    <div class="site-nav-wrapper">
                        <div class="site-nav">
                            
                        </div>                      
                    </div>                  
                </div>          
            </div>  -->                                                          <!-- /top bar black div  -->

            <header class="header">                                              <!-- header div -->
                <div class="container">
                    <div class="row" id="intro">
          
                        <b> <a style="padding-top: 30px;" href="https://www.booleandreams.com">Yearly SPI Calculator. Developed by Jagobandhu Some, Assistant Engineer, SPARRSO, +8801911852314</a> </b> 
                        
                    </div>                  
                </div>
            </header> 

<div class="container">                                              <!--  main container div -->
    	<div class="row">

		</div>  <!-- row end here -->
		
		<div id="wrap" class="wrap-custom">
			<div class="container">		
			<div class="footer-menu">
                <ul class="menu">
                   <li><a href="https://spi.booleandreams.com/">New Calculation</a></li>
                   <li><a href="https://spi.booleandreams.com/spi.php">Calculate</a></li>
                   <li><a href="https://spi.booleandreams.com/table.php">Report</a></li>                                  
                </ul>
            </div> 	
				<form name="std_details" method="post" enctype="multipart/form-data" action="">

				<!-- 1st part -->

				<!-- left menu part -->
					<!-- <div class="col-xs-6 col-sm-2 well">
						<legend>Result Sheet</legend>

						<button class="alert_p alert-info alert_p">View Pending</button>
						<button class="alert_p alert-success alert_p"><a href="?page=totalregistered">View Registered</a></button>
						<button class=" alert-warning alert_p"><a href="?page=notconfirmed">View Not Confirmed</a></button>
						<button class=" alert-warning alert_p"><a href="?page=disapproved">View Disapproved</a></button>
						<button class=" alert-danger alert_p"><a href="?page=notregistered">View Not Registered</a></button>
						<button class=" alert-success alert_p"><a href="?page=profile">User Profile</a></button>
					</div> -->
					<!-- /left menu part -->
		
					<!-- <div class="col-xs-6 col-sm-10 well">
						<div class="navbar navbar-default ymm">
							<div class="navbar-header" style="margin-top:5px;"></div>
							<div class="navbar-collapse collapse navbar-left left-margin">
							<form action="" name="query-form" method="post" enctype="multipart/form-data" >
									
									<ul class="nav nav-pills">
										<li class="top-margin"><span>Select Department:&nbsp;&nbsp;&nbsp;</span></li>
										<li class="dropdown" role="presentation">
											<?php //echo $objForm->getCoutriesSelect(2);?>											
										</li>
										<li class="top-margin"><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select Batch: &nbsp;&nbsp;</span></li>
										<li class="dropdown" role="presentation"><?php //echo $objForm->getBatchesSelect(1);?>		
										</li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
										<li class="active">
											<input class="btn btn-primary" type="submit" value="Submit" name="submit" />
										</li>									
									</ul>
								</form>
							</div>
						</div>						
					</div> -->

					 <!--/ 1st part -->

					<div class="col-xs-12 col-sm-12 well">
					<?php 
						
						$otherSql = "SELECT * FROM kval";
						 $otherResult = mysqli_query($conn, $otherSql);
						 $otherValues = mysqli_fetch_all($otherResult, MYSQLI_ASSOC);
						  foreach ($otherValues as $othervalue) {
						  	//var_dump($othervalue);
						  	echo "<b>";
							echo '<thead id="newtable">';
									echo '<tr>';
									echo "<th>Value of Average Mean (k5) like =AVERAGE(E15:E663) is: " .$othervalue['k5'] ."</th>" ."</br>";
									echo "<th>Value of Sum of Log(mean) / log(k5) by filed (k6) like =SUM(F15:F663) is: " .$othervalue['k6'] ."</th>" ." </br>";
									echo "<th>Value of (k8) like =LN(K5)-K6/K11 is: " .$othervalue['k8'] ."</th>" ." </br>";
									echo "<th>Value of GammaDist (k9) like =(1/(4*K8))*(1+SQRT((1+(4*K8/3)))) is: " .$othervalue['k9'] ."</th>" ." </br>";							    
									echo "<th>Value of (k10) like =K5/K9 is:  " .$othervalue['k10'] ."</th>" ."</br>";
									echo '</tr>';
						  echo '</thead>';	
						  echo "</b>";
						  }
				  
						 $tblSql = "SELECT * FROM spical";
						 $tblResult = mysqli_query($conn, $tblSql);
						 $tblValues = mysqli_fetch_all($tblResult, MYSQLI_ASSOC);
						 $RowNo = mysqli_num_rows($tblResult);

						if($RowNo != 0)
						    {
						    echo '<table  class="table"  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
						    	<tbody class="table-striped">'; 
				    		echo '<thead>
										<tr>
										    <th>Year</th>
										    <th>Total Yearly Rain</th>
										    <th>Countable Month</th>
										    <th>Average Yearly Rain (Mean)</th>								    
										    <th>Log of Average Yearly Rain / Log of(Mean)</th>
										    <th>Gamma Value</th>
										    <th>SPI Value</th>
										</tr>
									</thead>';	

								      $sn = 1;
								      $bg = 'bgcolor="#ffffff"';
								      $tbl_batch = 'batch';
								      $tbl_dept = 'department';	

								      foreach ($tblValues as $key => $value) {
								      					     
								     echo '<tr '.$bg.'>
								        <td width="20%" valign="middle"  align="left" >'.$value['year'].'</td>
								        <td width="20%" valign="middle" align="left" >'.$value['totalYearlyRain'] .'</td>        
								        <td width="20%" valign="middle" align="left" >'.$value['countableMonth'].'</td>                      
								        <td width="20%" valign="middle" align="left" >'.$value['yearlyAvgRain'].'</td>
								        <td width="20%" valign="middle" align="left" >'.$value['ln'].'</td>
								        <td width="20%" valign="middle" align="left" >'.$value['gammaValue'].'</td>
								        <td width="20%" valign="middle" align="left" >'.$value['spiVal'].'</td>';						     
								     
								     
								    if($sn % 2 != 0)
								    {
								    $bg = 'bgcolor="#eeeeee"';
								    }
								    else
								    {
								    $bg = 'bgcolor="#ffffff"';
								    }
								     
								    $sn = $sn + 1;
								     
								    }    
								  echo '</tbody>
								  </table>';
								  echo '<u>Found Total :&nbsp;'.$RowNo.'</u>	'; 
								}
								else
								{ 
								  echo '<table  class="auto"  width="100%" border="0" cellspacing="1" cellpadding="4" align="center">
								<tbody>'; 
								  echo '<tr><td align="center">';
								  echo "<br /><br /><center><h2>Select Your Department & Batch from Above & Click on Submit Button. </h2></center>";
								  echo "</td></tr></tbody></table>";  
								  echo "Found Total : ";
								  echo $RowNo;  
								}
						?>
					
					</div>


					<!-- <approval part> -->
					<!-- <div class="col-xs-6 col-sm-2 well">
					</div> -->
					<!-- /<approval part> -->

				</form>
			</div>
		</div>

  	</div>                                                             <!-- /main container div -->

</div> 
                                                                        <!-- /footer div -->
		
	</body>
</html>