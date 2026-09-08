
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>

.bg{
 width: 100%;
 background-color:#dcdcdc;
}
.bg2{
 width: 100%;
background-color:#cccccc;
}
.bg3{
  width: 100%;

}
.bg3 tr:nth-child(even) {
    background-color: #81DAF5;
}
.bg3 tr:nth-child(odd) {
    background-color: #82FA58;
}

table {
border-spacing: 0;
border-collapse: separate;

}
table td{
padding-left: 5px;
}
.thead td{
  border-bottom: solid green 2px;
  font-weight: bold;
  color:blue;
}
.red
{
  color:red;
  font-weight: bold;
}
.green {
  color:green;
  font-weight: bold;
}
.logo{
  height: 150px;
  width: 200px;
}
.lefthead{
  width: 30%;
}
.righthead{
  width: 100%;
}
.righthead p{
  margin: 0px;
  padding: 0px;
}
.bg3 tr:last-child {
    background-color: #cccccc;


}
.bg3 tr:last-child td {
border-top: solid #000 2px;
margin-top:10px !important;
}
#footer
{

width:100%;
height:50px;
position:absolute;
bottom:0;
left:0;
}
</style>
</head>

<body >
<div id="admit">
  <table class="bg">
    <tr>
    <!-- <td class="lefthead">

    <img class="logo" src="./img/logo.png">
    </td>-->

   <td class="righthead">
     <h3>{{$institute->name}}</h3><pre>
<p><strong>Establish:</strong> {{$institute->establish}}</p>
<p><strong>Web:</strong> {{$institute->web}}</p>
<p><strong>Email:</strong> {{$institute->email}}</p>
<p><strong>Phone:</strong> {{$institute->phoneNo}}</p>
<p><strong>Address:</strong> {{$institute->address}}</p>
     </pre>
   </td>
   </tr>

 </table>
 <table class="bg2">
   <tr><td>

  </td>
  <td><strong>Student Attendance Report</strong></td>
  <td >

  </td>
</tr>
</table>
<br>
<center><h2></h2></center>
<table>
<tr>
  <td></td>
  <td>Name: <strong>{{$stdinfo->firstName}} {{$stdinfo->middleName}} {{$stdinfo->lastName}} </strong></td>
</tr>
<tr>
    <td></td>
  <td>Class: <strong>{{$stdinfo->class}}</strong></td>
  <td></td>
  <td>Section: <strong>{{$stdinfo->section}}</strong></td>

</tr>
<tr>
 
  <td></td>
  <td>Session: <strong>{{get_current_session()->title}}</strong></td>

</tr>
<tr>
    <td></td>
  <td>Regi. No.: <strong>{{$stdinfo->regiNo}}</strong></td>
  <td></td>
  <td>Roll No.: <strong>{{$stdinfo->rollNo}}</strong></td>

</tr>
</table>
<br>
<table class="bg3">
   <tbody>
    <tr class="thead">
        <td>Year</td>
        <td>Percentage</td>
        
    </tr>

    @foreach($attendances as $attendance)
        <tr>
            <td>{{$attendance->year}}</td>

           <?php  

                $no_days  = date("z", mktime(0,0,0,12,31,$attendance->year)) + 1;

                $start = new \DateTime($attendance->year.'-01-01');
                $end = new \DateTime($attendance->year.'-12-31');
                $days = $start->diff($end, true)->days;
                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
                $total_days = $no_days-$sundays;

                $percentage= $attendance->data/$total_days * 100;

           ?>

            <td>{{$percentage}}</td>
        


        </tr>



    @endforeach
    
  </tbody>
</table>

<div id="footer">
  <p>Print Date: {{date('d/m/Y')}}</p>
</div>
</body>
</html>
