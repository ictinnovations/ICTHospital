<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Marks Sheet</title>


    <link rel="stylesheet" type="text/css" href="{{url('/markssheetcontent/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/markssheetcontent/result.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/markssheetcontent/fonts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/markssheetcontent/stylesheet.css')}}">
    <script type="text/javascript">
        //<![CDATA[
        var Croogo = {"basePath":"\/","params":{"controller":"student_results","action":"index","named":[]}};
        //]]>
    </script>

    <script type="text/javascript" src="{{url('/markssheetcontent/jquery-1.8.2.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/markssheetcontent/js.js')}}"></script>
    <script type="text/javascript" src="{{url('/markssheetcontent/admin.js')}}"></script>

<style type="text/css">
.attendenceReport{width:700px !important}
.btn-print {
    color: #ffffff;
    background-color: #49b3e2;
    border-color: #aed0df;
    float: right;

    font-size: 40px;
    padding: 5px;
    margin: 10px 10px;
    border-radius: 20px;
}

.btn-print:hover,
.btn-print:focus,
.btn-print:active,
.btn-print.active,
.open .dropdown-toggle.btn-print {
  color: #ffffff;
  background-color: #0F9966;
  border-color: #19910E;
}

.btn-print:active,
.btn-print.active,
.open .dropdown-toggle.btn-print {
  background-image: none;
}

.btn-print .badge {
  color: #34BA1F;
  background-color: #ffffff;
}

</style>

</head>
<?php //echo 'fdf<pre>';print_r($result ); 
   $result_array = array();
   $i=0;
   //echo '09<pre>';print_r( $result);
  // exit;
  foreach( $result as $key=>$rest){
    //$first_names[] = array_column($rest[$i], $key);
     //$typew[] = $rest[$i]['type'];
     //echo $student = $rest['firstName'].' '.$rest['firstName'];

       //echo 'fdf<pre>'.$key;print_r($rest['subject_name'] );
      // exit;
       /*$old_sub = $rest['subject_name'];
       if($old_sub ==$rest['subject_name']){
        $column[] =array_merge($rest);
       }*/
    $result_array[]     = $rest;
    $student            = $rest[0]['firstName'];
    $fatherName         = $rest[0]['fatherName'];
    $class              = $rest[0]['class'];
    $section_name       = $rest[0]['section_name'];
    $session            = $rest[0]['session'];
    $rollNo             = $rest[0]['rollNo'];
    $subject            = $key;
    $i++;
    break;
  }
   //echo '656<pre>';print_r($first_names );
    ?>
<body class="scms-result-print page">
  <button class="btn-print" onclick="printDiv('printableArea')">Print</button>
