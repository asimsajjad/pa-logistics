<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>mso33E.PDF</title>
    <meta name="author" content="NISA012" />
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;

        }

        th {
            height: 0pt;
            padding: 0;
        }

        p {
            margin: 0;
            padding: 0;
        }

        td {
            padding: 6pt;

        }
        .checkbox{
            border: 1px solid #000;
        }

        .s11 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8.5pt;
        }

        .s26 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5pt;
            text-align: justify;
            text-justify: inter-word;
        }

        .s27 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 5pt;
        }

        .s35 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7pt;
        }

        .s36 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11.5pt;
        }

        .s37 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7pt;
        }

        .panel-title {
            background: var(--black);
            color: #fff;
            text-align: center;
            font-size: 12pt;
            padding: 6pt 4pt;
            margin: -7pt -7pt 7pt -7pt;
            font-weight: 700;
        }

        .small {
            font-size: 5pt;
            line-height: 1.2;
        }

        /* Table like sections use real tables */
        table.small-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-top: 4pt;
        }

        table.small-table th,
        table.small-table td {
            border: 0.5px solid var(--black);
            padding: 5pt 7pt;
            text-align: center;
            vertical-align: top;
        }

        table.small-table thead th {
            background: #f8f8f8;
            font-weight: 400;
        }
    </style>
</head>

