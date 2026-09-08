<html>
<head>
  <style>
    body{margin:0; padding:0}
    .main-vouchar{width:100%;margin:0 auto;display: inline-flex;}
    .vouchar{-webkit-box-flex: 0;text-align:center;width: 33.333%;margin: 0 0.7% 0 0.7%;}
    .vouchar img {width:100%;}
    .for-use {background:#efefef;padding:10px;text-align:left;margin-top:0;width:120px;}
    .vouchar table {width:100%; margin:0 auto;}
    .student-info td {text-align:right;text-decoration: underline;}
    .student-info th {text-align:left}
    .sig-n-stamp { text-align: left; line-height: 25px; font-size:12px;}
    @page {size: landscape;}

    .doted {
      border-left: 2px dotted #444;
  
}
.background{
    position:absolute;
    z-index:0;
    background:white;
    display:block;
    min-height:50%; 
    min-width:50%;
    color:yellow;
}
.bg-text
{
    color:lightgrey;
    font-size:120px;
    transform:rotate(300deg);
    -webkit-transform:rotate(300deg);
}
</style>
</head>

<body>

  <div class="main-vouchar">
  
  @for($i=0;$i<3;$i++)
    <div class="@if($i==1 || $i==2) doted @endif vouchar ">
    @if($i==0)
      <p class="for-use"> Bank Copy </p>
      @elseif($i==1)
      <p class="for-use"> Office Copy  </p>
      @else
      <p class="for-use"> Student Copy </p>
      @endif
      <img src="http://school.ictcore.org/markssheetcontent/school-title.png" />
      <h2> </h2>
      <h3> Vouchar Type (Fee Vouchar) </h3>
 <table border="0" cellspacing="10" class="student-info">
        <tr>
          <th>Family Id</th>
          <td>{{$family_id}}</td>
          <th>Month</th>
            <td>{{$month}} {{-- {{ \DateTime::createFromFormat('!m', $month)->format('F')}}--}}</td>
        </tr>
 </table>
      <?php /* <table border="0" cellspacing="10" class="student-info">
        <tr>
          <th>Student Name</th>
          <td>{{$student->firstName}} {{$student->lastName}}</td>
        </tr>
        <tr>
          <th>Father Name</th>
          <td>{{$student->fatherName}}</td>
        </tr>
      </table>
      <table class="student-info">
        <tr>
          <th>Reg. No</th>
          <td>{{$student->regiNo}}</td>
          <th>Class</th>
          <td>{{$student->class}}</td>
        </tr>
        <tr>
          <th>Month</th>
          <?php
           $fe_title=array();
           $other=array();
           $ofees=array();
          ?>
          @foreach($vouchar_details as $vouchar_detail)
           <?php  
           $payable    = $vouchar_detail->payableAmount ; 
           $paid       = $vouchar_detail->paidAmount ; 
           $paid       = $vouchar_detail->total_fee ; 
           $dueAmount  = $vouchar_detail->dueAmount; 
           $due_date   = $vouchar_detail->due_date; 
           ?>
          @if($vouchar_detail->title=='monthly')
          <?php  $fe_title[]  = $vouchar_detail->month; ?>
          @else
           <?php  $other[]  = $vouchar_detail->title ;
                   $ofees[] =$vouchar_detail->fee;
           ?>
          @endif
          @endforeach
          <?php  sort($fe_title); $implod=implode(',', $fe_title); ?>

          <td>{{$implod}}</td>
          
          <th>Due Date </th>
          <td>{{$due_date}}</td>
        </tr>
      </table> */ ?>
{{--<div class="background">
  <p class="bg-text">Vouchar Paid</p>
  </div>--}}
      <table border="1">
        <tr>
          <th>Student Info</th>
          <th>Student Fee</th>
         
          <th>Amount</th>
        </tr>
        @foreach($vouchar_details as $vouchar_detail)
        
        <tr>
          <td>{{$vouchar_detail->firstName . ' '.$vouchar_detail->lastName}} ({{$vouchar_detail->rollNo}}-{{$vouchar_detail->class}})</td>
          <td>{{$vouchar_detail->payableAmount}}</td>
         
          <td> @if($vouchar_detail->payableAmount >= $vouchar_detail->paidAmount){{$vouchar_detail->payableAmount - $vouchar_detail->paidAmount}}@else 0.00  @endif</td>
        </tr>
        @endforeach
        <tr>
        <td>Due Amount</td>
        
        <td></td>
     
        <td colspan="3">{{$totals->dueamount}}</td>
        </tr>
        <tr>
        <td>total</td>
        
        <td></td>
     
        <td colspan="3">@if($totals->paiTotal<=$totals->payTotal){{$totals->payTotal - $totals->paiTotal}} @else 0.00 @endif</td>
        </tr>
      </table>

      <div class="sig-n-stamp">
        <p>Paid Date : _________________________________ </p>
        <p>Bank Stamp & Sig : __________________________ </p>
        <p>Accountant Sig : ____________________________ </p>
        <p>For Detail : 03157180220</p>
      </div>
    </div>
@endfor





    <?php /*
    <div class="vouchar">
      <p class="for-use"> Office Copy </p>
      <img src="http://school.ictcore.org/markssheetcontent/school-title.png" />
      <h2> Quid Campus </h2>
      <h3> Vouchar Type (Fee Vouchar) </h3>
      <table border="0" cellspacing="10" class="student-info">
        <tr>
          <th colspan="2">Student Name</th>
          <td colspan="2">Kashif Majeed</td>
        </tr>
        <tr>
          <th colspan="2">Father Name</th>
          <td colspan="2">Abd-ul-Majeed</td>
        </tr>
        <tr>
          <th>Reg. No</th>
          <td>122320</td>
          <th>Class</th>
          <td>Class Two</td>
        </tr>
        <tr>
          <th>Month</th>
          <td>Decembe</td>
          <th>Due Date </th>
          <td>10-Dec-2018</td>
        </tr>
      <table>

      <table border="1">
        <tr>
          <th>Detail</th>
          <th>Amount</td>
        </tr>
        <tr>
          <td>School Fees</td>
          <td>1500.00</td>
        </tr>
        <tr>
          <td>Previous Dues</td>
          <td>150.00</td>
        </tr>
        <tr>
          <td>Other</td>
          <td>00.00</td>
        </tr>
        <tr>
          <td>Total</td>
          <td> 1650.00 PKR</td>
        </tr>
        <tr>
          <td>After Due Date</td>
          <td>1700.00 PKR</td>
        </tr>
      </table>
      <div class="sig-n-stamp">
        <p>Paid Date : _________________________________ </p>
        <p>Bank Stamp & Sig : __________________________ </p>
        <p>Accountant Sig : ____________________________ </p>
        <p>For Detail : 03157180220</p>
      </div>
    </div>
    <div class="vouchar">
      <p class="for-use"> Student Copy </p>
      <img src="http://school.ictcore.org/markssheetcontent/school-title.png" />
      <h2> Quid Campus </h2>
      <h3> Vouchar Type (Fee Vouchar) </h3>
      <table border="0" cellspacing="10" class="student-info">
        <tr>
          <th colspan="2">Student Name</th>
          <td colspan="2">Kashif Majeed</td>
        </tr>
        <tr>
          <th colspan="2">Father Name</th>
          <td colspan="2">Abd-ul-Majeed</td>
        </tr>
        <tr>
          <th>Reg. No</th>
          <td>122320</td>
          <th>Class</th>
          <td>Class Two</td>
        </tr>
        <tr>
          <th>Month</th>
          <td>Decembe</td>
          <th>Due Date </th>
          <td>10-Dec-2018</td>
        </tr>
      <table>
      <table border="1">
        <tr>
          <th>Detail</th>
          <th>Amount</td>
        </tr>
        <tr>
          <td>School Fees</td>
          <td>1500.00</td>
        </tr>
        <tr>
          <td>Previous Dues</td>
          <td>150.00</td>
        </tr>
        <tr>
          <td>Other</td>
          <td>00.00</td>
        </tr>
        <tr>
          <td>Total</td>
          <td> 1650.00 PKR</td>
        </tr>
        <tr>
          <td>After Due Date</td>
          <td>1700.00 PKR</td>
        </tr>
      </table>
      <div class="sig-n-stamp">
        <p>Paid Date : _________________________________ </p>
        <p>Bank Stamp & Sig : __________________________ </p>
        <p>Accountant Sig : ____________________________ </p>
        <p>For Detail : 03157180220</p>
      </div>
    </div> */ ?>
  </div>
</body>
</html>
