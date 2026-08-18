<?php

  $conn = mysqli_connect("localhost","boolkyos_spi","boolkyos_spi","boolkyos_spi");
 
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }

  $sql = "SELECT * FROM data";
  $result = mysqli_query($conn, $sql);
  $values = mysqli_fetch_all($result, MYSQLI_ASSOC);

  $clearSqlspikval = "TRUNCATE TABLE kval";
   mysqli_query($conn, $clearSqlspikval);

//First step to calculate mean and LN then insert those value into database.
  /*foreach ($values as $key => $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    $mean = ($value['year']  +  $value['rain'])/12;
    $ln = log($mean);
    $dataUpdate = "UPDATE data SET mean = $mean, ln = $ln WHERE id ='".$value['id']."'";
    mysqli_query($con, $dataUpdate);
       
  }*/
 $totalUniqueYear = 0;
 $yearSql = "SELECT DISTINCT(year) AS year FROM data ORDER BY year ASC;";
 $yearResult = mysqli_query($conn,$yearSql);
 $yearValues = mysqli_fetch_all($yearResult, MYSQLI_ASSOC);
 $totalUniqueYear = mysqli_num_rows($yearResult);
 
/* foreach ($yearValues as $key => $value) {
     echo $value['year'] . "</br>";
 }*/



function getSameDateRowsSum($data) {
    $groups = array();
    $key = 0;
    $count = 0;
    foreach ($data as $item) {
        $key = $item['year'];
        
        if(is_numeric($item['rain'])){

            if (!array_key_exists($key, $groups)) {
                
                $groups[$key] = array(
                    'rain' => $item['rain'],
                    'count' => $count + 1
                );

            } else {
                $groups[$key]['rain'] = $groups[$key]['rain'] + $item['rain'];
                $groups[$key]['count'] = $groups[$key]['count'] + 1;
            }
            $key++;
        }
    }
    
    return $groups;
}

$preparedData = getSameDateRowsSum($values);
/*echo "<pre>";
var_dump($preparedData);
echo "</pre>";*/
 foreach ($yearValues as $key => $value) {
    $noYr = $value['year'];
    
    $totalMean = $preparedData[$noYr]['rain'];
    
    $avgMean  = $totalMean/$preparedData[$noYr]['count'];
    
    $ln = log($avgMean);
    
    $count = $preparedData[$noYr]['count'];
    //$dataUpdate = "INSERT INTO spical SET year = '".$noYr."', totalYearlyRain = '".$totalMean."', countableMonth = '".$preparedData[$noYr]['count']."', yearlyAvgRain = '".$avgMean."', ln = '".$ln."'";
    $insertSql = "UPDATE `spical` SET  totalYearlyRain = $totalMean, countableMonth = $count, yearlyAvgRain = $avgMean, ln = $ln WHERE year = $noYr";
    mysqli_query($conn, $insertSql);
 }

/*echo "<pre>";
print_r(getSameDateRowsSum($values));
echo "</pre>";
*/

 //second step to calculate average mean and Sum(LN)

 $avgSql = "SELECT  yearlyAvgRain, ln FROM spical";
 $avgResult = mysqli_query($conn, $avgSql);
 $avgValues = mysqli_fetch_all($avgResult, MYSQLI_ASSOC);
 $num_rows = mysqli_num_rows($avgResult);

 //echo "Total number of rows is: $num_rows" . "</br>";

 $totalUniqueYear = 0;
 $totalMean = 0;
 $sumOfLn = 0;
 $k5 = 0;
 $k8 = null;
 $k9 = null;
 $k10 = null;
 foreach ($avgValues as $key => $avgValue) {
     $totalMean += $avgValue['yearlyAvgRain'];
     $sumOfLn += $avgValue['ln'];
 }
 $k5  = $totalMean/$num_rows;

 //echo "K5/AvgMean is: $k5" . "</br>";

 //echo "Sumof(LN) is: $sumOfLn" . "</br>";

 $yearSql = "SELECT DISTINCT(year) AS year FROM spical ORDER BY year ASC;";
 $yearResult = mysqli_query($conn, $yearSql);
 $yearValues = mysqli_fetch_all($yearResult, MYSQLI_ASSOC);
 $totalUniqueYear = mysqli_num_rows($yearResult);

 //echo "Total unique year is: $totalUniqueYear" . "</br>";

 $k8 = log($k5) - $sumOfLn/$totalUniqueYear;

 //echo "Value of K8 is: $k8" . "</br>";

 $k9 = (1/(4*$k8))*(1+sqrt((1+(4*$k8/3))));

// echo "Value of K9 is: $k9" . "</br>";

 $k10 = $k5/$k9;

// echo "Value of K10 is: $k10" . "</br>";
 
 define('SQRT2PI', 2.5066282746310005024157652848110452530069867406099);
 include_once('Gamma.php');
 $objGamma = new Gamma;

 $spiSql = "SELECT * FROM spical";
 $spiResult = mysqli_query($conn, $spiSql);
 $spiValues = mysqli_fetch_all($spiResult, MYSQLI_ASSOC);

 mysqli_query($conn,"INSERT INTO `kval` (k5, k6, k8, k9, k10) VALUES ($k5, $sumOfLn, $k8, $k9, $k10)");

  /*$ksql = "INSERT INTO `kval` SET k5 = $k5, k6 = $sumOfLn, k8 = $k8, k9 = $k9, k10 = $k10";
   mysqli_query($conn, $ksql);
*/
 foreach ($spiValues as $key => $value) {
   $mean = $value['yearlyAvgRain'];
   $gma = $objGamma->GAMMADIST($mean,$k9,$k10,true);
  // echo "Gamma value is $gma" . "</t>";
   $spiVal = $objGamma->NORMINV($gma,0,1);
   /*echo $gma . "\t" . "\t" . $spiVal. "<br>" ;*/
   //echo $gma . "<br>" ;
   /*echo number_format((float)$spiVal, 3, '.', '') . "<br>" ;*/
   $dataUpdateAgain = "UPDATE spical SET gammaValue = $gma, spiVal = $spiVal WHERE id ='".$value['id']."'";
   mysqli_query($conn, $dataUpdateAgain);
   header("Location: https://spi.booleandream.com/spi/table.php"); /* Redirect browser */

       
  }
 

// echo "</br>";
 
 /*echo "Average mean value is: $k5" . "</br>";
 //echo "</br>";
 echo "Sum of LN is: " . $sumOfLn ."</br>";
 $k8 = log($k5) - $sumOfLn/$totalUniqueYear; //have to count unique total year.
 echo "Value of K8 is: $k8" . "</br>";
 
 echo "</br>"."Value of K9 is: $k9"; 
 $k10 = $avgMean/$k9;
 echo "</br>" ."Value of k10 is: $k10"; */




 



 //echo "<pre>";
 //var_dump($values);
 //echo "</pre>"; */

  
?>