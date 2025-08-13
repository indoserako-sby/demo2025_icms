<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'group_id' => 1,
                'name' => 'FN501-A',
                'code' => 'FN501A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 1,
                'name' => 'FN502-A',
                'code' => 'FN502A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 2,
                'name' => 'FL501-A',
                'code' => 'FL501A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 2,
                'name' => 'FL502-A',
                'code' => 'FL502A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 2,
                'name' => 'FL503-A',
                'code' => 'FL503A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 3,
                'name' => 'BL504-A',
                'code' => 'BL504A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 4,
                'name' => 'RM531A-A',
                'code' => 'RM531AA',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 4,
                'name' => 'RM531B-A',
                'code' => 'RM531BA',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 32,
                'name' => 'RM532A-A',
                'code' => 'RM532AA',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 4,
                'name' => 'RM532B-A',
                'code' => 'RM532BA',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 5,
                'name' => 'RV559/LH509-A',
                'code' => 'RV559LH509A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 6,
                'name' => 'BE201-A',
                'code' => 'BE201A',
                'description' => 'BE201-A',
                'status' => 'good'
            ],
            [
                'group_id' => 6,
                'name' => 'BE202-A',
                'code' => 'BE202A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 6,
                'name' => 'BE203-A',
                'code' => 'BE203A',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 31,
                'name' => 'FN601-B',
                'code' => 'FN601B',
                'description' => 'FN601-B',
                'status' => 'good'
            ],
            [
                'group_id' => 31,
                'name' => 'FN602-B',
                'code' => 'FN602B',
                'description' => 'FN602-B',
                'status' => 'good'
            ],
            [
                'group_id' => 14,
                'name' => 'FL601-B',
                'code' => 'FL601B',
                'description' => 'FL601-B',
                'status' => 'good'
            ],
            [
                'group_id' => 14,
                'name' => 'FL602-B',
                'code' => 'FL602B',
                'description' => 'FL602-B',
                'status' => 'good'
            ],
            [
                'group_id' => 13,
                'name' => 'BL604-B',
                'code' => 'BL604B',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 32,
                'name' => 'RM631A-B',
                'code' => 'RM631AB',
                'description' => 'RM631A-B',
                'status' => 'good'
            ],
            [
                'group_id' => 32,
                'name' => 'RM631B-B',
                'code' => 'RM631BB',
                'description' => 'RM631B-B',
                'status' => 'good'
            ],
            [
                'group_id' => 32,
                'name' => 'RM632A-B',
                'code' => 'RM632AB',
                'description' => 'RM632A-B',
                'status' => 'good'
            ],
            [
                'group_id' => 4,
                'name' => 'RM632B-B',
                'code' => 'RM632BB',
                'description' => 'RM632B-B',
                'status' => 'good'
            ],
            [
                'group_id' => 15,
                'name' => 'RV659/LH609-B',
                'code' => 'RV659LH609B',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 33,
                'name' => 'BE301-B',
                'code' => 'BE301B',
                'description' => 'BE301B',
                'status' => 'good'
            ],
            [
                'group_id' => 33,
                'name' => 'BE302-B',
                'code' => 'BE302B',
                'description' => 'BE302-B',
                'status' => 'good'
            ],
            [
                'group_id' => 33,
                'name' => 'BE303-B',
                'code' => 'BE303B',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 9,
                'name' => 'BL704-ABS',
                'code' => 'BL704ABS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 9,
                'name' => 'BL705-ABS',
                'code' => 'BL705ABS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 9,
                'name' => 'BL107-ABS',
                'code' => 'BL107ABS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 8,
                'name' => 'FL102-ABS (ABSEN)',
                'code' => 'FL102ABS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 11,
                'name' => 'RV704-ABS',
                'code' => 'RV704ABS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 11,
                'name' => 'RV705-ABS',
                'code' => 'RV705ABS',
                'description' => 'RV705-ABS',
                'status' => 'good'
            ],
            [
                'group_id' => 11,
                'name' => 'RV706-ABS',
                'code' => 'RV706ABS',
                'description' => 'RV706-ABS (ABSEN)',
                'status' => 'good'
            ],
            [
                'group_id' => 11,
                'name' => 'RV707-ABS (ABSEN)',
                'code' => 'RV707-ABS',
                'description' => 'RV707-ABS (ABSEN)',
                'status' => 'good'
            ],
            [
                'group_id' => 11,
                'name' => 'RV111/LH105-ABS',
                'code' => 'RV111LH105ABS',
                'description' => 'RV111/LH105-ABS',
                'status' => 'good'
            ],
            [
                'group_id' => 34,
                'name' => 'MM401A-AB',
                'code' => 'MM401AAB',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 17,
                'name' => 'FN501-G',
                'code' => 'FN501G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 17,
                'name' => 'FN502-G',
                'code' => 'FN502G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 17,
                'name' => 'FN503-G',
                'code' => 'FN503G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 17,
                'name' => 'FN504-G',
                'code' => 'FN504G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL501-G',
                'code' => 'FL501G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL502-G',
                'code' => 'FL502G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL503-G',
                'code' => 'FL503G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL504-G',
                'code' => 'FL504G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL505-G',
                'code' => 'FL505G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 18,
                'name' => 'FL506-G',
                'code' => 'FL506G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL507-G',
                'code' => 'BL507G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL508-G',
                'code' => 'BL508G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL509-G',
                'code' => 'BL509G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL510-G',
                'code' => 'BL510G',
                'description' => 'BL510-G',
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL511-G',
                'code' => 'BL511G',
                'description' => 'BL511-G',
                'status' => 'good'
            ],
            [
                'group_id' => 19,
                'name' => 'BL512-G',
                'code' => 'BL512G',
                'description' => 'BL512-G',
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM501A-G',
                'code' => 'RM501AG',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM501B-G',
                'code' => 'RM501BG',
                'description' => 'RM501B-G',
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM502A-G',
                'code' => 'RM502AG',
                'description' => 'RM502A-G',
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM502B-G',
                'code' => 'RM502BG',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM503A-G',
                'code' => 'RM503AG',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM503B-G',
                'code' => 'RM503BG',
                'description' => 'RM503B-G',
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM504A-G',
                'code' => 'RM504AG',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 20,
                'name' => 'RM504BG',
                'code' => 'RM504BG',
                'description' => 'RM504B-G',
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV532/LH517-G',
                'code' => 'RV532LH517G',
                'description' => 'RV532/LH517-G',
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV533/LH520-G',
                'code' => 'RV533LH520G',
                'description' => 'RV533/LH520-G',
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV536/LH523-G',
                'code' => 'RV536LH523G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV537/LH524-G',
                'code' => 'RV537LH524G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV539/LH408-G',
                'code' => 'RV539LH408G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV542/LH526-G',
                'code' => 'RV542LH526G',
                'description' => 'RV542LH526G',
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV541-G (POS)',
                'code' => 'RV541GPOS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 21,
                'name' => 'RV543-G (POS)',
                'code' => 'RV543GPOS',
                'description' => 'RV543-G (POS)',
                'status' => 'good'
            ],
            [
                'group_id' => 22,
                'name' => 'BE201-G',
                'code' => 'BE201G',
                'description' => 'BE201-G',
                'status' => 'good'
            ],
            [
                'group_id' => 22,
                'name' => 'BE202-G',
                'code' => 'BE202G',
                'description' => 'BE202-G',
                'status' => 'good'
            ],
            [
                'group_id' => 22,
                'name' => 'BE203-G',
                'code' => 'BE203G',
                'description' => 'BE203-G',
                'status' => 'good'
            ],
            [
                'group_id' => 22,
                'name' => 'BE204-G',
                'code' => 'BE204G',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 23,
                'name' => 'FN601-H',
                'code' => 'FN601H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 23,
                'name' => 'FN602-H',
                'code' => 'FN602H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 23,
                'name' => 'FN603-H',
                'code' => 'FN603H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 23,
                'name' => 'FN604-H',
                'code' => 'FN604H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL601-H',
                'code' => 'FL601H',
                'description' => 'FL601H',
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL602-H',
                'code' => 'FL602H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL603-H',
                'code' => 'FL603H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL604-H',
                'code' => 'FL604H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL605-H',
                'code' => 'FL605H',
                'description' => 'FL605-H',
                'status' => 'good'
            ],
            [
                'group_id' => 24,
                'name' => 'FL606-H',
                'code' => 'FL606H',
                'description' => 'FL606-H',
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL607-H',
                'code' => 'BL607H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL608-H',
                'code' => 'BL608H',
                'description' => 'BL608-H',
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL609-H',
                'code' => 'BL609H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL610-H',
                'code' => 'BL610H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL611-H',
                'code' => 'BL611H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 25,
                'name' => 'BL612-H',
                'code' => 'BL612H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM601A-H',
                'code' => 'RM601AH',
                'description' => 'RM601A-H',
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM601B-H',
                'code' => 'RM601BH',
                'description' => 'RM601B-H',
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM602A-H',
                'code' => 'RM602AH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM602B-H',
                'code' => 'RM602BH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM603A-H',
                'code' => 'RM603AH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM603B-H',
                'code' => 'RM603BH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM604A-H',
                'code' => 'RM604AH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 26,
                'name' => 'RM604B-H',
                'code' => 'RM604BH',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV632/LH617-H',
                'code' => 'RV632LH617H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV633/LH620-H',
                'code' => 'RV633LH620H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV636/LH623-H',
                'code' => 'RV636LH623H',
                'description' => 'RV636/LH623-H',
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV637/LH624-H',
                'code' => 'RV637LH624H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV639/LH409-H',
                'code' => 'RV639LH409H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV642/LH626-H',
                'code' => 'RV642LH626H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV641-H (POS)',
                'code' => 'RV641HPOS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 27,
                'name' => 'RV643-H (POS)',
                'code' => 'RV643HPOS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 28,
                'name' => 'BE301-H',
                'code' => 'BE301H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 28,
                'name' => 'BE302-H',
                'code' => 'BE302H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 28,
                'name' => 'BE303-H',
                'code' => 'BE303H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 28,
                'name' => 'BE304-H',
                'code' => 'BE304H',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 29,
                'name' => 'FL511/LH524-G (POS)',
                'code' => 'FL511LH524GPOS',
                'description' => null,
                'status' => 'good'
            ],
            [
                'group_id' => 30,
                'name' => 'MM401A-GH',
                'code' => 'MM401AGH',
                'description' => 'MM401A-GH',
                'status' => 'good'
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
