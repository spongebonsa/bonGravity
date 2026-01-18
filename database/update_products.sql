-- Update product names and add descriptions/ingredients
UPDATE products SET 
    name = 'BON Lemon (Single can)',
    description = 'Zesty lemon burst with effervescent bubbles. Pure and refreshing.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Lemon Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 1;

UPDATE products SET 
    name = 'BON Apple (Single can)',
    description = 'Crisp apple flavor with pure alpine water. No sugar, no sweeteners - just natural refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Apple Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 2;

UPDATE products SET 
    name = 'BON Mango (Single can)',
    description = 'Tropical mango essence with refreshing bubbles. Pure exotic taste.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Mango Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 3;

UPDATE products SET 
    name = 'BON Cherry (Single can)',
    description = 'Sweet cherry flavor with natural effervescence. Perfectly balanced refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Cherry Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 4;

UPDATE products SET 
    name = 'BON Peached Ice Tea (Single can)',
    description = 'Refreshing peach iced tea with natural ingredients. Perfect summer refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real tea extract\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Peach Juice (from concentrate), Tea Extract, Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 5 kcal\n• Fat: 0g\n• Carbohydrates: 1g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 5;

UPDATE products SET 
    name = 'BON Berry Mix (Single can)',
    description = 'Delicious blend of mixed berries with sparkling water. Bursting with natural flavor.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Natural Berry Juice Blend (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 6;

UPDATE products SET 
    name = 'BON Pure Alpine (Single can)',
    description = 'Pure alpine spring water with natural effervescence. The essence of mountain freshness.\n\n• No sugar added\n• No artificial sweeteners\n• Pure alpine spring water\n• Natural minerals\n• Naturally effervescent',
    ingredients = 'Water (Alpine Spring Water), Carbon Dioxide, Natural Minerals.\n\nNutritional Information (per 330ml):\n• Energy: 0 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g'
WHERE id = 7;

UPDATE products SET 
    name = 'BON Variety 6-Pack',
    description = 'Experience all our flavors in one convenient pack. Perfect for sharing or discovering your favorite.\n\n• 6 different flavors\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice',
    ingredients = 'Contains 6 cans of assorted BON flavors. See individual product labels for specific ingredients.\n\nPack includes:\n• Lemon\n• Apple\n• Mango\n• Cherry\n• Berry Mix\n• Pure Alpine'
WHERE name LIKE '%Pack%' OR name LIKE '%Variety%';