<div id="printableArea">
  <div class="wraperResult">
    <div class="resHdr">
        <img src="{{url('/markssheetcontent/res-logo.png')}}" alt="" class="resLogo">            <div class="schoolIdentity">
            <img src="{{url('/markssheetcontent/school-title.png')}}" alt="">                <div class="hdrText">
                <span> EXAMINATION-{{$student}}</span>
                <strong>{{$class}} / Equivalent Result Publication {{$session }} </strong>
            </div><!-- end of hdrText -->
        </div><!-- end of schoolIdentity -->
    </div><!-- end of resHdr -->

    <div class="resContainer">
        <div class="resTophdr">
            <div class="restopleft">
                <div><b>{{$student}} </b></div>
                <div><span>FATHER'S NAME</span><i>: </i><em>{{$fatherName}}</em></div>
                <div><span>CLASS</span><i>: </i><em>{{$class}}</em></div>
                <div><span>SECTION</span><i>: </i><em>{{$section_name}}</em></div>
                <div><span>ROLL NO</span><i>: </i><em>{{$rollNo}}</em></div>
              
            </div><!-- end of restopleft -->

            <div class="restopleft rgttopleft">
               

               <?php /* <div><span>GPA</span><i>: </i><em>{{$meritdata->point}}</em></div> 

                <div><span>GRADE</span><i>: </i><em>{{$meritdata->grade}}</em></div>
                <div><span>MERIT POSITION</span><i>: </i><em>{{$meritdata->position}}TH</em></div>
                */ ?>
                <!--<div><span>PROMOTED CLASS : </span><em>9 (B)</em></div>-->
            </div><!-- end of restopleft -->
        </div><!-- end of resTophdr -->


        <div class="resmidcontainer">
            <h2 class="markTitle">Subject-Wise Grade &amp; Mark Sheet</h2>
            <table class="pagetble_middle">
                <tbody><tr>
                    <th class="res2 cTitle" rowspan="2">SUBJECT</th>
                   
                    <!--<th class="res3 examtitle" colspan="6">Final EXAMINATION MARKS</th>-->
                </tr>

                <tr>
                <!--<td class="res1">&nbsp;</td>
                    <td class="res2">&nbsp;</td>
                    <td class="res1">Total</td>
                    <td class="res1">GP</td>
                    <td class="res3">Highest</td>
                    <td class="res7" colspan="2">Written</td>
                    <td class="res7" colspan="2">MCQ</td>
                    <td class="res7" colspan="2">SBA</td>
                    <td class="res7" colspan="2">Practical</td>-->
                     
            @foreach($result as $k=>$reslt)
               @if($subject==$k)
                   @foreach($reslt as $exam)
                        <td class="res5" colspan="4">{{$exam['type']}}</td>
                        
                        <!--<td class="res3">Written</td>
                        <td class="res4">MCQ</td>
                        <td class="res5">SBA</td>
                        <td class="res3">Total</td>
                        <td class="res3">GP</td>
                        <td class="res6">Grade</td>-->
            @endforeach
                @endif
                    @endforeach
                     <td class="res3">%</td>
                     <td class="res3">Grage</td>
                     <td class="res3">Comments</td>   

                </tr>

                <?php /* <tr>
                    @if($extra[1])

                    <td>{{$banglaArray[0][0]}}</td>
                    <td class="cTitle">{{$banglaArray[0][1]}}</td>

                    <td><b>{{$banglaArray[0][2]}}</b></td>
                    <td rowspan="2"><b>{{$banglaArray[0][2]+$banglaArray[1][2]}}</b></td>

                    <td><b>{{$banglaArray[0][3]}}</b></td>
                    <td rowspan="2"><b>{{$banglaArray[0][3]+$banglaArray[1][3]}}</b></td>

                    <td><b>{{$banglaArray[0][4]}}</b></td>
                    <td rowspan="2"><b>{{$banglaArray[0][4]+$banglaArray[1][4]}}</b></td>

                    <td><b>{{$banglaArray[0][5]}}</b></td>
                    <td rowspan="2"><b>{{$banglaArray[0][5]+$banglaArray[1][5]}}</b></td>
                    <td rowspan="2"><b>{{$blextra[0]}}</b></td>
                    <td rowspan="2"><b>{{$blextra[1]}}</b></td>
                    <td rowspan="2"><b>{{$blextra[2]}}</b></td>
                    <td rowspan="2"><b>{{$blextra[3]}}</b></td>
                    <!--<td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>-->
                </tr>                    <tr>
                    <td>{{$banglaArray[1][0]}}</td>
                    <td class="cTitle">{{$banglaArray[1][1]}}</td>

                    <td><b>{{$banglaArray[1][2]}}</b></td>

                    <td><b>{{$banglaArray[1][3]}}</b></td>

                    <td><b>{{$banglaArray[1][4]}}</b></td>

                    <td><b>{{$banglaArray[1][5]}}</b></td>


                    <!--<td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>-->
                </tr>
                @endif
                @if($extra[3])
                 
                    <td>{{$englishArray[0][0]}}</td>
                    <td class="cTitle">{{$englishArray[0][1]}}</td>

                    <td><b>{{$englishArray[0][2]}}</b></td>
                    <td rowspan="2"><b>{{$englishArray[0][2]+$englishArray[1][2]}}</b></td>

                    <td><b>{{$englishArray[0][3]}}</b></td>
                    <td rowspan="2"><b>{{$englishArray[0][3]+$englishArray[1][3]}}</b></td>

                    <td><b>{{$englishArray[0][4]}}</b></td>
                    <td rowspan="2"><b>{{$englishArray[0][4]+$englishArray[1][4]}}</b></td>

                    <td><b>{{$englishArray[0][5]}}</b></td>
                    <td rowspan="2"><b>{{$englishArray[0][5]+$englishArray[1][5]}}</b></td>


                    <td rowspan="2"><b>{{$enextra[0]}}</b></td>
                    <td rowspan="2"><b>{{$enextra[1]}}</b></td>
                    <td rowspan="2"><b>{{$enextra[2]}}</b></td>
                    <td rowspan="2"><b>{{$enextra[3]}}</b></td>


                    <!--<td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>-->
                    </tr>                    <tr>
                        <td>{{$englishArray[1][0]}}</td>
                        <td class="cTitle">{{$englishArray[1][1]}}</td>

                        <td><b>{{$englishArray[1][2]}}</b></td>

                        <td><b>{{$englishArray[1][3]}}</b></td>

                        <td><b>{{$englishArray[1][4]}}</b></td>

                        <td><b>{{$englishArray[1][5]}}</b></td>


                        <!--<td><b>&nbsp;</b></td>
                        <td><b>&nbsp;</b></td>
                        <td><b>&nbsp;</b></td>
                        <td><b>&nbsp;</b></td>
                        <td><b>&nbsp;</b></td>
                        <td><b>&nbsp;</b></td>-->
                    </tr>
                @endif
*/ ?>
                <?php /*@foreach($subcollection as $subject)
                <tr>
                  <!--  <td>{{$subject->subcode}}</td>-->
                    <td class="cTitle">{{$subject->subname}}</td>
                   <td colspan="4"><b>{{$subject->outof}}</b></td>
                    <td colspan="4"><b>{{$subject->total}}</b></td>
                    
                    <td colspan="4"><b><?php $percentage = $subject->total/$subject->outof*100;  ?>{{ $percentage }}</b></td>
                    <td colspan="4"><b>{{$subject->grade}} </b></td>
                    <!--<td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>
                    <td><b>&nbsp;</b></td>-->
                </tr>
              @endforeach
               */ 
           $obtmarks=0;
           $total_marks=0;
           $percentage=0;
           $id=0;
              ?>
               @foreach($result as $subject=>$reslt)
               <td class="cTitle">{{$subject}}</td>
               @foreach($reslt as $total)
               
                   <td colspan="4"><b>{{$total['obtain_marks']}}/{{$total['total_marks']}}</b></td>
                   
                <?php $obtmarks    += $total['obtain_marks'];  ?>
                <?php $total_marks += $total['total_marks'] ;  ?>
            @endforeach
            <?php $percentage = $obtmarks/$total_marks * 100;  

               if ($percentage <= 100 && $percentage >= 95){
                    $grade = 'A+';
                    //$gpoint = '4.00' 
                }
                 elseif ($percentage >= 90 &&$percentage < 95){
                    $grade = 'A';
                 }
                 elseif ($percentage < 90 && $percentage >= 80){
                    $grade = 'B+';
                 }
                 elseif ($percentage <= 79  && $percentage >= 70){
                    $grade = 'B';
                 }
                 elseif ($percentage <= 69 && $percentage >= 60 ){
                    $grade = 'C';
                 }else{
                    $grade = 'F';
                 }

          
            ?>
              <td >{{ $percentage }}</td>
                <td>{{$grade}}</td>
                  <td ><div id="{{$id}}" ></div><input type="text" value="" name='coment'></td>
             <tr>
               <?php $id++; ?>
            @endforeach
                </tr>
                </tbody></table>
        </div><!-- end of resmidcontainer -->
