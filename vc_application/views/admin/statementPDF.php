<?php
if($invoice) { 
$info = $invoice[0];
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
body {font-family: 'calibri';color: #000;font-size: 13px;line-height: 24px;padding: 0;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
.invoice-box {max-width:630px;margin:auto;position:relative; width: 630px;margin: auto;font-size: 13px;line-height: 24px;font-family: 'calibri';color: #000;}

.invoice-box table {width: 100%;line-height: inherit;text-align: left;border-collapse: collapse;position:relative;z-index:999;}
.invoice-box table table{width:100%;}
.invoice-box table td {vertical-align: top;}
.invoice-box .invoicetb table td {vertical-align: middle;}
.invoice-box .information table.address-tb{max-width:100%;min-width:100%;width:100%;}
.invoice-box  table.address-tb td.cls1{width:50%;}
.invoice-box  table.address-tb td.cls2{width:50%;}
.invoice-box table td td {padding: 8px;}
.invoice-box table tr td:nth-child(2) {/*text-align: right;*/}
.invoice-box table tr.top table td.title {font-size: 45px;line-height: 45px;color: #333;}
.invoice-box table tr.information table td {padding-bottom: 10px;}
.invoice-box table tr.heading td {color:#fff;background: #1f3864;border-bottom: 1px solid #ddd;font-weight: bold;}
.invoice-box table tr.details td {padding-bottom: 20px;}
.invoice-box table tr.total td:nth-child(2) {border-top: 2px solid #eee;font-weight: bold;}
.invoice-box table tr.notes  td{padding:0 12px 12px 12px;border:0px solid;}
.invoice-box table tr.notes td td {padding: 3px;}
table tr.notes table{width: 100%;}
.invoicetb{padding:12px 12px 0 8px;}
.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 13px;}
.invoicetb table{border: 0px solid #bab7b7;width:100%;}
.invoicetb tr { page-break-inside: avoid; }
.invoicetb thead {display: table-header-group;}
.invoicetb tbody {display: table-row-group; }
.footer {color:red;margin-top: 20px;text-align: center;font-weight: bold;font-size: 19px;letter-spacing: 1px;}
	</style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-box-bg"></div>
        <table cellpadding="0" cellspacing="0">
           
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="64%">
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:250px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                <strong>Accounts:</strong> Accounts@patransportca.com<br>
                                <strong>Fleet Manager:</strong> Akash@patransportca.com
                            </td>
                            <td width="36%" align="left">
                                <h3 style="display:inline;font-size:15px"><br>STATEMENT OF ACCOUNT</h3><br><br>
								<strong>Statement Date:</strong> <?=date('m-d-Y')?><br><br>
								<img src="<?php echo base_url()?>assets/images/barcode.jpg" style="width:100%; max-width:150px;">
							</td>
                        </tr>
                        <tr>
                            <td style="padding:0px;">
                                <table class="address-tb" border="0" width="100%">
                                    <tr>
                                        <td class="cls1" width="55%">
                                            <strong>Mailing Address<br>
                                            PA Transport LLC</strong><br>
                                            438 N Orinda CT<br>
                                            Tracy, CA 95391
                                        </td>
                                        <td class="cls2" width="45%">
                                            <strong>Bill To<br>
                                            <?=$company[0]['company']?></strong><br>
                                            <?php 
                                            echo $formattedAddress = preg_replace('/, ([^,]+, [A-Z]{2} \d{5})$/', '<br>$1', str_replace('--','',$company[0]['address']));
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left">
                                <strong>Shipping Contact:</strong><br>
                                <?php 
                                if($company[0]['contactPerson'] != '') { echo ''.$company[0]['contactPerson'].'<br>'; } 
								$designation = trim($company[0]['designation']);
								$department  = trim($company[0]['department']);
								if ($designation !== '' || $department !== '') {
									echo $designation . (($designation && $department) ? ', ' : '') . $department . '<br>';
								}
                                // if($company[0]['department'] != '') { echo ''.$company[0]['department'].'<br>'; } 
                                if($company[0]['email'] != '') { echo '(E)&nbsp;'.$company[0]['email'].'<br>'; } 
                                if($company[0]['phone'] != '') { echo '(O)&nbsp;'.$company[0]['phone'].'<br>'; } 
                                ?>
                            </td>
                        </tr>
						
						
                    </table>
                </td>
            </tr>
			
			
			<tr class="top">
                <td colspan="2" class="invoicetb">
					
                    <table border="1"  width="100%">
						<?php 
						$thead = '';
						$partialAmt = 0;	
                        $thead .= '<thead>
                            <tr class="heading">';
							  /*if($type == 'Drayage') { $colspan = 6; 
								$thead .= '<td width="13%">Shipment&nbsp;Date</td>
    							<td width="16%">PA&nbsp;Invoice&nbsp;No.</td>
    							<td width="16%">Cust&nbsp;Ref.&nbsp;No.</td>
    							<td width="16%">Container&nbsp;No. / Trailer</td>
    							<td width="13%">Invoice&nbsp;Date</td>
    							<td width="13%" align="center">Inv.&nbsp;Aging<br>(Days)</td>
    							<td width="13%" align="right">Amount</td>';
							   } else { $colspan = 5; 
    							$thead .= '<td width="15%">Shipment&nbsp;Date</td>
    							<td width="17%">PA&nbsp;Invoice&nbsp;No.</td>
    							<td width="19%">Cust&nbsp;Ref.&nbsp;No.</td>
    							<td width="15%">Invoice&nbsp;Date</td>
    							<td width="15%" align="center">Inv.&nbsp;Aging<br>(Days)</td>
    							<td width="15%" align="right">Amount</td>';
							   } */
							   if($type == 'Drayage') { $colspan = 6; 
								$thead .= '<td align="center">Delivery&nbsp;Date</td>
    							<td>PA&nbsp;Invoice&nbsp;No.</td>
    							<td>Cust&nbsp;Ref.&nbsp;No.</td>
    							<td>Container&nbsp;No. / Trailer</td>
    							<td align="center">Invoice&nbsp;Date</td>
    							<td align="center">Inv.&nbsp;Aging<br>(Days)</td>
    							<td align="right">Amount</td>';
							   } else { $colspan = 5; 
    							$thead .= '<td align="center">Delivery&nbsp;Date</td>
    							<td>PA&nbsp;Invoice&nbsp;No.</td>
    							<td>Cust&nbsp;Ref.&nbsp;No.</td>
    							<td align="center">Invoice&nbsp;Date</td>
    							<td align="center">Inv.&nbsp;Aging<br>(Days)</td>
    							<td align="right">Amount</td>';
							   } 
    						$thead .= '</tr>
                        </thead>';
                        
						echo $thead.'<tbody>';
						
						
						$amount = 0;
						if($invoice){
							$i = 1;
						    foreach($invoice as $dis){
						        $dispatchMeta = json_decode($dis['dispatchMeta'],true);
								if($table == 'wrehouse_dispatch'){
									$partialAmt = $partialAmt + $dis['partialAmount'];
								}
								if(is_numeric($dispatchMeta['partialAmount'])) {
									$partialAmt = $partialAmt + $dispatchMeta['partialAmount'];
								}
								if($table == 'wrehouse_dispatch'){
									if($type != '' && $dis['invoicePDF'] != $type){
										continue;
									}
								}else{
									if($type != '' && $dispatchMeta['invoicePDF'] != $type){
										continue;
									}
								}
								//if($i == 32 || $i == 83 || $i == 134 || $i == 185 || $i == 236 || $i == 287 || $i == 338 || $i == 389 || $i == 440) { // + 51
								if($i == 27 || $i == 68 || $i == 119 || $i == 170 || $i == 221 || $i == 272 || $i == 323 || $i == 374 || $i == 425) { // - 15
									echo '</tbody>
									</table>
									</td></tr>
									<tr class="top">
									<td colspan="2" class="invoicetb">
									<table border="1"  width="100%">'.$thead.'
									<tbody>';
								}
						        if($dis['pd_date'] != '' && (!strstr($dis['pd_date'],'0000'))) {
						            $dis['pudate'] = $dis['pd_date'];
						        }
						        $amount = $amount + $dis['parate'];
						        echo '<tr>
						        <td align="center">'.date('m-d-Y',strtotime($dis['pudate'])).'</td>
						        <td>'.$dis['invoice'].'</td>
								<td class="wrap-text">'.str_replace(',', ',<br>', $dis['tracking']).'</td>';

						        if($type == 'Drayage') { 
									echo '<td>'.str_replace('TBA','N/A',$dis['trailer']).'</td>';
									/*if($dispatchMeta['dispatchInfo']) { 
										foreach($dispatchMeta['dispatchInfo'] as $diVal) { 
											if($diVal[0]=='Container No' || $diVal[0]=='Container Number') { echo ', '.str_replace('TBA','N/A',$diVal[1]).''; }
										}
									}*/
								}
						        echo '<td align="center">'.date('m-d-Y',strtotime($dis['invoiceDate'])).'</td><td align="center">';
						        
									$invoiceType = '';
									$showAging = 'false';
									$aDays = 0;
									if($dis['invoiceType']=='Direct Bill'){  $aDays = 30; }
									elseif($dis['invoiceType']=='Quick Pay'){ $aDays = 7; }
									elseif($dis['invoiceType']=='RTS'){ $aDays = 3; }
									
									if($dis['invoiceDate'] != '0000-00-00'){ $showAging = 'true'; }
									if($table == 'wrehouse_dispatch'){
										if($dis['invoicePaidDate'] != '' || $dis['invoicePaidDate'] != '0000-00-00'){ $showAging = 'false';  }
										if($dis['invoiceCloseDate'] != '' || $dis['invoiceCloseDate'] != '0000-00-00'){ $showAging='false';  }
									}else{
										if($dispatchMeta['invoicePaidDate'] != ''){ $showAging = 'false';  }
										if($dispatchMeta['invoiceCloseDate'] != ''){ $showAging='false';  }
									}
									
									if($showAging == 'true'){
									    $date1 = new DateTime($dis['invoiceDate']);
										$date2 = new DateTime(date('Y-m-d'));
										$diff = $date1->diff($date2);
										$aging = $diff->days;
										
									    if($aging  > $aDays && $aDays > 0) { echo ''.$aging.' Days'; }
										else { echo ''.$aging.' Days'; }
									}
									
						        echo '</td>
						        <td align="right">$ '.number_format($dis['parate'],2).'</td>
						        </tr>';
								$i++;
						    }
						} 
						if(count($invoice) < 4){
						?>
						<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><?php if($type == 'Drayage') { echo '<td></td>'; } ?></tr>
						<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><?php if($type == 'Drayage') { echo '<td></td>'; } ?></tr>
						<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><?php if($type == 'Drayage') { echo '<td></td>'; } ?></tr>
						<?php } ?>
						<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><?php if($type == 'Drayage') { echo '<td></td>'; } ?></tr>
						<tr><td colspan="<?=$colspan?>" align="right"><strong>Subtotal</strong></td><td align="right">$ <?=number_format($amount,2)?></td></tr>
						<?php 
						$totalAmt = $amount;
						if($partialAmt > 0) {
							echo '<tr><td colspan="'.$colspan.'" align="right"><strong>Partial Amount</strong></td><td align="right"><strong>$ '.number_format($partialAmt,2).'</strong></td></tr>';
							$totalAmt = $totalAmt - $partialAmt;
						}
						?>
						<tr><td colspan="<?=$colspan?>" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?=number_format($totalAmt,2)?></strong></td></tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <tr class="notes">
                <td colspan="2">
					<table border="1">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 13px;display: block;padding:0px;margin:0;">Notes:</p></td>
						</tr>
					</table>
                    <table border="0">
                        <tr><td>Payment Terms:</td><td>Net 30 days from the date of the Invoice.</td></tr>
                        <tr><td>Remittance Advice:</td><td>When making a payment, please include the relevant PA Invoice Reference Number.</td></tr>
					</table>
					<table border="1">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 13px;display: block;padding:0px;margin:0;">Please proceed with payment using any of the following channels</p></td>
						</tr>
					</table>
                    <table border="0">
                        <tr>
                            <td><strong>Bank</strong><br>
                            <strong>Account Title:</strong> PA Transport LLC<br>
                            <strong>Account Number:</strong> 1245008154<br>
                            <strong>Routing Number:</strong> 121101037<br>
                            <strong>Bank Name:</strong> Bank of Stockton
                            </td>
                            <td><br><strong>Physical Check</strong><br>
                            (Please send it to the following address)<br>
                            <strong>PA Transport LLC</strong><br>
                            438 N Orinda CT, Tracy, CA 95391.</td>
                        </tr>
					</table>
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
    echo '<h2>This Invoice is not exist please check URL.</h2>';
}