<body>
    <?php 
    // echo "<pre>";
    // print_r($dispatch);exit;
        $pages = [];
        $pages[] = [
            'type' => 'main',
            'dispatch' => $dispatch[0],
            'pickup' => null,
            'dropoff' => null
        ];
        $pickups = array_values(array_filter($extraDispatch, function($e) {
            return $e['pd_type'] === 'pickup';
        }));

        $dropoffs = array_values(array_filter($extraDispatch, function($e) {
            return $e['pd_type'] === 'dropoff';
        }));
        $max = max(count($pickups), count($dropoffs));
        for ($i = 0; $i < $max; $i++) {
            $pages[] = [
                'type' => 'extra',
                'pickup' => $pickups[$i] ?? null,
                'dropoff' => $dropoffs[$i] ?? null
            ];
        }
        $pageNo = 1;
        $totalPages = count($pages);

        foreach ($pages as $page) {
    ?>
    <table>
        <tr>
            <td colspan="6"></td>
            <td colspan="6"></td>
        </tr>
        <tr style="border: 1px solid #000;">
            <td colspan="6" style="text-align: left; border: 0pt solid #000000; padding-left: 5pt;">
                Date:
            </td>
            <td colspan="5"
                style="text-align: right; font-weight: bold; font-size: 15pt; padding: 0pt; border: 0pt solid #000000;">
                BILL OF LADING
            </td>
            <td colspan="1" style="text-align: right; border: 0pt solid #000000; padding-right: 5pt;">
                Page <?= $pageNo ?> of <u>__<?= $totalPages ?>_______</u>
            </td>
        </tr>
    </table>
    <?php
        $shipFromName    = $page['type']==='main' ? $page['dispatch']['pickup_company'] : ($page['pickup']['pd_location'] ?? '');
        $shipFromAddress = $page['type']==='main' ? $page['dispatch']['pickup_address'] : ($page['pickup']['pd_address'] ?? '');
        $shipFromCity    = $page['type']==='main' ? $page['dispatch']['pickup_city'] . ', ' . $page['dispatch']['pickup_state'] 
                            : ($page['pickup']['pd_city'] ?? '');
    ?>
    <table>
        <tr>
            <th colspan="9"
                style="background:#000; color:#fff; text-align:center; border: 0.5px solid #000; font-weight:bold; width: 280pt;">
                <p style="font-size: 7pt;">
                    SHIP FROM
                </p>
            </th>
            <td colspan="3"
                style=" text-align: left;  font-size: 7pt; font-weight: bold;  border-right: 0.5px solid #000;  ">
            </td>
        </tr>

        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Name: <?= htmlspecialchars($shipFromName) ?></td>
            <td colspan="3"
                style=" padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; font-weight: bold ;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Bill of Lading Number:<?= $page['dispatch']['invoice'] ?? '' ?>
            </td>
        </tr>

        <tr>
            <td colspan="9"
                style=" padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Address: <?= htmlspecialchars($shipFromAddress) ?></td>
            <td colspan="3"
                style=" padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                City/State/Zip: <?= htmlspecialchars($shipFromCity) ?></td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
        </tr>
        <tr>
            <td colspan="7" style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; ">
                SID#:
                <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

            <td colspan="2" style=" font-size: 7pt; padding: 0pt; ">
                FOB <input type="checkbox" name="fob"></td>
            </td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;  ">
            </td>
        </tr>
        <?php
            $shipToName    = $page['type']==='main' ? $page['dispatch']['drop_company'] : ($page['dropoff']['pd_location'] ?? '');
            $shipToAddress = $page['type']==='main' ? $page['dispatch']['drop_address'] : ($page['dropoff']['pd_address'] ?? '');
            $shipToCity    = $page['type']==='main' ? $page['dispatch']['drop_city'] . ', ' . $page['dispatch']['drop_state'] 
                            : ($page['dropoff']['pd_city'] ?? '');
        ?>
        <tr>
            <th colspan="9"
                style="background:#000; color:#fff; text-align:center; border: 0.5px solid #000; font-weight:bold;">
                <p style="font-size: 7pt;">
                    SHIP TO
                </p>
            </th>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; text-align: left;  font-size: 7pt; font-weight: bold;  border-right: 0.5px solid #000;  ">
                CARRIER NAME: <?= $page['dispatch']['carrier'] ?? '' ?>
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Name: <?= htmlspecialchars($shipToName) ?></td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Trailer Name: <?= $page['dispatch']['trailer'] ?? '' ?></td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Address: <?= htmlspecialchars($shipToAddress) ?></td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000 ;  ">
                Seal Number(s): </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                City/State/Zip: <?= htmlspecialchars($shipToCity) ?></td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; font-weight: bold;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                SCAC: </td>
                  
        </tr>
        <tr>
            <td colspan="7" style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; ">
                CID#:
                <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

            <td colspan="2" style="font-size: 7pt; padding: 0pt; ">
                FOB <input type="checkbox" name="fob"></td>
            </td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Pro number: <?= $page['dispatch']['tracking'] ?? '' ?>
            </td>
        </tr>
        <tr>
            <th colspan="9"
                style="background:#000; color:#fff; text-align:center; border: 0.5px solid #000; font-weight:bold;">
                <p style="font-size: 7pt;">
                    THIRD PARTY FREIGHT CHARGES BILL TO:
                </p>
            </th>
            <td colspan="3"
                style=" font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Name: </td>
            <td colspan="3"
                style=" font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                Address: </td>
            <td colspan="3"
                style=" font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                City/State/Zip: </td>
            <td colspan="3"
                style=" padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;   border-top: 0.5px solid #000; border-right: 0.5px solid #000; ">
                <span style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; font-weight: bold; ">Freight Charge Terms:</span>
                <span style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; font-weight: bold; "> <i>(freight charges are prepaid unless </i></span>

            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                <span style="font-size: 7pt; font-weight: bold; "><i>marked otherwise)</i></span>
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style=" font-size: 7pt;  border-left: 0.5px solid #000; border-top: 0.5px solid #000; border-right: 0.5px solid #000;  ">
                SPECIAL INSTRUCTIONS:
            </td>
            <td colspan="3"
                style="padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt; font-weight: bold; border-left: 0.5px solid #000; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000 ;  ">
                Prepaid ________ &nbsp;
                &nbsp;&nbsp;Collect _______ &nbsp;
                &nbsp;&nbsp;3rd
                Party ______ </td>
        </tr>
        <tr>
            <td colspan="9"
                style=" font-size: 7pt;  border-left: 0.5px solid #000; border-right: 0.5px solid #000;  ">
            </td>
            <td colspan="1" style=" font-size: 7pt;  text-align: center;  "> <input
                    type="checkbox" name="checkbox"> <br> <span style=" font-size: 7pt; ">(Checkbox)</span> </td>

            <td colspan="2" style=" font-size: 7pt;   border-right: 0.5px solid #000;  ">
                Master Bill of Lading:
                with attached underlying
                Bills of Lading </td>
        </tr>
    </table>

    <!-- new table -->
    <table>
        <!-- Heading -->
        <tr>
            <th colspan="12"
                style="background-color: #000; border: 0.5px solid #000; color: #fff; text-align:center; font-size: 7pt; font-weight:bold;">
                CUSTOMER ORDER INFORMATION
            </th>
        </tr>
        <tr>
            <th colspan="4" style="border:0.5px solid #000; font-size: 7pt;    width: 73pt; ">CUSTOMER ORDER NUMBER
            </th>
            <th style="border:0.5px solid #000; font-size: 7pt; width: 44pt;" colspan="1"># PKGS</th>
            <th style="border:0.5px solid #000; font-size: 7pt; width: 44pt; " colspan="1">WEIGHT</th>
            <th style="border:0.5px solid #000; font-size: 7pt;" colspan="2">
                PALLET/SLIP <br>
                <small style="font-size:5pt; font-weight: 100;">(CIRCLE ONE)</small>
            </th>
            <th colspan="4" style="border:0.5px solid #000; font-size:9pt ">ADDITIONAL SHIPPER INFO</th>
        </tr>
        <?php
            if ($page['type'] === 'main') {
                $pickupQty =  $page['dispatch']['quantityP'] ?? '' ;
                $pickupWeight   =  $page['dispatch']['weightP'] ?? '' ;
            } else {
                $meta = isset($page['pickup']['pd_meta']) ? json_decode($page['pickup']['pd_meta'], true) : [];
                $pickupQty = $meta['quantityP'] ?? '';
                $pickupWeight   = $meta['weightP'] ?? '';
            }
        ?>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"><?= $page['dispatch']['invoice'] ?? ''?></td>
            <td style="border:0.5px solid #000;"><?= $pickupQty ?></td>
            <td style="border:0.5px solid #000;"><?= $pickupWeight ?></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <!-- Empty Rows -->
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt;  "
                colspan="1">Y
            </td>
            <td style="border:0.5px solid #000; text-align: center; font-size: 7pt; font-weight: bold; padding: 0pt; "
                colspan="1">N</td>
            <td colspan="4" style="border:0.5px solid #000;"></td>
        </tr>
        <!-- GRAND TOTAL Row -->
        <tr>
            <td colspan="4"
                style="border:0.5px solid #000; font-size: 7pt; font-weight:bold; text-align:left;  padding: 0pt 0pt 0pt 7pt;">
                GRAND
                TOTAL</td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000;"></td>
            <td style="border:0.5px solid #000; background-color: #626060;" colspan="6"></td>
        </tr>

    </table>

    <!-- 2nd table -->

    <table>

        <tr>
            <th colspan="12"
                style="background-color: #000; border: 0.5px solid #000; color: #fff; text-align:center; font-size: 7pt; font-weight:bold;">
                CARRIER INFORMATION
            </th>
        </tr>
        <tr>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; width: 65pt;" colspan="2">HANDLING UNIT</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; width: 65pt; " colspan="2">PACKAGE</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; width: 40pt; " rowspan="2">WEIGHT</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; width: 20pt; " rowspan="2">H.M. (X)</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; " colspan="4" rowspan="2">COMMODITY
                DESCRIPTION<br>
                <div style="font-size:6pt; font-weight:100;">Commodities requiring special or additional care or attention in handling or stowing
                    must be so
                    marked and packaged as to ensure safe transportation with ordinary care. <br>
                    <span style="  "> <i> See Section 2(e) of NMFC Item 360</i></span>
                </div>
            </th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; width: 65pt;  " colspan="2">LTL ONLY</th>
        </tr>
        <tr style="border:0.5px solid #000;">
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">TYPE</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">QTY</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">QTY</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">TYPE</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">NMFC #</th>
            <th style="border:0.5px solid #000; font-size: 7pt; padding: 1pt; ">CLASS</th>
        </tr>
        </thead>
        <?php
            if ($page['type'] === 'main') {
                $dropOffQty =  $page['dispatch']['quantityD'] ?? '' ;
                $dropOffWeight   =  $page['dispatch']['weightD'] ?? '' ;
                $dropOffDescription   =  $page['dispatch']['metaDescriptionD'] ?? '' ;
            } else {
                $meta = isset($page['dropoff']['pd_meta']) ? json_decode($page['dropoff']['pd_meta'], true) : [];
                $dropOffQty = $meta['quantityD'] ?? '';
                $dropOffWeight   = $meta['weightD'] ?? '';
                $dropOffDescription   =  $page['dispatch']['metaDescriptionD'] ?? '' ;
            }
        ?>
        <tbody style="border:0.5px solid #000;">
            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"><?= $dropOffQty ?></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"><?= $dropOffWeight ?></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"><?= $dropOffDescription ?></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>

            </tr>
            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
            </tr>
            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
            </tr>
            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
            </tr>

            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td colspan="4"></td>

                <td colspan="2" class="pe-4"
                    style="text-align: left; font-size: 7pt; color:#777; padding: 0pt; font-weight:bold;">
                    RECEIVING
                </td>

            </tr>

            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td colspan="4"></td>

                <td colspan="2" class="pe-4"
                    style="text-align: center; font-size: 7pt; color:#777; padding: 0pt; font-weight:bold;">
                    STAMP SPACE
                </td>

            </tr>

            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
            </tr>
            <tr style="border:0.5px solid #000;">
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" colspan="4"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;"></td>
            </tr>




            <tr>
                <td style="border:0.5px solid #000;" colspan="1"></td>
                <td style="border:0.5px solid #000;" colspan="1" class="col-color"></td>
                <td style="border:0.5px solid #000;" colspan="1"></td>
                <td style="border:0.5px solid #000;" colspan="1" class="col-color"></td>
                <td style="border:0.5px solid #000;"></td>
                <td style="border:0.5px solid #000;" class="col-color"></td>
                <td style="border:0.5px solid #000; font-weight: bold; padding: 0pt; text-align: center; font-size: 7pt; "
                    colspan="4" class="grand">GRAND TOTAL</td>
                <td style="border:0.5px solid #000;" class="col-color"></td>
                <td style="border:0.5px solid #000;" class="col-color"></td>
            </tr>
    </table>
    <table>
        <!-- <tr style="border: 0.5px solid #000;">
            <td style="text-align: left; border: 0.5px solid #000; padding: 0pt; " colspan="6">
                <p style="font-size: 7pt; padding: 0pt;">
                    Where the rate is dependent on value, shippers are required to state specifically in writing
                    the
                    agreed or
                    declared value of the property as follows:
                    “The agreed or declared value of the property is specifically stated by the shipper to be
                    not
                    exceeding. <br> <br>
                    __________________ per ___________________.”
                </p>
            </td>
            <td style=" border: 0.5px solid #000; padding: 0pt; text-align: center; " colspan="6">

                <span style=" font-size: 7pt; font-weight: bold; ">
                    COD Amount: $______________________________ </span><br>
                <span style=" font-size: 7pt; font-weight: bold;  ">Fee Terms: Collect: <input type="checkbox"
                        name="checkbox"> &nbsp;
                    &nbsp;&nbsp; Prepaid: <input type="checkbox" name="checkbox"> &nbsp;
                    &nbsp;&nbsp; Customer check acceptable: <input type="checkbox" name="checkbox"> &nbsp;
                    &nbsp;&nbsp;</span>

            </td>

        </tr> -->
        <tr>
            <td style=" border-left: 0.5px solid #000; border-right: 0.5px solid #000; text-align: left;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  "
                colspan="6">
                Where the rate is dependent on value, shippers are required to state specifically in writing the agreed
                or
                declared value of the property as follows:
            </td>
            <td style="font-size: 7pt; font-weight: bold; padding: 0pt 0pt 0pt 30pt ; border-left: 0.5px solid #000; border-right: 0.5px solid #000;  "
                colspan="6">
                COD Amount: $ ______________________
            </td>
        </tr>
        <tr>
            <td style=" border-left: 0.5px solid #000; border-right: 0.5px solid #000; text-align: left;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  "
                colspan="6">
                “The agreed or declared value of the property is specifically stated by the shipper to be not exceeding
            </td>
            <td style="  border-left: 0.5px solid #000; border-right: 0.5px solid #000; font-weight: bold; font-size: 7pt; text-align: center;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  "
                colspan="6">
                Fee Terms: Collect: <input type="checkbox" name="checkbox"> &nbsp;
                &nbsp;&nbsp; Prepaid: <input type="checkbox" name="checkbox"> &nbsp;
                &nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td style=" border-left: 0.5px solid #000; border-right: 0.5px solid #000; text-align: left;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; font-size: 7pt;  "
                colspan="6">
                __________________ per ___________________.”
            </td>
            <td style="border-left: 0.5px solid #000; border-right: 0.5px solid #000;  font-size: 7pt; font-weight: bold; text-align: center;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  "
                colspan="6">
                Customer check acceptable: <input type="checkbox" name="checkbox">

            </td>
        </tr>



        <!-- note section -->
        <tr>
            <td colspan="12"
                style="text-align:left; font-size:9pt; font-weight: bold; border:0.5px solid #000; padding:4pt;">
                NOTE: Liability Limitation for loss or damage in this shipment may be applicable.
                See 49 U.S.C. 14706(c)(1)(A) and (B).
            </td>
        </tr>


        </tr>
        <tr style=" border: 0.5px solid #000;">
            <td style="border: 0.5px solid #000; text-align: left;padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; "
                colspan="6">
                <p style="font-size: 7pt; padding: 0pt; width: 600pt;">
                    RECEIVED, subject to individually determined rates or contracts that have been agreed upon
                    in
                    writing
                    between the carrier and shipper, if applicable, otherwise to the rates, classifications and
                    rules that have been
                    established by the carrier and are available to the shipper, on request, and to all
                    applicable
                    state and federal
                    regulations.
                </p>
            </td>
            <td style="border: 0.5px solid #000; text-align: left;  " colspan="6">
                <p style="font-size: 7pt; ">
                    The carrier shall not make delivery of this shipment without payment of freight
                    and all other lawful charges. <br><br>
                    _______________________________________Shipper Signature
                </p>
            </td>

        </tr>
        <tr>
            <td style=" padding-right: 1pt; border-left: 0.5px solid #000;width: 120pt; font-weight: bold; padding-bottom:0; border-right: 0.5px solid #000;font-size:9pt "
                colspan="4">SHIPPER SIGNATURE / DATE</td>
            <td style=" padding-right: 1pt;padding-top: 0pt; padding-bottom: 0pt; font-size: 7pt;" colspan="2">
                <u>Trailer Loaded
            </td></u>
            <td style=" padding-right: 1pt;padding-top: 0pt; padding-bottom: 0pt; border-right: 0.5px solid #000;     width: 120pt; font-size: 7pt; "
                colspan="2"><u>Freight Counted</u></td>
            <td style=" padding-right: 1pt;padding-bottom: 0pt; padding-top:0pt;border-right: 0.5px solid #000; font-size: 7pt; font-weight:bold "
                colspan="4">CARRIER SIGNATURE / PICKUP DATE</td>
        </tr>
        <tr>
            <td style=" font-size: 5pt; padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;  border-left: 0.5px solid #000 ; border-right: 0.5px solid #000 ; vertical-align: top; "
                colspan="4" rowspan="3">
                This is to certify that the above named materials are properly classified,
                packaged, marked and labeled, and are in proper condition for
                transportation according to the applicable regulations of the DOT.
            </td>
            <td style=" font-size: 7pt; adding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; " colspan="2">
                <input type="checkbox">By Shipper

            </td>
            <td style=" font-size: 7pt; adding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;" colspan="2">
                <input type="checkbox">By Shipper

            </td>
            <td style="font-size: 5pt; padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; border-left: 0.5px solid #000; border-right: 0.5px solid #000; vertical-align: top;"
                colspan="4" rowspan="3">
                Carrier acknowledges receipt of packages and required placards. Carrier certifies
                emergency response information was made available and/or carrier has the DOT
                emergency response guidebook or equivalent documentation in the vehicle.
                <i style="font-weight:bold">Property described above is received in good order, except as noted.</i>
            </td>


        </tr>
        <tr>
            <td style=" font-size: 7pt;adding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;" colspan="2">
                <input type="checkbox">By Driver
            </td>
            <td style=" font-size: 7pt; adding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt; border-right: 0.5px solid #000; "
                colspan="2">
                <input type="checkbox">By Driver/pallets said to contain
            </td>
        </tr>
        <tr>
            <td style=" font-size: 6pt; padding: 0pt; " colspan="2">
            </td>
            <td style=" font-size: 7pt;adding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;" colspan="2">
                <input type="checkbox"> By Driver/Pieces
            </td>
        </tr>
        <tr>
            <td style=" font-size: 7pt; padding: 0pt; border-bottom: 0.5px solid #000; border-right: 0.5px solid #000; border-left: 0.5px solid #000;"
                colspan="4">

            </td>
            <td style=" font-size: 6pt; padding: 0pt; border-bottom: 0.5px solid #000 ;border-bottom: 0.5px solid #000  "
                colspan="2">
            </td>
            <td style=" font-size: 7pt; padding: 0pt; border-bottom: 0.5px solid #000;" colspan="2">

            </td>
            <td style="height: 4pt; font-size: 5pt; font-weight: bold; padding-right: 1pt;padding-top: 0pt;padding-bottom: 0pt;   border-bottom: 0.5px solid #000;  border-right: 0.5px solid #000; border-left: 0.5px solid #000; "
                colspan="4">
            </td>
        </tr>

    </table>
    <?php
    $pageNo++;
    echo "<div style='page-break-after:always;'></div>"; // force new page in PDF
}
?>
</body>


</html>
<? //  echo 'test', exit; ?>