<div class="btmcontainer">
        <?php /*<div class="btmcontainer">
            <div class="overalreport overalreportAll">
                <h2 class="markTitle">Overall Report</h2>
                <table class="pagetble" style="height:113px">
                    <tbody><tr>
                        <th class="column1" style="width:110px; padding:3px 0 2px">Subjects</th>
                        <th class="column2" style="width:130px">Total Marks</th>
                        <th class="column3">Gp</th>
                    </tr>
                    @if($extra[1])
                    <tr>
                        <!--<td class="column1" style="width:110px; text-align:center">Urdu {{$banglaArray[1][1]}}</td>-->
                        <td class="column2" style="width:130px">{{$blextra[0]}}</td>
                        <td class="column3"><b>{{$blextra[2]}}</b></td>
                    </tr>
                    @endif
                    @if($extra[3])
                        <tr>
                            <!--<td class="column1" style="width:110px; text-align:center">English{{$englishArray[1][1]}} </td>-->
                            <td class="column2" style="width:130px">{{$enextra[0]}}</td>
                            <td class="column3"><b>{{$enextra[2]}}</b></td>
                        </tr>
                    @endif
                    @foreach($subcollection as $subject)
                        <tr>
                            <td class="column1" style="width:110px; text-align:center">{{$subject->subname}}</td>
                            <td class="column2" style="width:130px">{{$subject->total}}</td>
                            <td class="column3"><b>{{$subject->point}}</b></td>
                        </tr>
                        @endforeach
                    <tr>
                        <td class="column1" style="width:110px">&nbsp;</td>
                        <td class="column2" style="width:130px">&nbsp;</td>
                        <td class="column3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="column1" style="width:110px;height:23px;text-align:center">Overall</td>
                        <td class="column2" style="width:130px;height:23px">{{intval($meritdata->totalNo)}}</td>
                        <td class="column3" style="height:23px"><b>{{$meritdata->point}}</b></td>
                    </tr>
                    </tbody></table>
            </div><!-- end of overalreport -->
         */ ?>

            <div class="overalreport attendenceReport">
                <h2 class="markTitle">Attendance Report</h2>
                <table class="pagetble" style="height:181px">
                    <tbody><tr>
                        <th colspan="2">Month : Presence</th>
                    </tr>

                    @for($i = 0; $i < 12; $i=$i+2)
                    <tr>
                        <td>{{$attendance[$i]->month}} : {{$attendance[$i]->present}}</td>
                        <td>{{$attendance[$i+1]->month}} : {{$attendance[$i+1]->present}}</td>

                    </tr>
                  @endfor
                  </tbody></table>

               
            </div><!-- end of overalreport -->

            <div class="overalreport gpagrading">
                <?php /*<h2 class="markTitle">GPA Grading</h2>
                <table class="pagetble" style="height:181px">
                    <tbody><tr>
                        <th class="column1">Range of Marks(%)</th>
                        <th class="column2">Grade</th>
                        <th class="column3">Point</th>
                    </tr>
                    <tr>
                        <td class="column1">80 or Above </td>
                        <td class="column2"> A+</td>
                        <td class="column3">5.00</td>
                    </tr>
                    <tr>
                        <td class="column1">70 to 79</td>
                        <td class="column2">A</td>
                        <td class="column3">4.00</td>
                    </tr>
                    <tr>
                        <td class="column1">60 to 69</td>
                        <td class="column2">A-</td>
                        <td class="column3">3.50</td>
                    </tr>
                    <tr>
                        <td class="column1">50 to 59</td>
                        <td class="column2">B</td>
                        <td class="column3">3.00</td>
                    </tr>

                    <tr>
                        <td class="column1">40 to 49</td>
                        <td class="column2">C</td>
                        <td class="column3">2.00</td>
                    </tr>

                    <tr>
                        <td class="column1">33 to 39</td>
                        <td class="column2">D</td>
                        <td class="column3">1.00</td>
                    </tr>
                    <tr class="lastitem">
                        <td class="column1">Below 33</td>
                        <td class="column2">F</td>
                        <td class="column3">0.00</td>
                    </tr>
                    </tbody></table> */ ?>

                     <h2 class="markTitle">Extra Activities </h2>
                <table class="pagetble" style="height:106px"><tbody>
                    <tr><td></td></tr> </tbody></table>

                <h2 class="markTitle">Achievement</h2>
                <table class="pagetble" style="height:106px">
                    <tbody>
                        <tr>
                            <th align="center" valign="middle">
                            </th>
                            <th align="center" valign="middle">
                            </th>
                        </tr>                    
                    </tbody>
                </table>
            </div><!-- end of overalreport -->

        </div><!-- end of resmidcontainer -->
    </div><!-- end of resContainer -->
    <div class="signatureWraper"><div class="signatureCont">
            <div class="sign-grdn"><b>Signature (Guardian)</b></div>
            <div class="sign-clsT"><b>Signature (Class Teacher)</b></div>
            <div class="sign-head">
                <!--<img src="/markssheetcontent/head-sign.png" alt="" style="left:23px;bottom:21px">-->                <b>Signature (Head Master)</b>
            </div>
        </div>
            </div><!-- end of signatureWraper -->
    <img src="{{url('/markssheetcontent/certificate-bg.png')}}" alt="" class="result-bg">    </div><!-- end of wraperResult -->
  </div>


   <script>
    function printDiv(divName) {
      // alert(JSON.stringify(document.getElementsByTagName("input").length));
         for($i=0;$i<document.getElementsByTagName("input").length;$i++){
          inpText=JSON.stringify(document.getElementsByTagName("input")[$i].value);
          //alert(inpText);
          //var neww = document.getElementById($i).innerHTML =inpText ;
          var neww =  $("#"+$i).append(JSON.parse(inpText));
          // alert(neww)

           printContents += inpText;
       }
       $('input').hide();

         var printContents    = document.getElementById(divName).innerHTML;
         var originalContents = document.body.innerHTML;
       
        //var inpText = document.getElementsByTagName("input").value;
       
        
        //alert(JSON.stringify( printContents));
      //console.log(JSON.stringify( printContents));
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        //document.write(printContents + originalContents);
       //$(this).attr('value',val);
    }

   </script>
</body><!-- end of fromwrapper-->
</html>
