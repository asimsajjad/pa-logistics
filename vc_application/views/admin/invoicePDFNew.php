<?php

if($invoice) { 
$info = $invoice[0];
// $rateArr = array($info['rate']);
$rateArr = array();
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
    				// foreach($childDispMetaArr['expense'] as $cVal) {
    				// 	if(array_key_exists($cVal[0],$expense)){
    				// 		$expense[$cVal[0]]['price'] = $expense[$cVal[0]]['price'] + $cVal[1];
    				// 		$expense[$cVal[0]]['unit'][] = $cVal[1];
    				// 	} else {
    				// 		$expense[$cVal[0]] = array('price'=>$cVal[1],'unit'=>array($cVal[1]));
    				// 	}
    				// }
    			}
    		}
    	}
    }
    if($dispatchMeta['expense']) { 
    	foreach($dispatchMeta['expense'] as $expVal) {
    		if(array_key_exists($expVal[0],$expense)){
    			$expense[$expVal[0]]['price'] = $expense[$expVal[0]]['price'] + $expVal[1];
    			$expense[$expVal[0]]['unit'][] = $expVal[1];
    		} else {
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
body {font-family: 'calibri';color: #000;font-size: 17px;line-height: 24px;padding:0px;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
.invoice-box {position:relative; width: 1000px;padding:15px; margin: auto;font-size: 17px;line-height: 24px; font-family: 'calibri';color: #000;}

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
.invoice-box table tr.notes  td{padding:0 17px 17px 10px;border:0px solid;}
.invoice-box table tr.notes td td {padding: 3px;}
.invoice-box table tr.notes td {font-size: 17px;color: #666;}
table tr.notes table{margin-bottom:5px;width:100%;max-width:1000px;}
.invoicetb{padding:17px 17px 0 10px;}
.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 17px;}
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
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:480px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                Accounts: Accounts@patransportca.com<br>
                                Fleet Manager: Akash@patransportca.com
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
                                	if ($contactPerson != '') { 
										echo $contactPerson . '<br>'; 
									} elseif ($info['contactPerson'] != '') { 
										echo $info['contactPerson'] . '<br>'; 
									} 
									$designation = trim(($cdesignation != '') ? $cdesignation : $info['cdesignation']);
									$department  = trim(($cdepartment != '') ? $cdepartment : $info['cdepartment']);
									if ($designation !== '' || $department !== '') {
										echo $designation . (($designation && $department) ? ', ' : '') . $department . '<br>';
									}
									if ($cemail != '') { 
										echo '(E): ' . $cemail . '<br>'; 
									} elseif ($info['cemail'] != '') { 
										echo '(E): ' . $info['cemail'] . '<br>'; 
									} 
									if ($cphone != '') { 
										echo '(O): ' . $cphone . '<br>'; 
									} elseif ($info['cphone'] != '') { 
										echo '(O): ' . $info['cphone'] . '<br>'; 
									} 
                                ?>
                            </td>
                        </tr>
						<tr>
                            <td>
                                <strong>Dispatch Information</strong><br>
                                <?php 
								if($pickup == '') { $pickup = $info['pplocation'].' ['.$info['ppcity'].']'; }
								$extraPickup = false;
								if($extraDispatch) {
									foreach($extraDispatch as $Pick){ 
										if($Pick['pd_type']=='pickup') {
											$extraPickup = true;
										}
									}
								}

								if($extraPickup) { 
                                    $p = 1;
                                    echo '<strong>Pick # '.$p.': </strong>'.$pickup.'';
                                    if(is_array($pickupExtra)){
                                        foreach($pickupExtra as $ex){
                                            $p++;
                                            echo '<br><strong>Pick # '.$p.': </strong>'.$ex; 
                                        }
                                    } else {
                                        foreach($extraDispatch as $ex){
                                            if($ex['pd_type']=='pickup'){
                                                $p++;
                                                echo '<br><strong>Pick # '.$p.': </strong>'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'; 
                                            }
                                        }
                                    }
                                } else {
                                    echo  'From: ' .$pickup.'';
                                }
								echo '<br>'; 
								?>

                                <?php 
                                if($dropoff == '') { $dropoff = $info['ddlocation'].' ['.$info['ddcity'].']'; }
								$extraDropOff = false;
								if($extraDispatch) {
									foreach($extraDispatch as $drop){ 
										if($drop['pd_type']=='dropoff') {
											$extraDropOff = true;
										}
									}
								}

                                if($extraDropOff) { 
                                    $d = 1;
                                    echo '<strong>DO # '.$d.': </strong>'.$dropoff;
                                    if(is_array($dropoffExtra)){
                                        foreach($dropoffExtra as $ex){
                                            $d++;
                                            echo '<br><strong>DO # '.$d.': </strong>'.$ex; 
                                        }
                                    } else {
                                        foreach($extraDispatch as $ex){
											if($ex['pd_type']=='dropoff'){
                                            	$d++;
                                            	echo '<br><strong>DO # '.$d.': </strong>'.$ex['ppd_location'].' ['.$ex['ppd_city'].']';
											} 
                                        }
                                    }
                                } else {
                                    echo 'To: ' .$dropoff;
                                }
                                ?>
                            </td>
							<td align="left">
								<strong><?=$trackingLabel?>: <?=$info['tracking']?></strong><br>
								<?php 
									$trailer = [];
									if (!empty($info['trailer'])) {
										$trailer = array_merge($trailer, explode(',', $info['trailer']));
									}
									if (!empty($childTrailer)) {
										foreach($childTrailer as $val) {
											$trailer[] = $val['trailer'];
										}
									}
									$filteredTrailers = array_filter(array_map('trim', $trailer), function($val) {
										$val = strtoupper($val);
										return $val !== '' && $val !== 'TBA' && $val !== 'TBD' && $val !== 'N/A';
									});
									if (!empty($trailerVal) || count($filteredTrailers) > 0) {
										echo '<strong>';
										if ($trailerValLabel != '') {
											echo $trailerValLabel . ': ';
										} else {
											echo 'Trailer No.: ';
										}
										if (!empty($trailerVal)) {
											echo $trailerVal;
										} else {
											echo implode(', ', $filteredTrailers);
										}
										echo '</strong><br>';
									}
								?>
							</td>
                        </tr>
                    </table>
                </td>
            </tr>
			
			<tr class="top">
                <td colspan="2" class="invoicetb">
					<?php 
					// echo '<pre><br>';
					// print_r($dynamicUnitPrice);exit;
					$isValid = false;
					foreach ($dynamicUnitPrice as $item) {
						if (is_array($item) && isset($item['unit']) && $item['unit'] > 0) {
							$isValid = true;
							break; 
						}
					}
					$showUnitPrice = false;
					if (!empty($dynamicUnitPrice) && $isValid == true) {
						$showUnitPrice = true;
						$dynamicUnitCount = count($dynamicUnitPrice); 
						$dynamicUnitsSpan = $dynamicUnitCount; 
						$totalDynamicAmount = 0; 
						$totalUnitPriceAmount = 0;
						foreach ($dynamicUnitPrice as $unitData) {
							$rowTotal = $unitData['unit'] * $unitData['unitPrice']; 
							$totalDynamicAmount += $rowTotal;
						}
						$totalUnitPriceAmount = $totalDynamicAmount;
					}
					// echo $showUnitPrice ? 'true' : 'false'; exit;
					if($childTrailer) { 
						// $parate = $info['rate'];
						$parate = $info['parate'];
						$childRateSum=array_sum($rateArr);
						$parentPlusChild = $parate;
						$showChild = 'false';	
					?>
					<table border="1">
                        <tr class="heading">
							<?php if($showUnitPrice){ ?>
							<td align="center" width="13%">Shipment&nbsp;Date</td>
							<td align="center" width="17%">PA Reference #</td>
							<td width="31%">Dispatch Information</td>
							<td align="center" width="10%">Unit Rate</td>
							<td align="center" width="12%">No. of Units</td>
							<td align="right" width="17%">Total Amount</td>
							<?php }else{?>
								<td align="center" width="18%">Shipment&nbsp;Date</td>
								<td align="center" width="20%">PA Reference #</td>
								<td width="48%">Description</td>
								<td align="right" width="14%">Amount</td>
						<?php	} ?>
						</tr>
						<tr>
						<?php if($showUnitPrice){ 
								echo '<td align="center"  rowspan="' . $dynamicUnitsSpan . '" >';
							    // $dropDate = date('m-d-Y',strtotime($info['dodate']));
								$puDate = date('m-d-Y',strtotime($info['pudate']));

							    // if($extraDispatch){
							    //     foreach($extraDispatch as $exDispatch){
							    //         if($exDispatch['pd_type']=='dropoff'){
							    //             $dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							    //         }
							    //     }
							    // }
								if($extraDispatch){
							        foreach($extraDispatch as $exDispatch){
							            if($exDispatch['pd_type']=='pickup'){
							                $puDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							            }
							        }
							    }
							    echo $puDate;
							    
							} else { ?>
								<td align="center" <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> >
							    <?php 
							    // $dropDate = date('m-d-Y',strtotime($info['dodate']));
								$puDate = date('m-d-Y',strtotime($info['pudate']));

							    // if($extraDispatch){
							    //     foreach($extraDispatch as $exDispatch){
							    //         if($exDispatch['pd_type']=='dropoff'){
							    //             $dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							    //         }
							    //     }
							    // }
								if($extraDispatch){
							        foreach($extraDispatch as $exDispatch){
							            if($exDispatch['pd_type']=='pickup'){
							                $puDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							            }
							        }
							    }
							    echo $puDate;
							   
							}
							if($showUnitPrice){ 
								echo '<td align="center" rowspan="' . $dynamicUnitsSpan . '" >';
								echo $info['invoice']?></td>
							    <?php
							} else { ?>
								<td align="center" <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> >
									<?=$info['invoice']?>
									</td>
								<td <?php if($showChild == 'true') { echo 'rowspan="'.(1 + count($childTrailer)).'"'; } ?> >
									<?=$info['invoiceNotes']?> </td>
							   
							<?php } ?>
							
							
							<?php if($showUnitPrice){ 
								if ($dynamicUnitCount > 0) {
									 $first = true;
									foreach ($dynamicUnitPrice as $unitData) {
										if (!$first) echo '<tr>';
										echo '<td align="center">' . $unitData['unitDescription'] . '</td>';
										echo '<td align="center">' . $unitData['unitPrice'] . '</td>';
										echo '<td align="center">' . $unitData['unit'] . '</td>';
										echo '<td align="right">$ ' . number_format(( $unitData['unit']* $unitData['unitPrice']), 2) . '</td>';
										echo '</tr>';
        								$first = false;
									}
								}
							}else{
								$totalAmt = $info['parate'] + 0;
								$discountAmt = 0;
								$expenseAmt=0;
								if($expense) { 
									foreach($expense as $key=>$expVal) {
										if($key=='Discount'){
											$discountAmt = $discountAmt + $expVal['price'];
											$totalAmt = $totalAmt - $expVal['price'];
										} else {
											$totalAmt = $totalAmt + $expVal['price'];
											$expenseAmt= $expenseAmt +  $expVal['price'];
										}
									}
								}
								$amount_calculated=$parentPlusChild - $expenseAmt + $discountAmt;
								?>
								<td align="right">$ <?= number_format($amount_calculated,2)?></td>
							<?php } ?>
						</tr>
						
						<?php 
						// echo '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
						for($c=count($expense);$c<5;$c++) {
							// echo '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
							if ($showUnitPrice) {
								echo '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
							}else{
								echo '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
							}
						}
						
						if($showUnitPrice){ 
							?>
							<tr><td colspan="5" align="right"><strong>Subtotal</strong></td><td align="right">$ <?=number_format($totalUnitPriceAmount,2)?></td></tr>
						<?php }else{ ?>
							<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">$ <?=number_format($parentPlusChild - $expenseAmt + $discountAmt,2)?></td></tr>
						<?php }
						?>
					
						<?php
						$totalAmt = $info['parate'] + 0;
						$discountAmt = 0;
						$expenseAmt=0;
						if($expense) { 
            				foreach($expense as $key=>$expVal) {
            				     if($key=='Discount'){
            				        $discountAmt = $discountAmt + $expVal['price'];
            				        $totalAmt = $totalAmt - $expVal['price'];
            				     } else {
            				         $totalAmt = $totalAmt + $expVal['price'];
									 $expenseAmt= $expenseAmt +  $expVal['price'];
            				     }
								$price = min($expVal['unit']);
								$sum = array_sum($expVal['unit']);
								$unit = ($sum / $price);
								$unit = round($unit);
								$unit = number_format($unit);

								if($showUnitPrice){
									echo '<tr>
									<td align="right"  colspan="5"><strong>'.$key.'</strong></td>
									<td  align="right">$ '.number_format($expVal['price'],2).'</td>
									</tr>';
								}else{
									echo '<tr><td></td><td></td>
									<td align="right"><strong>'.$key.'</strong></td>
									<td  align="right">$ '.number_format($expVal['price'],2).'</td>
									</tr>';
								}
            				     
            				}
						} else {
							if($showUnitPrice){
						    	echo '<tr><td colspan="5" align="right"><strong>Accessorial</strong></td><td  align="right">$ 0.00</td></tr>';
							}else{
								echo '<tr><td colspan="3" align="right"><strong>Accessorial</strong></td><td  align="right">$ 0.00</td></tr>';
							}
						}
						
						if($showUnitPrice){
							$grandtotalUnitPriceAmount=0;
							if ($showUnitPrice) {
								$grandtotalUnitPriceAmount = ($totalUnitPriceAmount - $discountAmt) + $expenseAmt;	
							} 
							?>
							<tr><td colspan="5" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
							$grandTotal = $grandtotalUnitPriceAmount;
							echo number_format($grandTotal,2)?></strong></td></tr>
						<?php }else{ 
								$grandTotal=0;
								$grandTotal = $parentPlusChild;	 
							?>
							<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
							echo number_format($grandTotal,2)?></strong></td></tr>
						 <?php }?> 
						
                    </table>
				<?php } else { ?>
					<table border="1">
                        <tr class="heading">
							<?php if($showUnitPrice){ ?>
							<td align="center" width="13%">Shipment&nbsp;Date</td>
							<td align="center" width="17%">PA Reference #</td>
							<td width="31%">Dispatch Information</td>
							<td align="center" width="10%">Unit Rate</td>
							<td align="center" width="12%">No. of Units</td>
							<td align="right" width="17%">Total Amount</td>
							<?php }else{?>
								<td align="center" width="18%">Shipment&nbsp;Date</td>
								<td align="center" width="20%">PA Reference #</td>
								<td width="48%">Description</td>
								<td align="right" width="14%">Amount</td>
						<?php	} ?>
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
						
						// $dropDate = date('m-d-Y',strtotime($info['dodate']));
						$puDate = date('m-d-Y',strtotime($info['pudate']));

						// if($extraDispatch){
						// 	foreach($extraDispatch as $exDispatch){
						// 		if($exDispatch['pd_type']=='dropoff'){
						// 			$dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
						// 		}
						// 	}
						// }
						if($extraDispatch){
							foreach($extraDispatch as $exDispatch){
								if($exDispatch['pd_type']=='pickup'){
									$puDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
								}
							}
						}
						$discountAmt = $subtotal = $totalAmt = 0;
						$tr = $emptyTr = '';
						if ($showUnitPrice) {
							$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
						}else{
							$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
						}
						for($c=count($expense);$c<5;$c++) {
							if ($showUnitPrice) {
								$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
							}else{
								$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
							}
						}
						if($expense) { 
            				foreach($expense as $key=>$expVal) {
								if ($showUnitPrice) {
									$tr .= '<tr><td colspan="5" align="right"><strong>'.$key.'</strong></td><td  align="right">';
								}else{
									$tr .= '<tr><td colspan="3" align="right"><strong>'.$key.'</strong></td><td  align="right">';
								}
            				     if($key=='Discount'){
            				        $discountAmt = $discountAmt + $expVal['price']; 
            				     } else {
            				         $totalAmt = $totalAmt + $expVal['price'];
            				     }
            				     $tr .= '$ '.number_format($expVal['price'],2).'</td></tr>';
            				}
							$grandtotalUnitPriceAmount=0;
							if ($showUnitPrice) {
								$grandtotalUnitPriceAmount = ($totalUnitPriceAmount - $discountAmt) + $totalAmt;	
							}						
							$subtotal = ($info['parate'] + $discountAmt) - $totalAmt;
							echo '<tr>';
							if ($showUnitPrice) {
								echo '<td align="center" rowspan="' . $dynamicUnitsSpan . '">' . $puDate . '</td>';
								echo '<td align="center" rowspan="' . $dynamicUnitsSpan . '">' . $info['invoice'] . '</td>';
							} else {
								echo '<td align="center">' . $puDate . '</td>';
								echo '<td align="center">' . $info['invoice'] . '</td>';
								echo '<td>' . $info['invoiceNotes'] . '</td>';
							}
							if($showUnitPrice){
								if ($dynamicUnitCount > 0) {
									 $first = true;
									foreach ($dynamicUnitPrice as $unitData) {
										if (!$first) echo '<tr>';
										echo '<td align="center">' . $unitData['unitDescription'] . '</td>';
										echo '<td align="center">' . $unitData['unitPrice'] . '</td>';
										echo '<td align="center">' . $unitData['unit'] . '</td>';
										echo '<td align="right">$ ' . number_format($unitData['unit']* $unitData['unitPrice'], 2) . '</td>';
										echo '</tr>';
        								$first = false;
									}
								}
							}else{
							echo '<td align="right">$ '.number_format($subtotal,2).'</td>';
							}
							echo '</tr>
							' . $emptyTr;
						
							if ($showUnitPrice) {
								echo '
								<tr>
									<td colspan="5" align="right"><strong>Subtotal</strong></td>
									<td align="right">$ ' . number_format($totalUnitPriceAmount, 2) . '</td>
								</tr>' . $tr;
							}else{
								echo '
								<tr>
									<td colspan="3" align="right"><strong>Subtotal</strong></td>
									<td align="right">$ ' . number_format($subtotal, 2) . '</td>
								</tr>' . $tr;
							}
						} else {
							$grandtotalUnitPriceAmount = $totalUnitPriceAmount;
							echo '<tr>';
								if ($showUnitPrice) {
									echo '<td align="center" rowspan="' . $dynamicUnitsSpan . '">' . $puDate . '</td>';
									echo '<td align="center" rowspan="' . $dynamicUnitsSpan . '">' . $info['invoice'] . '</td>';
								} else {
									echo '<td align="center">' . $puDate . '</td>';
									echo '<td align="center">' . $info['invoice'] . '</td>';
									echo '<td>' . $info['invoiceNotes'] . '</td>';
								}
								if($showUnitPrice){
									if ($dynamicUnitCount > 0) {
										 $first = true;
										foreach ($dynamicUnitPrice as $unitData) {
											if (!$first) echo '<tr>';
											echo '<td align="center">' . $unitData['unitDescription'] . '</td>';
											echo '<td align="center">' . $unitData['unitPrice'] . '</td>';
											echo '<td align="center">' . $unitData['unit'] . '</td>';
											echo '<td align="right">$ ' . number_format($unitData['unit']* $unitData['unitPrice'], 2) . '</td>';
											echo '</tr>';
        									$first = false;
										}
									}
								}else{
								echo '<td align="right">$ '.number_format($info['parate'],2).'</td>
								';
								}
							
							echo '</tr>
							' . $emptyTr;						
							if ($showUnitPrice) {
								echo 
								'<tr><td colspan="5" align="right">
									<strong>Subtotal</strong></td>
									<td align="right">$ '.number_format($totalUnitPriceAmount,2).'</td>
									</tr>';
							}else{
								'<tr><td colspan="3" align="right">
							<strong>Subtotal</strong></td>
							<td align="right">$ '.number_format($info['parate'],2).'</td>
							</tr>';
							}
							if ($showUnitPrice) {
								echo '<tr>
								<td colspan="5" align="right"><strong>Accessorial</strong></td>
								<td  align="right">$ 0.00</td></tr>';
							}else{
								echo '<tr>
								<td colspan="3" align="right"><strong>Accessorial</strong></td>
								<td  align="right">$ 0.00</td></tr>';
							}
						 
						    //echo '<tr><td colspan="3" align="right"><strong>Discount</strong></td><td  align="right">$ 0.00</td></tr>';
						}
						 
						if ($showUnitPrice) { ?>						
							<tr><td colspan="5" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
							$grandTotal = $grandtotalUnitPriceAmount;
							echo number_format($grandTotal,2)?></strong></td></tr>
						<?php }else{?>
							<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
								$grandTotal = $info['parate'];
								echo number_format($grandTotal,2)?></strong></td></tr>
							<?php }
							?>
                    </table>
					<?php }  ?>
                    
                </td>
            </tr>
            <br>
            <tr class="notes">
                <td colspan="2">
					<table border="1" width="100%">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 17px;display: block;padding:0px;margin:0;">Notes:</p></td>
						</tr>
					</table>
                    &nbsp;&nbsp;&nbsp;&nbsp;1. Include the invoice number in your payment method to ensure accurate processing.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;2. Earn a 1.5% discount for payments within 7 days, and a 1% discount for payments within 7-15 days.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;3. Late payments will incur a 2% monthly fee on invoices aged beyond 30 days.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;4. Report service discrepancies within 10 days of receipt for resolution.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;5. Payment defaults may result in recovery actions; responsible parties will bear all associated legal costs.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;6. This invoice is legally binding. Retain a copy for your records.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;7. Make checks payable to <strong>PA Transport LLC.</strong>
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
// echo 'text';exit;