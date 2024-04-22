<?php
// echo '<h4>'.$contentTitle.'</h4> ';

if ((isset($discountBirth) && $discountBirth )
    || (isset($discountForLogin) && $discountForLogin>0)) 
    printDiscountsTitle();

if (isset($discountBirth)) printDiscountBirth();
if (isset($discountForLogin)) printDiscountForLogin();

?>
    <table class="tblServices">
<?php
    $discountBirthSize = 0.5; // скидка 50% в день рождения 
    $discountForLoginSize = 0.10; // скидка 5% на первые 24 часа
    $discountTotal = 0;
    if ($userAuthorized) {
            if (isset($discountBirth) && $discountBirth) $discountTotal += $discountBirthSize;
            if (isset($discountForLoginSize)) $discountTotal += $discountForLoginSize;
        };
    foreach($services as $service) {
        
        $costDiscounted = round($service['cost'] * (1 - $discountTotal),-1);
        
        $finalCostMSG = ($costDiscounted < $service['cost']) ?
         ('<s style="color:darkred"> ' . $service['cost'] . " </s>&nbsp; " . $costDiscounted) : $service['cost'];
        echo '
        <tr>
            <td><img src="' .$service['photo'].'" width="250" height="250"></td>
            <td style="vertical-align:top; padding-left: 1.5rem;">
                <div class="srvName">' . $service['name'] . '</div>
                <div class="srvDescr">' . $service['description'] . '</div>
                <div class="srvCost">' . $finalCostMSG . ' руб. </div>                
            </td>
        </tr>
        ';
    }
?>        
    </table>
    <br>