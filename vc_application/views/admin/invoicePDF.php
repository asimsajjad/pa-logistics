<?php
if($invoice) { 
$info = $invoice[0];
$rateArr = array($info['rate']);
$dispatchMeta = json_decode($info['dispatchMeta'],true);
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
    if($childTrailer) {
    	foreach($childTrailer as $val){ 
    		$rateArr[] = $val['rate'];
    	}
    }
} else {
    if($childTrailer) {
    	foreach($childTrailer as $val){ 
    		$rateArr[] = $val['rate'];
    		$childDispMetaArr = json_decode($val['dispatchMeta'],true);
    		if($childDispMetaArr){
    			if($childDispMetaArr['expense']){
    				foreach($childDispMetaArr['expense'] as $cVal) {
    					if(array_key_exists($cVal[0],$expense)){
    						//$expense[$cVal[0]] = $expense[$cVal[0]] + $cVal[1];
    						$expense[$cVal[0]]['price'] = $expense[$cVal[0]]['price'] + $cVal[1];
    						$expense[$cVal[0]]['unit'][] = $cVal[1];
    					} else {
    						//$expense[$cVal[0]] = 0 + $cVal[1];
    						$expense[$cVal[0]] = array('price'=>$cVal[1],'unit'=>array($cVal[1]));
    					}
    				}
    			}
    		}
    	}
    }
    if($dispatchMeta['expense']) { 
    	foreach($dispatchMeta['expense'] as $expVal) {
    		if(array_key_exists($expVal[0],$expense)){
    			//$expense[$expVal[0]] = $expense[$expVal[0]] + $expVal[1];
    			$expense[$expVal[0]]['price'] = $expense[$expVal[0]]['price'] + $expVal[1];
    			$expense[$expVal[0]]['unit'][] = $expVal[1];
    		} else {
    			//$expense[$expVal[0]] = 0 + $expVal[1];
    			$expense[$expVal[0]] = array('price'=>$expVal[1],'unit'=>array($expVal[1]));
    		}
    	}
    }
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
body {font-family: 'calibri';color: #000;font-size: 12px;line-height: 24px;padding: 0;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
.invoice-box {position:relative; width: 1000px;margin: auto;font-size: 12px;line-height: 24px;font-family: 'calibri';color: #000;}

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
.invoice-box table tr.notes  td{padding:0 12px 12px 12px;border:0px solid;}
.invoice-box table tr.notes td td {padding: 3px;}
.invoice-box table tr.notes td {font-size: 12px;color: #666;}
table tr.notes table{margin-bottom:5px;}
.invoicetb{padding:12px 12px 0 12px;}
.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 12px;}
.invoicetb table{border: 0px solid #bab7b7;}
.footer {color:red;margin-top: 20px;text-align: center;font-weight: bold;font-size: 19px;letter-spacing: 1px;}
.invoice-box table tr .invoicetb td.childinvoicetbl{padding:0;}
.invoice-box table tr .invoicetb td.childinvoicetbl table{width:100%;}
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
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:350px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                Accounts: Accounts@patransportca.com<br>
                                Fleet Manager: Akash@patransportca.com
                            </td>
                            <td width="35%" align="left">
                                <h3 style="display:inline;font-size:30"><br>INVOICE</h3><br><br>
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
                                if($contactPerson != '') { echo ''.$contactPerson.'<br>'; } 
                                elseif($info['contactPerson'] != '') { echo ''.$info['contactPerson'].'<br>'; } 
                                if($cdepartment != '') { echo ''.$cdepartment.'<br>'; } 
                                elseif($info['cdepartment'] != '') { echo ''.$info['cdepartment'].'<br>'; } 
                                if($cemail != '') { echo '(E) '.$cemail.'<br>'; } 
                                elseif($info['cemail'] != '') { echo '(E) '.$info['cemail'].'<br>'; } 
                                if($cphone != '') { echo '(O) '.$cphone.'<br>'; } 
                                elseif($info['cphone'] != '') { echo '(O) '.$info['cphone'].'<br>'; } 
                                ?>
                            </td>
                        </tr>
						<tr>
                            <td>
                                <strong>Dispatch Information</strong><br>
                                From:  <?php 
                                if($pickup != '') { echo $pickup; }
                                else { echo $info['pplocation'].' ['.$info['ppcity'].']'; }
                                echo '<br>'; ?>
                                To: 
                                <?php 
                                if($dropoff == '') { $dropoff = $info['ddlocation'].' ['.$info['ddcity'].']'; }
                                
                                if($extraDispatch) { 
                                    $d = 1;
                                    echo '<strong>DO # '.$d.': </strong>'.$dropoff.'';
                                    if(is_array($dropoffExtra)){
                                        foreach($dropoffExtra as $ex){
                                            $d++;
                                            echo '<br><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DO # '.$d.': </strong>'.$ex; 
                                        }
                                    } else {
                                        foreach($extraDispatch as $ex){
                                            if($ex['pd_type']=='dropoff'){
                                                $d++;
                                                echo '<br><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DO # '.$d.': </strong>'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'; 
                                            }
                                        }
                                    }
                                } else {
                                    echo $dropoff.'';
                                }
                                ?>
                            </td>
                            <td align="left">
                                <strong><?=$trackingLabel?>: <?=$info['tracking']?></strong><br>
                                <strong><?php 
                                    if($trailerValLabel != ''){ echo ''.$trailerValLabel.': '; }
                                    else { echo 'Trailer No.: '; }
                                        
                                    $trailer = array();
                                    if(strstr($info['trailer'],',')) {
                                        $trailer = explode(',',$info['trailer']);
                                    }
                                    elseif($info['trailer'] != ''){ $trailer[] = $info['trailer']; }
                                   
									foreach($childTrailer as $val){ $trailer[] = $val['trailer']; }
									
                                    if($trailerVal != ''){ echo $trailerVal; }
									elseif(count($trailer) == 0) { echo 'N/A'; } 
                                    //elseif(count($trailer) > 5) { echo 'Multiple'; } 
                                    else { 
										$trailerss = implode(', ',$trailer); 
										echo str_replace(' ,',',',str_replace('TBA','N/A',$trailerss));
									} 
                                    ?></strong><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
			
			<tr class="top">
                <td colspan="2" class="invoicetb">
					<?php 
					if($childTrailer) { 
						$parate = $info['rate'];
						$showChild = 'false';
						$uniqueRate = array_unique($rateArr);
						if($unitprice > 0) { 
							$parate = $info['parate'] = $unitTotal * $unitprice;
							$unit = $unitTotal;
							$showChild = 'false';
						}
						elseif(count($uniqueRate) === 1) { 
							$unit = 1 + count($childTrailer); 
							$parate = $info['parate']; 
							$showChild = 'false';
						} else {
							$unit = 1;
							// disable child invoice
							$showChild = 'false';
							$parate = array_sum($rateArr);
							// if you want to make enable child invoice
							//$showChild = 'true';
						}
						//echo ' -'.$showChild.'- dd';
					?>
					<table border="1">
                        <tr class="heading">
							<td align="center" width="13%">Shipment&nbsp;Date</td>
							<td align="center" width="17%">PA Reference #</td>
							<td width="31%">Dispatch Information</td>
							<td align="center" width="10%">Unit Rate</td>
							<td align="center" width="12%">No. of Units</td>
							<td align="right" width="17%">Total Amount</td>
						</tr>
						<tr>
							<td align="center" <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> >
							    <?php 
							    $dropDate = date('m-d-Y',strtotime($info['dodate']));
							    if($extraDispatch){
							        foreach($extraDispatch as $exDispatch){
							            if($exDispatch['pd_type']=='dropoff'){
							                $dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							            }
							        }
							    }
							    echo $dropDate;
							    ?>
							</td>
							<td align="center" <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> ><?=$info['invoice']?></td>
							<td <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> ><?=$info['invoiceNotes']?> <?php //if(count($trailer) > 5) { echo '(Trailer No.: '.implode(', ',$trailer).')'; } ?></td>
							<td align="center">$ <?php if($unitprice > 0) { echo $unitprice; } else { echo $info['rate']; } ?></td>
							<td align="center"><?php echo $unit; ?></td>
							<td align="right">$ <?=$parate?></td>
						</tr>
						<?php 
						if($showChild == 'true') {
							foreach($childTrailer as $val){ 
								echo '<tr>
								<td align="center">$ '.$val['rate'].'</td>
								<td align="center">1</td>
								<td align="right">$ '.$val['rate'].'</td>
								</tr>';
							} 
						}
						
							//$uniqueRate = array_unique($rateArr);
							/*if(count($uniqueRate) != count($childTrailer)){
								?>
								<td colspan="3" class="childinvoicetbl">
									<table border="0">
									<!--tr style="display:none"><td>Unit Rate</td><td>No. of Units</td><td>Total Amount</td></tr-->
									<?php 
									echo '<tr>
										<td align="center" width="22%">$ '.$info['rate'].'</td>
										<td align="center" width="34%">1</td>
										<td align="right" width="44%">$ '.$info['rate'].'</td></tr>';
									foreach($childTrailer as $val){ 
										echo '<tr>
										<td align="center" width="22%">$ '.$val['rate'].'</td>
										<td align="center" width="34%">1</td>
										<td align="right" width="44%">$ '.$val['rate'].'</td></tr>';
									} 
									?>
									</table>
								</td>
								<?php 
							} else {
							?>
							<td align="center">$ <?=$info['rate']?></td>
							<td align="center"><?php echo (1 + count($childTrailer)); ?></td>
							<td align="right">$ <?=$info['parate']?></td>
							<?php }*/ ?>
						
						<?php 
						echo '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
						for($c=count($expense);$c<5;$c++) {
							echo '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
						}
						?>
						<tr><td colspan="5" align="right"><strong>Subtotal</strong></td><td align="right">$ <?=number_format($info['parate'],2)?></td></tr>
						<?php
						$totalAmt = $info['parate'] + 0;
						$discountAmt = 0;
						if($expense) { 
            				foreach($expense as $key=>$expVal) {
            				     if($key=='Discount'){
            				        //echo '-';
            				        $discountAmt = $discountAmt + $expVal['price'];
            				        $totalAmt = $totalAmt - $expVal['price'];
            				     } else {
            				         $totalAmt = $totalAmt + $expVal['price'];
            				     }
								$price = min($expVal['unit']);
								$sum = array_sum($expVal['unit']);
								$unit = ($sum / $price);
								$unit = round($unit);
								$unit = number_format($unit);

            				     echo '<tr><td></td><td></td>
								 <td align="right"><strong>'.$key.'</strong></td>
								 <td align="center">'.$price.'</td>
								 <td align="center">'.$unit.'</td>
								 <td  align="right">$ '.number_format($expVal['price'],2).'</td>
								 </tr>';
            				}
						} else {
						    echo '<tr><td colspan="5" align="right"><strong>Accessorial</strong></td><td  align="right">$ 0.00</td></tr>';
						    //echo '<tr><td colspan="5" align="right"><strong>Discount</strong></td><td  align="right">$ 0.00</td></tr>';
						}
						?> 
						<tr><td colspan="5" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
						$grandTotal = $info['parate'] + $discountAmt;
						echo number_format($grandTotal,2)?></strong></td></tr>
                    </table>
				<?php } else { ?>
					<table border="1">
                        <tr class="heading">
							<td align="center" width="18%">Shipment&nbsp;Date</td>
							<td align="center" width="20%">PA Reference #</td>
							<td width="48%">Description</td>
							<td align="right" width="14%">Amount</td>
						</tr>
						<?php 
						/*
						<tr>
							<td align="center">
							    <?php 
							    $dropDate = date('m-d-Y',strtotime($info['dodate']));
							    if($extraDispatch){
							        foreach($extraDispatch as $exDispatch){
							            if($exDispatch['pd_type']=='dropoff'){
							                $dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							            }
							        }
							    }
							    echo $dropDate;
							    ?>
							</td>
							<td align="center"><?=$info['invoice']?></td>
							<td><?=$info['invoiceNotes']?> <?php //if(count($trailer) > 5) { echo '(Trailer No.: '.implode(', ',$trailer).')'; } ?></td>
							<td align="right">$ <?=$info['parate']?></td>
						</tr>
						*/ 
						
						$dropDate = date('m-d-Y',strtotime($info['dodate']));
						if($extraDispatch){
							foreach($extraDispatch as $exDispatch){
								if($exDispatch['pd_type']=='dropoff'){
									$dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
								}
							}
						}
					   
						$discountAmt = $subtotal = $totalAmt = 0;
						$tr = $emptyTr = '';
						
						$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
						for($c=count($expense);$c<5;$c++) {
							$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
						}
						
						if($expense) { 
            				foreach($expense as $key=>$expVal) {
            				     $tr .= '<tr><td colspan="3" align="right"><strong>'.$key.'</strong></td><td  align="right">';
            				     if($key=='Discount'){
            				        $discountAmt = $discountAmt + $expVal['price']; 
            				     } else {
            				         $totalAmt = $totalAmt + $expVal['price'];
            				     }
            				     $tr .= '$ '.number_format($expVal['price'],2).'</td></tr>';
            				}
							$subtotal = ($info['parate'] + $discountAmt) - $totalAmt;
							//$subtotal = number_format($subtotal,2);
							echo '<tr><td align="center">'.$dropDate.'</td><td align="center">'.$info['invoice'].'</td><td>'.$info['invoiceNotes'].'</td><td align="right">$ '.number_format($subtotal,2).'</td></tr>'.$emptyTr.'<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">$ '.number_format($subtotal,2).'</td></tr>'.$tr;
						} else {
							echo '<tr><td align="center">'.$dropDate.'</td><td align="center">'.$info['invoice'].'</td><td>'.$info['invoiceNotes'].'</td><td align="right">$ '.number_format($info['parate'],2).'</td></tr>'.$emptyTr.'<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">$ '.number_format($info['parate'],2).'</td></tr>';
						    echo '<tr><td colspan="3" align="right"><strong>Accessorial</strong></td><td  align="right">$ 0.00</td></tr>';
						    //echo '<tr><td colspan="3" align="right"><strong>Discount</strong></td><td  align="right">$ 0.00</td></tr>';
						}
						?> 
						<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
						$grandTotal = $info['parate'] + $discountAmt;
						echo number_format($grandTotal,2)?></strong></td></tr>
                    </table>
					<?php }  ?>
                    
                </td>
            </tr>
            
            <tr class="notes">
                <td colspan="2">
					<table border="1">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 12px;display: block;padding:0px;margin:0;">Notes:</p></td>
						</tr>
					</table>
                    1. Include the invoice number in your payment method to ensure accurate processing.<br>
                    2. Earn a 1.5% discount for payments within 7 days, and a 1% discount for payments within 7–15 days.<br>
                    3. Late payments will incur a 2% monthly fee on invoices aged beyond 30 days.<br>
                    4. Report service discrepancies within 10 days of receipt for resolution.<br>
                    5. Payment defaults may result in recovery actions; responsible parties will bear all associated legal costs.<br>
                    6. This invoice is legally binding. Retain a copy for your records.<br>
                    7. Make checks payable to <strong>PA Transport LLC.</strong>
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