<?php
if($invoice) { 
    // print_r($warehouse_expense);exit;
$info = $invoice[0];
$dispatchMeta = json_decode($info['dispatchMeta'],true);
$invoicePDF = $cExpense = '';
$expense = array();
if($expName){
    for($e=0;count($expName)>$e;$e++){
        if(array_key_exists($expName[$e],$expense)){
			$expense[$expName[$e]]['price'] = $expense[$expName[$e]]['price'] + $expPrice[$e];
			$expense[$expName[$e]]['unit'][] = $expPrice[$e];
		} else {
			$expense[$expName[$e]] = array('price'=>$expPrice[$e],'unit'=>array($expPrice[$e]));
		}
    }
    if($childTrailer){
        foreach($childTrailer as $val){ 
        	$childDispMetaArr = json_decode($val['dispatchMeta'],true);
        	if($childDispMetaArr){
        		if($childDispMetaArr['expense']){
        			$cExpense = 'true';
        		}
        	}
        }
    }
} else {
    if($childTrailer){
        foreach($childTrailer as $val){ 
            $childDispMetaArr = json_decode($val['dispatchMeta'],true);
            if($childDispMetaArr){
                if($childDispMetaArr['expense']){
                    foreach($childDispMetaArr['expense'] as $cVal) {
                        $cExpense = 'true';
                        if(array_key_exists($cVal[0],$expense)){
                            $expense[$cVal[0]]['price'] = $expense[$cVal[0]]['price'] + $cVal[1];
                            $expense[$cVal[0]]['unit'][] = $cVal[1];
                        } else {
                            $expense[$cVal[0]] = array('price'=>$cVal[1],'unit'=>array($cVal[1]));
                        }
                    }
                }
            }
        }
    }

   $expense = isset($warehouse_expense) ? $warehouse_expense : [];
    if (!empty($customExpenseDetails)) {
        foreach ($customExpenseDetails as $ce) {
            $title = $ce['title'];
            $value = $ce['value'];
            $type = ($value < 0) ? 'Negative' : 'Positive';
            if (array_key_exists($title, $expense)) {
                $expense[$title]['price'] += $value;
                $expense[$title]['unit'][] = $value;
            } else {
                $expense[$title] = [
                    'price' => $value, 
                    'unit' => [$value],
                    'type'  => $type
                ];
            }
        }
    }

	// print_r($warehouse_expense);exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
    @font-face {
    font-family: 'calibri';
    src: url('<?php echo base_url()?>assets/css/Calibri-Regular.ttf');
}
body::before {content: '';position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.9);  z-index: -1;}
body {font-family: 'calibri';color: #000;font-size: 17px;line-height: 24px;padding: 0;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
.invoice-box {position:relative; width: 1000px;padding:15px; margin: auto;font-size: 17px;line-height: 24px;font-family: 'calibri';color: #000;}

.invoice-box table {width: 1000px;line-height: inherit;text-align: left;border-collapse: collapse;position:relative;z-index:999;}
.invoice-box table td {vertical-align: top;}
.invoice-box .invoicetb table td {vertical-align: middle;}
.invoice-box table td td {padding: 8px;}
.invoice-box table tr td:nth-child(2) {/*text-align: right;*/}
.invoice-box table tr.top table td.title {font-size: 45px;line-height: 45px;color: #333;}
.invoice-box table tr.information table td {padding-bottom: 10px;}
.invoice-box table tr.heading td {color:#fff;background: #1f3864;border-bottom: 1px solid #ddd;font-weight: bold;}
.invoice-box table tr.details td {padding-bottom: 20px;}
.invoice-box table tr.total td:nth-child(2) {border-top: 2px solid #eee;font-weight: bold;}
.invoice-box table tr.notes  td{padding:0 17px 17px 10px; border:0px solid;}
.invoice-box table tr.notes td td {padding: 3px;}
.invoice-box table tr.notes td {font-size: 17px;color: #666;}
table tr.notes table{margin-bottom:5px;width:100%;max-width:1000px;}
.invoicetb{padding:17px 17px 0 10px;}
.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 17px;}
.invoicetb table{border: 0px solid #bab7b7;}
.footer {color:red;margin-top: 20px;text-align: center;font-weight: bold;font-size: 19px;letter-spacing: 1px;}
	</style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-box-bg"></div>
        <table cellpadding="0" cellspacing="0">
           <?php 
           if($invoiceNotes != ''){ $info['invoiceNotes'] = $invoiceNotes; }
           if($tracking != ''){ $info['tracking'] = $tracking; }
           ?>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="65%">
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:480px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                <strong>Accounts: </strong>accounts@patransportca.com<br>
                                <strong>Director Ops: </strong> akash@patransportca.com
                            </td>
                            <td width="35%" align="left">
                                <h3 style="display:inline;font-size:40"><br>INVOICE</h3><br><br>
								<strong>Invoice No.:</strong> <?=$info['invoice']?><br>
                                <strong>Invoice Date:</strong> <?php if($invoiceDate != '') { echo date('m-d-Y',strtotime($invoiceDate)); }
                                elseif($info['invoiceDate'] != '0000-00-00') { echo date('m-d-Y',strtotime($info['invoiceDate'])); } ?><br><br>
								<img src="<?php echo base_url()?>assets/images/barcode.jpg" style="width:100%; max-width:150px;">
							</td>
                        </tr>
						<tr>
                            <td>
                                <strong>Bill To:<br>
                                <?=$info['ccompany']?></strong><br>
                                <?=str_replace('--','<br>',$info['caddress'])?>
                            </td>
                            <td align="left">
                                <strong>Shipping Contact:</strong><br>
                                <?php 
                                if($contactPerson != '') {
                                    echo ''.$contactPerson.'<br>'; 
                                }elseif($info['contactPerson'] != '') { 
                                    echo ''.$info['contactPerson'].'<br>'; 
                                } 
                                $designation = trim(($cdesignation != '') ? $cdesignation : $info['cdesignation']);
								$department  = trim(($cdepartment != '') ? $cdepartment : $info['cdepartment']);
								if ($designation !== '' || $department !== '') {
									echo $designation . (($designation && $department) ? ', ' : '') . $department . '<br>';
								}
                                if($cemail != '') { echo '(E) '.$cemail.'<br>'; } 
                                elseif($info['cemail'] != '') { echo '(E) '.$info['cemail'].'<br>'; } 
                                if($cphone != '') { echo '(O) '.$cphone.'<br>'; } 
                                elseif($info['cphone'] != '') { echo '(O) '.$info['cphone'].'<br>'; } 
                                ?>
                            </td>
                        </tr>
						<tr>
                            <td>
                                <strong>Warehouse Location</strong><br>
                                <?php 
                                if($pickup == '') { $pickup = $info['pplocation'].' ['.$info['ppcity'].']'; }
                                echo  $pickup;
								echo '<br>'; 
								?>
                            </td>
                            <td align="left">
								<?php 
                                // print_r($invoice['dispatchInfoDetails']);exit;
                                    // if($invoice['dispatchInfoDetails'][0]['dispatchInfoId'] == 10){                                   
                                      if (!empty($invoice['dispatchInfoDetails'])) {
                                        foreach ($invoice['dispatchInfoDetails'] as $detail) {
                                            if (empty($detail['dispatchInfoId']) && empty($detail['dispatchValue']) && empty($detail['dispatchInfoTitle'])) {
                                                continue;
                                            }
                                            echo '<strong>' . $detail['dispatchInfoTitle'] . ': ' . $detail['dispatchValue'] . '</strong><br>';
                                        }
                                    }	
                                    // }						
                                    $trailer = array();			
                                 ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
			<tr class="top">
                <td colspan="2" class="invoicetb">
                    <table border="1">
                        <tr class="heading">
							<td align="center" width="18%">Shipment&nbsp;Date</td>
							<td align="center" width="20%">PA Reference #</td>
							<td width="48%">Description</td>
							<td align="right" width="14%">Amount</td>
						</tr>
						<?php 
						$puDate = date('m-d-Y',strtotime($info['pudate']));
						if($extraDispatch){
							foreach($extraDispatch as $exDispatch){
								if($exDispatch['pd_type']=='pickup'){
									$puDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
								}
							}
						}?>	
							<tr><td align="center"><?php echo $puDate; ?></td>
							<td align="center"><?=$info['invoice']?></td>
							<td align="center" colspan="2"><strong>Itemization of Charges</strong></td></tr>
							<?php  
							 
						    $discountAmt = $subtotal = $totalAmt = 0;
                            $tr = $emptyTr = '';
                            if ($expense) {
                                $positiveRows = '';
                                $negativeRows = '';
                                foreach ($expense as $key => $expVal) {
                                    $row = '<tr><td colspan="3" align="right"><strong>'.$key.'</strong>';
                                    if ($cExpense == 'true' && count($expVal['unit']) > 1) { 
                                        $row .= ' ('.implode(' + ', $expVal['unit']).') '; 
                                    }
                                    $row .= '</td><td align="right">';
                                    if (isset($expVal['type']) && $expVal['type'] === 'Negative') { 
                                        $discountAmt += abs($expVal['price']);
                                        $row .= '<span style="color:red;">$ '.number_format(abs($expVal['price']),2).'</span>';
                                        $negativeRows .= $row.'</td></tr>';  
                                    } else {
                                        $totalAmt += $expVal['price'];
                                        $row .= '$ '.number_format($expVal['price'],2);
                                        $positiveRows .= $row.'</td></tr>';  
                                    }
                                }
                                $emptyTr .= '<tr><td>&nbsp;</td><td></td><td colspan="2"></td></tr>';
                                $subtotal = $totalAmt - $discountAmt;
                                $tr = $emptyTr.$positiveRows.$negativeRows;
                            } else {
                                $subtotal = $info['parate'];
                            }
							?> 
							<?=$tr?>
							<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
							$grandTotal = $subtotal;
							echo number_format($grandTotal,2)?></strong></td></tr>
						<?php  ?>
                    </table>
                </td>
            </tr>
            <br>
            <tr class="notes">
                <td colspan="2">
					<table border="0" width="100%">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 17px;display: block;padding:0px;margin:0;">Notes:</p></td>
						</tr>
					</table>
                    &nbsp;&nbsp;&nbsp;&nbsp;1. Include the invoice number in your payment method to ensure accurate processing.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;2. Report service discrepancies within 10 days of receipt for resolution.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;3. Payment defaults may result in recovery actions; responsible parties will bear all associated legal costs.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;4. This invoice is legally binding. Retain a copy for your records.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;5. Make checks payable to PA Transport LLC.</strong>
                </td>
            </tr>
        </table>
        <div class="footer">
            Thank you for your business!<br>
        </div>
    </div>
</body>
</html>
<?php }
else {
    echo '<h2>This Invoice is not exit please check URL.</h2>';
}
// echo 'test';exit;