<?php 

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/app/get_token.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/app/get_token.php");

if (empty($unauthorized)) {
    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/app/get_data.php"))
	    require_once($_SERVER["DOCUMENT_ROOT"]."/app/get_data.php");
?>

<div>
    <a href="/?action=logout" class="className" data-client_id="" style="text-decoration:none; display: inline-flex; align-items: center; background: rgb(51, 157, 199); color: rgb(255, 255, 255); border: 1px solid rgb(0, 111, 159); cursor: pointer; font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 13px; -webkit-font-smoothing: antialiased; text-rendering: optimizelegibility; height: 45px;">
        <div style="padding-left: 2px; height: 100%; display: flex; background: #006F9F; align-items: center;">
            <svg width="43" height="20" viewBox="0 0 103 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100.978 110.821H80.1612C76.3105 110.82 72.5171 109.887 69.1058 108.1C65.6946 106.313 62.7669 103.727 60.5734 100.562L47.5568 81.7001L37.3466 66.9414C36.8699 66.2881 36.2006 65.8005 35.4326 65.5471C34.6646 65.2937 33.8365 65.2871 33.0646 65.5284C32.2927 65.7697 31.6158 66.2467 31.1288 66.8924C30.6419 67.5381 30.3694 68.3201 30.3496 69.1286V104.685C30.3496 106.312 29.7032 107.873 28.5525 109.023C27.4018 110.174 25.8412 110.821 24.2139 110.821H6.1357C4.5084 110.821 2.94778 110.174 1.7971 109.023C0.646432 107.873 0 106.312 0 104.685V78.2161C0 71.8968 2.51034 65.8364 6.97873 61.368C11.4471 56.8996 17.5075 54.3893 23.8268 54.3893H28.5689C28.8723 54.3897 29.1711 54.3158 29.4393 54.1741C29.7076 54.0324 29.937 53.8272 30.1076 53.5763L47.5277 28.4721L60.1379 10.2681C62.3319 7.10063 65.2608 4.51197 68.6738 2.72372C72.0868 0.935456 75.8823 0.000873344 79.7355 0H100.523C100.86 0.0042817 101.188 0.0987274 101.476 0.273547C101.763 0.448367 101.998 0.697159 102.156 0.99383C102.314 1.2905 102.39 1.62418 102.375 1.96008C102.36 2.29598 102.256 2.62176 102.072 2.90334L71.6158 46.889C69.9526 49.2899 69.0614 52.1411 69.0614 55.0619C69.0614 57.9826 69.9526 60.8338 71.6158 63.2348L102.527 107.859C102.726 108.141 102.844 108.473 102.867 108.818C102.89 109.162 102.818 109.507 102.658 109.813C102.497 110.119 102.256 110.375 101.96 110.553C101.663 110.731 101.324 110.823 100.978 110.821Z" fill="white"></path>
            </svg>
        </div>
        <span style="display: inline-block;padding-left: 17px;padding-right: 18px;font-family: sans-serif;font-weight: bold;font-size: 14px;line-height: 18px;text-align: center;letter-spacing: 1px;text-transform: uppercase;color: #FFFFFF;">Log out</span>
    </a>
</div>

<?
    $leadsService = $apiClient->leads();

    $leadsCollection = $leadsService->get();

    foreach ($leadsCollection as $lead) {
        $leadId = $lead->getId();
        $links = $apiClient->leads()->getLinks($lead);
        $i=1;?>
        
        <table style="
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;">
            <caption style="font-weight: bold;padding: 8px;">
                Товары у сделки с id "<?=$leadId?>
            </caption>
            <tr style="background-color: #1b3446;color: #ffffff">
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Название</th>
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Количество</th>
            </tr>
        
            <?foreach($links->toArray() as $key=>$value){
                $i++;
                if($value['to_entity_type'] == 'catalog_elements'){
                $catalogElement = $apiClient->catalogElements($value['metadata']['catalog_id'])->getOne($value['to_entity_id']);
                if($i % 2 == 0) echo "<tr>";
                else {?>
                    <tr style="background-color: #dddddd;">
                <?}?>
                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;\"><?=$catalogElement->toArray()['name']?>"</td>
                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;\"><?=$value['metadata']['quantity']?></td>
                </tr>
            
                <?}?> 
            <?}?>
        
        </table>
    <? } ?>

<? } ?>