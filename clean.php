<?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule('perfmon');
CModule::IncludeModule('iblock');

$tables = CPerfomanceTableList::GetList(false);

$iblocksWithIndex = [];

$re = '/(b_iblock_(\d{3,4})_index$)/';

while($table = $tables->Fetch()) {

    preg_match($re, $table['TABLE_NAME'], $matches);

    if($matches[2] > 0) {

        $iblockId = $matches[2];

        $iblocksWithIndex[] = $iblockId;
    }
}

sort($iblocksWithIndex);

$iblocks = CIBlock::GetList([], ['ID' => $iblocksWithIndex]);

$iblocksExists = [];

while($iblock = $iblocks->Fetch()) {

    $iblocksExists[] = $iblock['ID'];
}

$clearIblocksIndex = array_diff($iblocksWithIndex, $iblocksExists);

$iblocks = $clearIblocksIndex;

foreach($iblocks as $iblockId) {

    Bitrix\Iblock\PropertyIndex\Manager::DeleteIndex($iblockId);
    Bitrix\Iblock\PropertyIndex\Manager::markAsInvalid($iblockId);
}
?>
