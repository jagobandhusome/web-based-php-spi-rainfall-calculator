<?php
    $con = mysqli_connect("localhost","boolkyos_spi","boolkyos_spi","boolkyos_spi");
 
    if (mysqli_connect_errno())
      {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }

  $uploadOk = 1;

  if(isset($_POST["submit"])) {
    $check = ($_FILES["upload"]["tmp_name"]);
    if($check !== false) {
      //$name_file=$_FILES['upload']['name'];
     // $type_file=$_FILES['upload']['type'];
      //$size_file=$_FILES['upload']['size'];
     // $target_dir = "http://localhost/spi/";
      $target_file = $_FILES["upload"]["tmp_name"];
      //$content = file_get_contents($target_file);
      $handle = fopen($target_file,"r");
      //var_dump($_FILES);

      if ($handle) {
        $clearSql = "TRUNCATE TABLE data";
        mysqli_query($con, $clearSql);

        $clearSqlspiCal = "TRUNCATE TABLE spical";
        mysqli_query($con, $clearSqlspiCal);

          while (($line = fgets($handle)) !== false) {

               $lineArr = explode("\t", "$line");                        
               list($year, $month, $rain) = $lineArr;
               mysqli_query($con,"INSERT INTO `data` (year, month, rain) VALUES ('$year', '$month', '$rain')");
          }
      }
        $uploadOk = 1;
    } else {
        echo "Incorrect type file.";
        $uploadOk = 0;
    }
    fclose($handle);
   // $yearSql = "SELECT DISTINCT(year) AS year FROM data ORDER BY year ASC";
    // $yearResult = mysqli_query($con,$yearSql);
    // $yearValues = mysqli_fetch_assoc($yearResult);
     
        $yearSql = $con->prepare("SELECT DISTINCT(year) AS year FROM data ORDER BY year ASC");
        $yearSql->execute();

        $yearValues = $yearSql->fetchAll();
        print_r($yearValues);

    foreach ($yearValues as $key => $value) {

      $noYr = $value['year'];
      mysqli_query($con,"INSERT INTO `spical` (year) VALUES ($noYr)");
     }
     header("Location: /spi.php");
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

                 <!-- my custom css code are here -->
    </head>                                                                     <!--  /head -->

    <body>
        <script src="scripts/bootstrap.min.js"></script>                        <!-- loaded bootstrap jquery code -->
        <div id="wrap">                                                   <!-- /top bar black div  -->
            <header class="header">                                              <!-- header div -->
                <div class="container">
                    <div class="row" id="intro">
          
                        <b> <a style="padding-top: 30px;" href="https://www.booleandreams.com"> SPI Calculator. Developed by Jagobandhu Some, Assistant Engineer, SPARRSO, +8801911852314, jagobandhusome@gmail.com</a> </b> 
                        
                    </div>                  
                </div>
            </header> 

          <div class="container">                                              <!--  main container div -->
            <div class="row">
        <div class="container">                                              <!--  main container div -->
          <div class="row">
            <div class="footer-menu">
                <ul class="menu">
                   <li><a href="spi.booleandreams.com">New Calculation</a></li>
                   <li><a href="spi.booleandreams.com/spi.php">Calculate</a></li>
                   <li><a href="spi.booleandreams.com/table.php">Report</a></li>                                  
                </ul>
            </div>
            <div id="loginbox">
              <form role="form" method="POST" name="myform" enctype="multipart/form-data">
                <span class="span-font">Upload Data From text file</span><br/>
              <div class="input-group margin-top-50" style="text-align: center;">

                <!-- <span class="input-group-addon"><i class="icon-user"></i></span> -->
                
                    <div class="form-group" style="text-align: center;">
                      <label for="exampleFormControlFile1">Choose a text file...</label>
                      <input type="file" class=" btn btn-primary" name="upload" id="exampleFormControlFile1">
                    </div>
              </div>

              <div class="input-group margin-top" style="text-align: center;">
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
              </div>
<!-- 
                <button type="submit" class="btn btn-danger pull-right margin-top">Sign in</button> -->

            </form>
            </div>
          </div>
        </div>

             

  <!--     <form action="" method="post" enctype="multipart/form-data">
              <div style="position:relative;">
                      <a class='btn btn-primary' href='javascript:;'>
                          Choose File...
                          <input type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="upload" size="40"  onchange='$("#upload-file-info").html($(this).val());'>
                      </a>
                      &nbsp;
                      <span class='label label-info' id="upload-file-info"></span><button type="submit" class="btn btn-primary">Submit</button>
              </div>
              
             </form>
 -->




            </div>                                                             <!-- /main container div -->
          </div>  
        </div>                                                             <!-- /footer div -->  
  </body>
</html>