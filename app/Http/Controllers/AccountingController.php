<?php

namespace App\Http\Controllers;

use DB;
use App\Models\FeeCol;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Institute;
use App\Models\Accounting;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AccountSector;
use App\Models\AccountingSetting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class Damidata
{
}
class AccountingController extends BaseController
{

	public function __construct()
	{
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('sectorDelete','incomeDelete','expenceDelete')));*/
		$this->middleware('auth');
		//$this->middleware('userAccess',array('only'=> array('sectorDelete','incomeDelete','expenceDelete')));

	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$accounting = AccountingSetting::first();
		if (empty($accounting)) {
			$accounting = new Damidata();
			$accounting->company_id = '';
			$accounting->api_link = '';
			$accounting->username = '';
			$accounting->password = '';
		}

		return View('app.accounting', compact('accounting'));
	}

	public function store(Request $request)
	{
		$rules = [
			'company_id' => 'required',
			'api_link'   => 'required',
			'username'   => 'required',
			'password'   => 'required',

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting')->withInput($request->all())->withErrors($validator);
		} else {
			//AccountingSetting::delete();
			DB::table("accounting_settings")->delete();

			$accountingsetting = new AccountingSetting();

			$accountingsetting->company_id = $request->input('company_id');
			$accountingsetting->api_link   = $request->input('api_link');
			$accountingsetting->username   = $request->input('username');
			$accountingsetting->password   = $request->input('password');
			$accountingsetting->save();

			return Redirect::to('/accounting')->with("success", "Accounting Setting Saved Succesfully.");
		}
	}
	public function sectors()
	{
		$sectors = AccountSector::all();
		$sector = array();
		//return View::Make('app.accountsector',compact('sectors','sector'));
		return View('app.accountsector', compact('sectors', 'sector'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function sectorCreate(Request $request)
	{
		$rules = [
			'name' => 'required',
			'type' => 'required'

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/sectors')->withInput($request->all())->withErrors($validator);
		} else {
			$sector = new AccountSector();
			$sector->name = $request->input('name');
			$sector->type = $request->input('type');
			$sector->save();
			return Redirect::to('/accounting/sectors')->with("success", "Accounting Sector Created Succesfully.");
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function sectorEdit($id)
	{
		$sectors = AccountSector::all();
		$sector = AccountSector::find($id);
		//return View::Make('app.accountsector',compact('sectors','sector'));
		return View('app.accountsector', compact('sectors', 'sector'));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sectorUpdate(Request $request)
	{
		$rules = [
			'name' => 'required',
			'type' => 'required'

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/sectoredit/' . $request->input('id'))->withInput($request->all())->withErrors($validator);
		} else {
			$sector = AccountSector::find($request->input('id'));
			$sector->name = $request->input('name');
			$sector->type = $request->input('type');
			$sector->save();
			return Redirect::to('/accounting/sectors')->with("success", "Accounting Sector Updated Succesfully.");
		}
	}


	/**
	 * Delete the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sectorDelete($id)
	{
		$sector = AccountSector::find($id);
		$sector->delete();
		return Redirect::to('/accounting/sectors')->with("success", "Accounting Sector Deleted Succesfully.");
	}


	public function  income()
	{
		$sectors = AccountSector::select('id', 'name')->where('type', '=', 'Income')->orderby('id', 'asc')->get();
		//return View::Make('app.accountIncome',compact('sectors'));
		return View('app.accountIncome', compact('sectors'));
	}
	public function  incomeCreate(Request $request)
	{
		$rules = [
			'name'   => 'required',
			'amount' => 'required|between:0,99.99',
			'date'   => 'required'

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/income')->withInput($request->all())->withErrors($validator);
		} else {
			$sectors   = $request->input('name');
			$amount    = $request->input('amount');
			$date      = $request->input('date');
			$desc      = $request->input('description');
			$sectorIds = array_keys($sectors);
			// $amountIds = array_keys($amount);
			//$dateIds = array_keys($date);
			$dataToSave = array();
			foreach ($sectorIds as $id) {
				if ($amount[$id] !== "" && $date[$id] !== "") {
					if (is_numeric($amount[$id])) {
						$data = array("name" => $sectors[$id], "amount" => $amount[$id], "date" => $date[$id], "description" => $desc[$id]);
						array_push($dataToSave, $data);
					} else {
						$errorMessages = new \Illuminate\Support\MessageBag;
						$errorMessages->add('Invalid', 'Amount must be a number.');
						//return Redirect::to('/accounting/income')->withInput($request->all())->withErrors($errorMessages);
					}
				}
			}

			$counter = 0;
			foreach ($dataToSave as $singleData) {
				$income = new Accounting();
				$income->name = $singleData["name"];
				$income->type = "Income";
				$income->amount = $singleData["amount"];
				$income->description = $singleData["description"];
				if ($singleData["description"] == '') {
					$income->description = '';
				}


				$income->date = $this->parseAppDate($singleData["date"]);
				$income->save();
				$counter++;
			}


			return Redirect::to('/accounting/income')->with("success", $counter . "'s income saved Succesfully.");
		}
	}
	public  function incomeList(Request $request)
	{
		$incomes = array();
		if ($request->input('year') == '') {
			$year = '';
		} else {
			$year = $request->input('year');
		}
		if ($request->input('month') == '') {
			$mn = '';
		} else {
			$mn = $request->input('month');
			$month  = date('m', strtotime($mn));
		}
		if ($mn != '' && $year != '') {

			$incomes = DB::select(DB::raw("SELECT * FROM accounting WHERE type ='Income' and YEAR(date)='" . $year . "'  and MONTH(date)='" . $month . "'"));
		}
		//echo "<pre>".$mn.$year;print_r($incomes);
		//return View::Make('app.accountIncomeView',compact('incomes'));
		return View('app.accountIncomeView', compact('incomes', 'year', 'mn'));
	}
	public  function incomeListPost(Request $request)
	{
		$year = trim($request->input('year'));
		$mn   = trim($request->input('month'));
		$month  = date('m', strtotime($mn));



		$incomes = DB::select(DB::raw("SELECT * FROM accounting WHERE type ='Income' and YEAR(date)='" . $year . "'  and MONTH(date)='" . $month . "'"));
		//return View::Make('app.accountIncomeView',compact('incomes'));
		return View('app.accountIncomeView', compact('incomes', 'year', 'mn'));
	}

	public function  incomeEdit(Request $request, $id)
	{
		$income = Accounting::find($id);
		$year = trim($request->input('year'));
		$month   = trim($request->input('month'));
		//return View::Make('app.accountIncomeEdit',compact('income'));
		return View('app.accountIncomeEdit', compact('income', 'year', 'month'));
	}
	public function incomeUpdate(Request $request)
	{
		$rules = [
			'name' => 'required',
			'amount' => 'required|between:0,99.99',
			'date'   => 'required'

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/incomeedit/' . $request->input('id') . '?year=' . $request->input('year') . '&month=' . $request->input('month'))->withErrors($validator);
		} elseif (!is_numeric($request->input('amount'))) {
			$errorMessages = new \Illuminate\Support\MessageBag;
			$errorMessages->add('Invalid', 'Amount must be a number.');
			return Redirect::to('/accounting/incomeedit/' . $request->input('id'))->withErrors($errorMessages);
		} else {
			$income = Accounting::find($request->input('id'));
			$income->amount = $request->input('amount');
			$income->description = $request->input('description');
			if ($request->input('description') == '') {
				$income->description = '';
			}
			$income->date = $this->parseAppDate($request->input('date'));
			$income->save();

			return Redirect::to('/accounting/incomelist?year=' . $request->input('year') . '&month=' . $request->input('month'))->with("success", "Income Updated Succesfully.");
		}
	}
	public function incomeDelete(Request $request, $id)
	{
		$income = Accounting::find($id);
		$income->delete();
		return Redirect::to('/accounting/incomelist?year=' . $request->input('year') . '&month=' . $request->input('month'))->with("success", "Income Deleted Succesfully.");
	}

	public function  expence()
	{
		$sectors = AccountSector::select('id', 'name')->where('type', '=', 'Expence')->orderby('id', 'asc')->get();
		//return View::Make('app.accountExpence',compact('sectors'));
		return View('app.accountExpence', compact('sectors'));
	}
	public function expenceCreate(Request $request)
	{
		$rules = [
			'name'   => 'required',
			'amount' => 'required|between:0,99.99',
			'date'   => 'required'
		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/expence')->withInput($request->all())->withErrors($validator);
		} else {
			$sectors = $request->input('name');
			$amount  = $request->input('amount');
			$date    = $request->input('date');
			$desc    = $request->input('description');

			$sectorIds = array_keys($sectors);
			// $amountIds = array_keys($amount);
			//$dateIds = array_keys($date);
			$dataToSave = array();
			foreach ($sectorIds as $id) {
				if ($amount[$id] !== "" && $date[$id] !== "") {
					if (is_numeric($amount[$id])) {
						if ($desc[$id] === '') {
							$desc[$id] = '';
						}
						$data = array("name" => $sectors[$id], "amount" => $amount[$id], "date" => $date[$id], "description" => $desc[$id]);
						array_push($dataToSave, $data);
					} else {
						$errorMessages = new \Illuminate\Support\MessageBag;
						$errorMessages->add('Invalid', 'Amount must be a number.');
						//return Redirect::to('/accounting/expence')->withInput($request->all())->withErrors($errorMessages);
					}
				}
			}

			$counter = 0;
			foreach ($dataToSave as $singleData) {
				$income = new Accounting();
				$income->name = $singleData["name"];
				$income->type = "Expence";
				$income->amount = $singleData["amount"];
				$income->description = $singleData["description"];
				if ($singleData["description"] == '') {
					$income->description = '';
				}
				$income->date = $this->parseAppDate($singleData["date"]);
				$income->save();
				$counter++;
			}


			return Redirect::to('/accounting/expence')->with("success", $counter . "'s Expence saved Succesfully.");
		}
	}
	public  function expenceList(Request $request)
	{
		$expences = array();
		//return View::Make('app.accountExpenceView',compact('expences'));
		if ($request->input('year') == '') {
			$year = '';
		} else {
			$year = $request->input('year');
		}
		if ($request->input('month') == '') {
			$mn = '';
		} else {
			$mn     = $request->input('month');
			$month  = date('m', strtotime($mn));
		}
		if ($mn != '' && $year != '') {

			$expences = DB::select(DB::raw("SELECT * FROM accounting WHERE type ='Expence' and YEAR(date)='" . $year . "'  and MONTH(date)='" . $month . "'"));
		}
		return View('app.accountExpenceView', compact('expences', 'year', 'mn'));
	}
	public  function expenceListPost(Request $request)
	{
		$year     = trim($request->input('year'));
		$mn       = trim($request->input('month'));
		$month    = date('m', strtotime($mn));
		$expences = DB::select(DB::raw("SELECT * FROM accounting WHERE type ='Expence' and YEAR(date)='" . $year . "' and MONTH(date)='" . $month . "'"));
		//return View::Make('app.accountExpenceView',compact('expences'));
		return View('app.accountExpenceView', compact('expences', 'year', 'mn'));
	}

	public function  expenceEdit(Request $request, $id)
	{
		$expence  = Accounting::find($id);
		$year    = trim($request->input('year'));
		$month   = trim($request->input('month'));
		return View('app.accountExpenceEdit', compact('expence', 'year', 'month'));
	}
	public function expenceUpdate(Request $request)
	{
		$rules = [
			'name'   => 'required',
			'amount' => 'required|between:0,99.99',
			'date'   => 'required'

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('/accounting/expenceedit/' . $request->input('id') . '?year=' . $request->input('year') . '&month=' . $request->input('month'))->withErrors($validator);
		} elseif (!is_numeric($request->input('amount'))) {
			$errorMessages = new Illuminate\Support\MessageBag;
			$errorMessages->add('Invalid', 'Amount must be a number.');
			return Redirect::to('/accounting/expenceedit/' . $request->input('id') . '?year=' . $request->input('year') . '&month=' . $request->input('month'))->withErrors($errorMessages);
		} else {
			$income = Accounting::find($request->input('id'));
			$income->amount = $request->input('amount');
			$income->description = $request->input('description');
			if ($request->input('description') == '') {
				$income->description = '';
			}
			$income->date = $this->parseAppDate($request->input('date'));
			$income->save();

			return Redirect::to('/accounting/expencelist?year=' . $request->input('year') . '&month=' . $request->input('month'))->with("success", "Expence Updated Succesfully.");
		}
	}
	public function expenceDelete(Request $request, $id)
	{
		$income = Accounting::find($id);
		$income->delete();
		return Redirect::to('/accounting/expencelist?year=' . $request->input('year') . '&month=' . $request->input('month'))->with("success", "Expence Deleted Succesfully.");
	}

	public  function getReport()
	{
		$formdata = array('', '');
		$datas = array();
		//return View::Make('app.accountingReport',compact('datas','formdata'));
		return View('app.accountingReport', compact('datas', 'formdata'));
	}
	public  function printReport($rtype, $fdate, $tdate)
	{

		if ($rtype == "" && $fdate == "" && $tdate == "") {
			return Redirect::to('/accounting/report')->with("noresult", "Data Not Found!");
		} else {

			$datas = Accounting::select('name', 'amount', 'date', 'description')->where('type', '=', $rtype)->where('date', '>=', $fdate)->where('date', '<=', $tdate)->get();
			$total = DB::select(DB::raw("SELECT sum(amount) as total FROM accounting where type='" . $rtype . "' and date >='" . $fdate . "' and date <='" . $tdate . "'"));

			if ($rtype == 'Income') {
				$tutionfees = FeeCol::join('billHistory', 'stdBill.billNo', '=', 'billHistory.billNo')->select(DB::RAW('sum(stdBill.payableAmount) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
					//->where('class',$request->input('class'))
					//->groupBy('month')
					->where('billHistory.title', 'monthly')
					->whereDate('stdBill.updated_at', '>=', $fdate . ' 00:00:00')
					->whereDate('stdBill.updated_at', '<=', $tdate . ' 00:00:00')
					->first();
				$otherfees = FeeCol::join('billHistory', 'stdBill.billNo', '=', 'billHistory.billNo')->select(DB::RAW('sum(stdBill.payableAmount) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
					//->where('class',$request->input('class'))
					//->groupBy('month')
					->where('billHistory.title', '<>', 'monthly')
					->whereDate('stdBill.updated_at', '>=', $fdate . ' 00:00:00')
					->whereDate('stdBill.updated_at', '<=', $tdate . ' 00:00:00')
					->first();
			} else {
				$otherfees  = array();
				$tutionfees = array();
			}

			if (!is_null($datas) && count($datas) > 0) {

				$formdata = array($this->getAppdate($fdate), $this->getAppdate($tdate), $rtype);
				$institute = Institute::select('*')->first();
				//return View::Make('app.accountreportprint', compact('datas','formdata','total','institute'));
				return View('app.accountreportprint', compact('datas', 'formdata', 'total', 'institute', 'tutionfees', 'otherfees', 'rtype'));
			} else {
				echo '<script> alert("Data Not Found!!!");window.close();</script> ';
			}
		}
	}
	public  function  getReportsum()
	{
		//return View::Make('app.accountingReportsum');
		return View('app.accountingReportsum');
	}
	public  function  printReportsum($fdate, $tdate)
	{
		if ($fdate == "" && $tdate == "") {
			return Redirect::to('/accounting/reportsum')->with("noresult", "Data Not Found!");
		} else {

			$incomes = Accounting::select('name', 'amount', 'description', 'date')->where('type', '=', 'Income')->where('date', '>=', $fdate)->where('date', '<=', $tdate)->get();

			$intotal = DB::select(DB::raw("SELECT sum(amount) as total FROM accounting where type='Income' and date >='" . $fdate . "' and date <='" . $tdate . "'"));

			$tutionfees = FeeCol::join('billHistory', 'stdBill.billNo', '=', 'billHistory.billNo')->select(DB::RAW('sum(stdBill.payableAmount) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
				//->where('class',$request->input('class'))
				//->groupBy('month')
				->where('billHistory.title', 'monthly')
				->whereDate('stdBill.updated_at', '>=', $fdate . ' 00:00:00')
				->whereDate('stdBill.updated_at', '<=', $tdate . ' 00:00:00')
				->first();
			$otherfees = FeeCol::join('billHistory', 'stdBill.billNo', '=', 'billHistory.billNo')->select(DB::RAW('sum(stdBill.payableAmount) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
				//->where('class',$request->input('class'))
				//->groupBy('month')
				->where('billHistory.title', '<>', 'monthly')
				->whereDate('stdBill.updated_at', '>=', $fdate . ' 00:00:00')
				->whereDate('stdBill.updated_at', '<=', $tdate . ' 00:00:00')
				->first();
			//echo "<pre>";print_r($tutionfees->toArray());exit;

			$expences = Accounting::select('name', 'amount', 'description', 'date')->where('type', '=', 'Expence')->where('date', '>=', $fdate)->where('date', '<=', $tdate)->get();
			$extotal = DB::select(DB::raw("SELECT sum(amount) as total FROM accounting where type='Expence' and date >='" . $fdate . "' and date <='" . $tdate . "'"));
			$intotals = $intotal[0]->total + $tutionfees->paiTotal + $otherfees->paiTotal;
			//$balance = array($intotal[0]->total-$extotal[0]->total);
			$balance = array($intotals - $extotal[0]->total);


			$formdata = array($this->getAppdate($fdate), $this->getAppdate($tdate));
			$institute = Institute::select('*')->first();

			//return View::Make('app.accountreportprintsum', compact('datas','formdata','incomes','expences','intotal','extotal','balance','institute'));
			return View('app.accountreportprintsum', compact('datas', 'formdata', 'incomes', 'expences', 'intotal', 'extotal', 'balance', 'institute', 'intotals', 'tutionfees', 'otherfees'));
		}
	}

	private function  parseAppDate($datestr)
	{
		$date = explode('/', $datestr);
		return $date[2] . '-' . $date[1] . '-' . $date[0];
	}
	private function  getAppdate($datestr)
	{
		$date = explode('-', $datestr);
		return $date[2] . '/' . $date[1] . '/' . $date[0];
	}
}
