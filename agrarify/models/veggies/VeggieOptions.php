<?php

namespace Agrarify\Models\Veggies;

use Illuminate\Support\Facades\Config;

class VeggieOptions {

    public static function getOptions()
    {
        /*
         * Elements are:
         *
         * 0 - Veggie ID
         * 1 - Veggie name
         * 2 - Veggie singular name
         * 3 - Veggie collective noun
         * 4 - Veggie image name
         */
        return [
            [1, 'Apples', 'apple', 'apples', 'apples'],
            [2, 'Asparagus', 'asparagus', 'bunches', 'asparagus'],
            [3, 'Artichokes', 'artichoke', 'artichokes', 'artichokes'],
            [4, 'Avocados', 'avocado', 'avocados', 'avocados'],
            [5, 'Broccoli', 'broccoli', 'heads', 'broccoli'],
            [6, 'Beans (Green)', 'bean', 'handfulls', 'beans-green'],
            [7, 'Beans (Dry)', 'bean', 'handfulls', 'beans-dry'],
            [8, 'Bok Choy', 'bok choy', 'heads', 'bok-choy'],
            [9, 'Basil', 'basil', 'bunches', 'basil'],
            [10, 'Beets', 'beet', 'beets', 'beets'],
            [11, 'Cilantro', 'cilantro', 'bunches', 'cilantro'],
            [12, 'Cabbage', 'cabbage', 'heads', 'cabbages'],
            [13, 'Cucumbers', 'cucumber', 'cucumbers', 'cucumbers'],
            [14, 'Carrots', 'carrot', 'bunches', 'carrots'],
            [15, 'Cauliflower', 'cauliflower', 'heads', 'cauliflower'],
            [16, 'Celery', 'celery', 'heads', 'celery'],
            [17, 'Cherries', 'cherry', 'handfulls', 'cherries'],
            [18, 'Eggplant', 'eggplant', 'eggplants', 'eggplant'],
            [19, 'Garlic', 'garlic', 'heads', 'garlic'],
            [20, 'Kale', 'kale', 'heads', 'kale'],
            [21, 'Lettuce', 'lettuce', 'heads', 'lettuce'],
            [22, 'Lemons', 'lemon', 'lemons', 'lemons'],
            [23, 'Limes', 'lime', 'limes', 'limes'],
            [24, 'Oranges', 'orange', 'oranges', 'oranges'],
            [25, 'Radishes', 'radish', 'bunches', 'radishes'],
            [26, 'Spinach', 'spinach', 'bunches', 'spinach'],
            [27, 'Chard', 'chard', 'bunches', 'chard'],
            [28, 'Turnips', 'turnip', 'bunches', 'turnips'],
            [29, 'Pumpkins', 'pumpkin', 'pumpkins', 'pumpkins'],
            [30, 'Squashes', 'squash', 'squashes', 'squashes'],
            [31, 'Peppers (Bell)', 'pepper', 'peppers', 'peppers-bell'],
            [32, 'Peppers (Hot)', 'pepper', 'peppers', 'peppers-hot'],
            [33, 'Tomatoes', 'tomato', 'tomatoes', 'tomatoes'],
            [34, 'Onions', 'onion', 'onions', 'onions'],
            [35, 'Leeks', 'leek', 'bunches', 'leeks'],
            [36, 'Plums', 'plum', 'plums', 'plums'],
            [37, 'Grapes', 'grape', 'bunches', 'grapes'],
            [38, 'Potatoes', 'potato', 'potatoes', 'potatoes'],
            [39, 'Potatoes (Sweet)', 'potato', 'potatoes', 'potatoes-sweet'],
        ];
    }

    public static function getMetadata()
    {
        return [
            'image_root_url' => Config::get('agrarify.static_veggie_icons_root_url'),
            'mdpi_suffix'    => '_mdpi.png',
            'hdpi_suffix'    => '_hdpi.png',
            'xhdpi_suffix'   => '_xhdpi.png',
            'xxhdpi_suffix'  => '_xxhdpi.png',
        ];
    }

}