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
</style>
</head>

<body>
  <div class="main-vouchar">
  @for($i=0;$i<3;$i++)
    <div class="@if($i==1 || $i==2) doted @endif vouchar ">
    @if($i==0)
      <p class="for-use"> Bank Copy </p>
      @elseif($i==1)
      <p class="for-use"> Student Copy </p>
      @else
      <p class="for-use"> Office Copy </p>
      @endif
      <img src="http://school.ictcore.org/markssheetcontent/school-title.png" />
      <h2> </h2>
      <h3> Vouchar Type (Fee Vouchar) </h3>
      <table border="0" cellspacing="10" class="student-info">
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
           echo "count".count($vouchar_details);
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
          <?php  
            $fe_title[]  =  \DateTime::createFromFormat('!m', $vouchar_detail->month)->format('F'); 
          ?>
          @else
           <?php  
                  $other[]  = $vouchar_detail->title ;
                  $ofees[]  = $vouchar_detail->fee;
           ?>
          @endif
          @endforeach
    
          <?php  sort($fe_title); $implod=implode(',', $fe_title); ?>

          <td>{{$implod}}</td>
          <th>Due Date </th>
          <td>{{$due_date}}</td>
        </tr>
      </table>

      <table border="1">
        <tr>
          <th>Detail</th>
          <th>Amount</td>
        </tr>
        <tr>
          <td>School Fees</td>
          <td>{{$fees}}</td>
        </tr>
        <tr>
        <?php 
        
          //$due =  $totals->dueamount ;
          $due =  $dueAmount ;
          //$totals->paiTotal
                    
        ?>
          <td>Pending Amount</td>
          <td>{{$due}}</td>
        </tr>
        <?php 
            
          $toal=  count($other);
           //exit;
        ?>
        @for($j=0;$j<$toal; $j++)
        <tr>
          <td>{{$other[$j]}}</td>
          <td>{{$ofees[$j]}}</td>
        </tr>
        @endfor
        <tr>
          <td>Discount</td>
          <td>{{$discount}}</td>
        </tr>
        <tr>
          <td>Paid Amount</td>
          {{--<td> {{$paid }} PKR</td>--}}
          <td> {{$paid }} PKR</td>
        </tr>
        <tr>
          <td>After Due Date</td>
          <td>{{ $late_fee }} PKR</td>
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
