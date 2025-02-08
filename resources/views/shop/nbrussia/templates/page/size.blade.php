<!--template:Размер одежды-->
@extends('shop.nbrussia.layouts.main')


@section('size', 'size container-xl')

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="size-h1 block-90">
        <h1>Размеры одежды New Balance</h1>
        <div class="underline-red"></div>
        <div class="links block-content">
            <div class="links-buttons">
                <a class="btn-nb" href="#men">Для мужчин</a>
                <a class="btn-nb" href="#women">Для женщин</a>
                <a class="btn-nb" href="#child">Детям</a>
            </div>
        </div>
    </div>

    <div id="men" class="block-90">
        <div class="block-content">
            <h2>Как выбрать размер мужской одежды New Balance</h2>
            <div class="underline-red"></div>
            <div class="block-image image-page-men">
                <div class="kg-sizechart-photo-inf kg-sizechart-chest">
                    <p class="kg-sizechart-photo-inf-title">Грудная клетка</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте подмышки, в самом широком месте грудной клетки.</p>
                </div>

                <div class="kg-sizechart-photo-inf kg-sizechart-waist">
                    <p class="kg-sizechart-photo-inf-title">Талия</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте окружность горизонтально в месте наибольшего сужения туловища.</p>
                </div>
                <div class="kg-sizechart-photo-inf kg-sizechart-seat">
                    <p class="kg-sizechart-photo-inf-title">Бедра</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте в самом широком месте бедер.</p>
                </div>
            </div>
        </div>
        <div class="sizechart-table">
            <div class="underline-red"></div>
            <table class="mt-1">
                <thead>
                <tr>
                    <th></th>
                    <th>Грудная клетка</th>
                    <th>Талия</th>
                    <th>Бедра</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="kh-sizechart-th">XS</td>
                    <td>84-89</td>
                    <td>68-74</td>
                    <td>81-86</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">S</td>
                    <td>89-96</td>
                    <td>74-79</td>
                    <td>86-94</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">M</td>
                    <td>96-104</td>
                    <td>79-86</td>
                    <td>94-102</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">L</td>
                    <td>104-112</td>
                    <td>86-94</td>
                    <td>102-109</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">XL</td>
                    <td>112-122</td>
                    <td>94-104</td>
                    <td>109-117</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">XXL</td>
                    <td>122-135</td>
                    <td>104-117</td>
                    <td>117-127</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="women" class="block-90">
        <div class="block-content">
            <h2>Как выбрать размер женской одежды New Balance</h2>
            <div class="underline-red"></div>
            <div class="block-image image-page-women">
                <div class="kg-sizechart-photo-inf kg-sizechart-chest">
                    <p class="kg-sizechart-photo-inf-title">Грудная клетка</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте подмышки, в самом широком месте грудной клетки.</p>
                </div>
                <div class="kg-sizechart-photo-inf kg-sizechart-waist">
                    <p class="kg-sizechart-photo-inf-title">Талия</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте окружность горизонтально в месте наибольшего сужения туловища.</p>
                </div>
                <div class="kg-sizechart-photo-inf kg-sizechart-seat">
                    <p class="kg-sizechart-photo-inf-title">Бедра</p>
                    <p class="kg-sizechart-photo-inf-desc">Измерьте в самом широком месте бедер.</p>
                </div>
            </div>
        </div>
        <div class="sizechart-table">
            <div class="underline-red"></div>
            <table class="mt-1">
                <thead>
                <tr>
                    <th></th>
                    <th>Грудная клетка</th>
                    <th>Талия</th>
                    <th>Бедра</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="kh-sizechart-th">XS</td>
                    <td>84-89</td>
                    <td>68-74</td>
                    <td>81-86</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">S</td>
                    <td>89-96</td>
                    <td>74-79</td>
                    <td>86-94</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">M</td>
                    <td>96-104</td>
                    <td>79-86</td>
                    <td>94-102</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">L</td>
                    <td>104-112</td>
                    <td>86-94</td>
                    <td>102-109</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">XL</td>
                    <td>112-122</td>
                    <td>94-104</td>
                    <td>109-117</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">XXL</td>
                    <td>122-135</td>
                    <td>104-117</td>
                    <td>117-127</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>



    <div id="child" class="block-90">
        <div class="block-content">
            <h2>Детская одежда</h2>
        </div>

        <div class="sizechart-table">
            <div class="underline-red"></div>
            <table class="mt-1">
                <thead>
                <tr>
                    <th></th>
                    <th>Рост</th>
                    <th>Грудная клетка</th>
                    <th>Талия</th>
                    <th>Бедра</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="kh-sizechart-th">S</td>
                    <td>127-137</td>
                    <td>76-81</td>
                    <td>58-61</td>
                    <td>86-95</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">M</td>
                    <td>137-147</td>
                    <td>81-89</td>
                    <td>61-64</td>
                    <td>95-102</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">L</td>
                    <td>147-157</td>
                    <td>89-97</td>
                    <td>64-69</td>
                    <td>102-109</td>
                </tr>
                <tr>
                    <td class="kh-sizechart-th">XL</td>
                    <td>157-170</td>
                    <td>97-102</td>
                    <td>69-71</td>
                    <td>109-112</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
