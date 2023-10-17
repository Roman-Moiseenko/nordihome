<?php
declare(strict_types=1);

namespace Entity;

use App\Entity\Picture;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PictureTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate()
    {
        $class = 'class-tag';
        $picture = Picture::create('/image.png',  'lucide-flat-home', 'Описание', true);

        $icon_no_class = '<i class="lucide-flat-home" area-label="Описание"></i>';
        $image_no_class = '<img src="/image.png" alt="Описание" >';

        $icon = '<i class="lucide-flat-home class-tag" area-label="Описание"></i>';
        $image = '<img src="/image.png" alt="Описание" class="class-tag">';

        self::assertEquals($picture->getIconHTML(), $icon_no_class);
        self::assertEquals($picture->getIconHTML($class), $icon);

        self::assertEquals($picture->getImageHTML(), $image_no_class);
        self::assertEquals($picture->getImageHTML($class), $image);

        self::assertEquals($picture->getHTML(), $image_no_class);
        $picture2 = Picture::create('/image.png', '', 'Описание', true);
        self::assertEquals($picture2->getHTML(), $image_no_class);

        $picture_to_save = $picture->toSave();
        $picture_load = Picture::load($picture_to_save);

        self::assertEquals(json_encode($picture), json_encode($picture_load));
    }
}
