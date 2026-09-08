<!DOCTYPE html>
<html>
<head>

	<title></title>
	<style type="text/css">
	#invoice-POS{
 /* box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  padding:2mm;
  margin: 0 auto;
  width: 44mm;
  background: #FFF;*/
  text-align: center;
  
  }
h1{
  font-size: 1.5em;
  color: #222;
}
h2{font-size: .9em;}
h3{
  font-size: 1.2em;
  font-weight: 300;
  line-height: 2em;
}
p{
  font-size: .9em;
  color: #000;
  line-height: 1.2em;
}
	#top, #mid,#bot{ /* Targets all id with 'col-' */
  border-bottom: 1px solid #EEE;
}
	#top{min-height: 0px;}
#mid{min-height: 80px;} 
#bot{ min-height: 50px;}

#top .logo{
  //float: left;
  height: 75px;
  width: 120px;
  background: url(http://school.ictcore.org/markssheetcontent/school-title.png) no-repeat;
  background-size: 130px 75px;
}
.clientlogo{
  float: left;
  height: 60px;
  width: 60px;
  background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
  background-size: 60px 60px;
  border-radius: 50px;
}
.info{
  display: block;
  //float:left;
  margin-left: 0;
}
.title{
  float: right;
}
.title p{text-align: right;} 
table{
  width: 100%;
  border-collapse: collapse;

}

td{
  //padding: 5px 0 5px 15px;
  //border: 1px solid #EEE;
   font-size: 1.5em;

}
.tabletitle{
  //padding: 5px;
  font-size: .9em;
  background: #EEE;
}
.service{border-bottom: 1px solid #EEE;}
.item{width: 24mm;}
.itemtext{font-size: .5em;}

#legalcopy{
  margin-top: 5mm;
}
	</style>
</head>
<body>



  <div id="invoice-POS">
    
    <center id="top">
      <div class="logoff"></div>
      <div class="info"> 
        <h1>{{getinstitueinfo()->name}}</h1>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    
    <div id="mid">
      <div class="info">
        <h1>Fee Vouchar</h1>
         <p> 
            <h1>{{$fthername->fatherName}} ({{$family_id}})</h1>
            <h2>{{ $month }}</h2>
            <h2>{{ date('m/d/Y h:i:s a', time()) }}</h2>
        </p>
    </div><!--End Invoice Mid-->
    
    <div id="bot">

					<div id="table">
						<table>
							<thead>
				              <tr class="tabletitle">
				                <th class="item"><h2>Student</h2></th>
				               <!--  <th class="item"><h2>Payable</h2></th> -->
				                <th class="item"><h2>Amount</h2></th>
				                <!-- <td class="Rate"><h2>Sub Total</h2></td> -->
				              </tr>
				            <thead>
							<tbody>
							<?php 
								$total = 0 ; 
								$paid  = 0 ; 
							?>
				            @foreach($print_vouchar as $vouchar)
					            <?php 

					            	$total += $vouchar->payableAmount;
					            	$paid  += $vouchar->paidAmount;
					            ?>
					            <tr class="service">
					                <td class="tableitem"><p class="itemtext">{{$vouchar->firstName}} {{$vouchar->lastName}} ({{gclass_name($vouchar->class)->name}})-({{gsection_name($vouchar->section)->name}})</p></td>
					               {{-- <td class="tableitem"><p class="itemtext">{{$vouchar->payableAmount}}</p></td> --}}
					                <td class="tableitem"><p class="itemtext">{{$vouchar->payableAmount}}</p></td>
					                <!-- <td class="tableitem"><p class="itemtext">$375.00</p></td> -->
					            </tr>
				            @endforeach
			           		<tr class="service">
				                <td class="tableitem" ><p class="itemtext"></p></td>
				                <td class="tableitem"><h4>Total Amount: {{$total}}</h4></td>
				            </tr>
		                <tr class="service">
			                <td class="tableitem" ><p class="itemtext"></p></td>
			                <td class="tableitem"><h4>Paid Amount: {{$paid}}</h4></td>
		                </tr>
						               <!--  <tr>
									    <td colspan="3">Sum: $180</td>
									  </tr> -->
						                <!-- <td class="tableitem"><p class="itemtext">$375.00</p></td> -->
						            <!-- </tr>
						             <tr class="service">
						                <td class="tableitem" ><p class="itemtext"></p></td>
						               
						                <td class="tableitem"><p class="itemtext">Paid:</p></td>
						            </tr> -->
             				</tbody>
						</table>
					</div><!--End Table-->

					 <div id="legalcopy" style="margin-left:745px">
						 <div class="sig-n-stamp"><strong >{{auth()->user()->group}}:{{auth()->user()->login}}</strong>  
						</div> 
						<div class="sig-n-stamp">
					        <p>Accountant Sig : ___________ </p>
					        <p>For Detail     : {{getinstitueinfo()->phoneNo}}</p>
					    </div>
					
					</div>

				</div><!--End InvoiceBot-->
  </div><!--End Invoice-->
</div>

<script type="text/javascript">
	//window.print();
	if(navigator.userAgent.toLowerCase().indexOf('chrome') > -1){   // Chrome Browser Detected?
    window.PPClose = false;                                     // Clear Close Flag
    window.onbeforeunload = function(){                         // Before Window Close Event
        if(window.PPClose === false){                           // Close not OK?
            return 'Leaving this page will block the parent window!\nPlease select "Stay on this Page option" and use the\nCancel button instead to close the Print Preview Window.\n';
        }
    }                   
    window.print();                                             // Print preview
    window.PPClose = true;                                      // Set Close Flag to OK.
}
</script>


</body>
</html>