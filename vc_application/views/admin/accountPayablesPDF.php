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
tr.top {
    margin-top: 0;
    padding-top: 0;
}

.invoicetb {
    margin-top: 0;
    padding-top: 0;
}


    table {
        width: 120%; /* Increase table width */
        table-layout: fixed; /* Fixed layout for columns */
        border-collapse: collapse;
    }
    th, td {
        white-space: nowrap; /* Prevent text wrapping */
        /* text-align: center; Align text to the center */
        padding: 8px;
    }
</style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-box-bg"></div>
        <table cellpadding="0" cellspacing="0" style="  height: auto;">
			
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="64%" style="text-align: left;">
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:250px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                <strong>Accounts:</strong> Accounts@palogisticsgroup.com<br>
                                <strong>Account Manager:</strong> Akash@palogisticsgroup.com
                            </td>
                            <td width="36%" align="left">
                                <h3 style="display:inline;font-size:15px"><br>PAYABLE STATEMENT</h3><br><br>
								<strong>Statement Date:</strong> <?=date('m-d-Y')?><br><br>
								<img src="<?php echo base_url()?>assets/images/barcode.jpg" style="width:100%; max-width:150px;">
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
							  $colspan = 6; 
    							$thead .= '<td class="no-wrap" align="center">Sr. # </td>
    							<td>Trucking&nbsp;Company</td>
								<td>Factoring&nbsp;Company</td>
    							<td>Invoice&nbsp;Numbers</td>
    							<td align="center">Invoice&nbsp;Count</td>
    							<td align="center">Aging&nbsp;Days&nbsp;Range</td>
    							<td align="right">Total&nbsp;Amount</td>';
    						$thead .= '</tr>
                        </thead>';
						echo $thead.'<tbody>';
						$amount = 0;
						if ($invoice) {
							$i = 1;
							// print_r($invoice);exit;
							foreach ($invoice as $dis) {
								// $invoiceList = implode(', ', $dis['invoices']);
								$invoiceChunks = array_chunk($dis['invoices'], 2);
								$invoiceList = '';
								foreach ($invoiceChunks as $chunk) {
									$invoiceList .= implode(', ', $chunk) . '<br>';
								}
								$invoiceList = rtrim($invoiceList, '<br>');

								$invoiceCount = count($dis['invoices']);

								// $minAging = min($dis['aging_values']);
								// $maxAging = max($dis['aging_values']);
								$agingList = implode(',', $dis['aging_values']);

								$rateValue = floatval($dis['rate_total']); // ensure it's a float
								$amount += $rateValue;

								if (in_array($i, [27, 68, 119, 170, 221, 272, 323, 374, 425])) {
									echo '</tbody>
									</table>
									</td></tr>
									<tr class="top">
									<td colspan="2" class="invoicetb">
									<table border="1" width="100%">' . $thead . '
									<tbody>';
								}

								echo '<tr>
									<td align="center">' . $i . '</td>
									<td class="no-wrap">' . $dis['company'] . '</td>
									<td class="no-wrap">' . $dis['factoringCompany'] . '</td>
									<td class="no-wrap">' . $invoiceList . '</td>
									<td align="center">' . $invoiceCount . '</td>
									<td align="center">' . $agingList . '</td>';
									
								echo '<td align="center">$ ' . number_format($rateValue, 2) . '</td>
								</tr>';

								$i++;
							}
						}
						?>
						<tr><td colspan="<?=$colspan?>" align="right"><strong>Total </strong></td><td align="center"><strong>$ <?=number_format($amount,2)?></strong></td></tr>
                        </tbody>
                    </table>
                </td>
            </tr>
           
        </table>
      
    </div>
	
</body>
</html>
<?php }
else {
    echo '<h2>This Invoice is not exist please check URL.</h2>';
}