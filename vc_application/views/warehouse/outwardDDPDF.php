<?php
if($invoice) { 
$info = $invoice[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Outward Dispatch Document</title>
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
tr.top {
    margin-top: 0;
    padding-top: 0;
}

.invoicetb {
    margin-top: 0;
    padding-top: 0;
}

    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    td {
        white-space: nowrap; 
        padding: 8px;
    }
</style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-box-bg"></div>
        <table cellpadding="0" cellspacing="0" style="height: auto;">
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="64%" style="text-align: left;">
                                <img src="<?php echo base_url()?>assets/images/logo-resized.jpg" style="width: 130px; height: auto;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                <strong>Accounts:</strong> Accounts@palogisticsgroup.com<br>
                                <strong>Account Manager:</strong> Akash@palogisticsgroup.com
                            </td>
                            <td width="23%" valign="top">
                                <table width="100%" style="float: right;">
                                    <tr>
                                        <td style="text-align: left;">
                                            <h3 style="display:inline;font-size:15px"><br>Outward DD</h3><br><br>
                                            <strong>Date Out:</strong> <?=date($date)?><br><br>
                                            <img src="<?php echo base_url()?>assets/images/barcode.jpg" style="width:100%; max-width:150px;"><br><br>
                                            <p><strong>Receipt No:</strong> <?php echo $receipt_no; ?></p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0px;">
                                <table class="address-tb" border="0" width="100%">
                                    <tr>
                                        <!-- <td class="cls1" width="55%">
                                            <strong>Mailing Address<br>
                                            PA Transport LLC</strong><br>
                                            438 N Orinda CT<br>
                                            Tracy, CA 95391
                                        </td> -->
                                        <td class="cls2" width="45%">
                                            <strong>Warehouse<br>
                                            <?=$warehouse[0]['warehouse']?></strong><br>
                                            <?php 
                                            echo $formattedAddress = preg_replace('/, ([^,]+, [A-Z]{2} \d{5})$/', '<br>$1', str_replace('--','',$warehouse[0]['address']));

											?>
                                            <br>
                                            <?=$sublocation[0]['name']?></strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
							<td style="text-align: left; vertical-align: top;">
							<strong>Customer Contact:</strong><br>
                                <?php 
                                if($company[0]['company'] != '') { echo ''.$company[0]['company'].'<br>'; }
								if($company[0]['password'] != '') { echo '(O)&nbsp;'.$company[0]['password'].'<br>'; } 
                                 if($company[0]['email'] != '') { echo '(E)&nbsp;'.$company[0]['email'].'<br>'; } 
                                if($company[0]['phone'] != '') { echo '(O)&nbsp;'.$company[0]['phone'].'<br>'; } 
                                ?>
                            </td>
                        </tr>
						
						
                    </table>
                </td>
            </tr>	
            <?php 
                $summary = [];
                foreach ($invoice as $dis) {
                    $key = $dis['materialNumber'] . '|' . $dis['batch'];
                    if (!isset($summary[$key])) {
                        $summary[$key] = [
                            'materialNumber' => $dis['materialNumber'],
                            'batch' => $dis['batch'],
                            'description' => $dis['description'],
                            'palletQuantity' => 0,
                            'piecesQuantity' => 0
                        ];
                    }
                    $summary[$key]['palletQuantity'] += (int)$dis['palletQuantity'];
                    $summary[$key]['piecesQuantity'] += (int)$dis['piecesQuantity'];
                }
                if (!empty($summary) && count($invoice) > 1) { ?>
                    <tr class="top">
                        <td colspan="2" class="invoicetb">
                            <table border="1" width="100%">
                                <thead>
                                     <tr class="heading">
                                        <td colspan="6" style=" text-align: center; font-size: 16px; font-weight: bold;"><b>Dispatch Summary</b></td>
                                    </tr>
                                    <tr class="heading">
                                        <td>Sr #.</td>
                                        <td align="center">Material Number</td>
                                        <td>Description</td>
                                        <td>Batch</td>
                                        <td>Total Pallet Quantity</td>
                                        <td>Total Pieces Quantity</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $n=1;
                                     foreach ($summary as $item) { ?>
                                        <tr>
                                            <td style="vertical-align: middle; text-align: center;"><?php echo $n; ?></td>
                                            <td style="vertical-align: middle; text-align: center;"><?php echo $item['materialNumber']; ?></td>
                                            <td class="wrap-text"><?php echo $item['description']; ?></td>
                                            <td style="vertical-align: middle; text-align: center;"><?php echo $item['batch']; ?></td>
                                            <td style="vertical-align: middle; text-align: center;"><?php echo $item['palletQuantity']; ?></td>
                                            <td style="vertical-align: middle; text-align: center;"><?php echo $item['piecesQuantity']; ?></td>
                                        </tr>
                                    <?php $n++; } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <br>
            <?php } ?>		
			<tr class="top">
                <td colspan="2" class="invoicetb">
                    <table border="1"  width="100%" >
						<?php 
						$thead = '';
						 $totalPallets = 0;
                        $totalPieces = 0;	
                        $thead .= '<thead>
                            <tr class="heading"><td colspan="9" style=" text-align: center; font-size: 16px; font-weight: bold;"><b>Itemized Details</b></td></tr>
                            <tr class="heading">';
							   $colspan = 7; 
    							$thead .= '<td>Sr #.</td>
                                <td align="center">Material Number</td>
    							<td>Description</td>
								<td>Batch</td>
    							<td>Lot Position</td>
    							<td>Pallet Number</td>
                                <td>Pallet Position</td>
    							<td>Pallet Quantity</td>
								<td>Pieces Quantity</td>'; 
    						$thead .= '</tr>
                        </thead>';
						echo $thead.'<tbody>';
						if($invoice){
							$i = 1;
						    foreach($invoice as $dis){
                                $totalPallets += (int)$dis['palletQuantity'];
                                $totalPieces += (int)$dis['piecesQuantity'];
						        echo '<tr>
                                    <td style="vertical-align: middle; text-align: center;">'.$i.'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['materialNumber'].'</td>
									<td class="wrap-text">'.$dis['description'].'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['batch'].'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['lotNumber'].'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['palletNumber'].'</td>
                                	<td style="vertical-align: middle; text-align: center;">'.$dis['palletPosition'].'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['palletQuantity'].'</td>
									<td style="vertical-align: middle; text-align: center;">'.$dis['piecesQuantity'].'</td>
								</tr>';
								$i++; 
						    }
                            echo '<tr style="font-weight: bold;">
                                <td colspan="7" align="right" style="font-weight:bold">Total:</td>
                                <td style="font-weight:bold; vertical-align: middle; text-align: center;">' . $totalPallets . '</td>
                                <td style="font-weight:bold; vertical-align: middle; text-align: center;">' . $totalPieces . '</td>
                            </tr>';
						} 
						?>					
                        </tbody>
                    </table>
                </td>
            </tr>
            <!-- <tr class="notes">
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
            </tr> -->
        </table>
        <!-- <div class="footer">
            Thank you for your business!<br>
        </div> -->
    </div>
	
</body>
</html>
<?php }
else {
    echo '<h2>This Invoice is not exist please check URL.</h2>';
}
// echo 'test';exit;