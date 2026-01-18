<?php
require_once 'config.php';
require_once 'includes/db.php';

echo "<h2>Adding ingredients column to products table...</h2>";

try {
    // Add ingredients column
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS ingredients TEXT AFTER description");
    echo "<p style='color: green;'>✓ Ingredients column added successfully!</p>";
    
    echo "<h2>Updating products...</h2>";
    
    // Product updates array
    $products = [
        1 => [
            'name' => 'BON Lemon (Single can)',
            'description' => "Zesty lemon burst with effervescent bubbles. Pure and refreshing.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Lemon Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ],
        2 => [
            'name' => 'BON Apple (Single can)',
            'description' => "Crisp apple flavor with pure alpine water. No sugar, no sweeteners - just natural refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Apple Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ],
        3 => [
            'name' => 'BON Mango (Single can)',
            'description' => "Tropical mango essence with refreshing bubbles. Pure exotic taste.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Mango Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ],
        4 => [
            'name' => 'BON Cherry (Single can)',
            'description' => "Sweet cherry flavor with natural effervescence. Perfectly balanced refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Cherry Juice (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ],
        5 => [
            'name' => 'BON Peached Ice Tea (Single can)',
            'description' => "Refreshing peach iced tea with natural ingredients. Perfect summer refreshment.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real tea extract\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Peach Juice (from concentrate), Tea Extract, Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 5 kcal\n• Fat: 0g\n• Carbohydrates: 1g\n• Sugars: 0g\n• Protein: 0g"
        ],
        6 => [
            'name' => 'BON Berry Mix (Single can)',
            'description' => "Delicious blend of mixed berries with sparkling water. Bursting with natural flavor.\n\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Natural Berry Juice Blend (from concentrate), Carbon Dioxide, Natural Flavoring.\n\nNutritional Information (per 330ml):\n• Energy: 4 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ],
        7 => [
            'name' => 'BON Pure Alpine (Single can)',
            'description' => "Pure alpine spring water with natural effervescence. The essence of mountain freshness.\n\n• No sugar added\n• No artificial sweeteners\n• Pure alpine spring water\n• Natural minerals\n• Naturally effervescent",
            'ingredients' => "Water (Alpine Spring Water), Carbon Dioxide, Natural Minerals.\n\nNutritional Information (per 330ml):\n• Energy: 0 kcal\n• Fat: 0g\n• Carbohydrates: 0g\n• Sugars: 0g\n• Protein: 0g"
        ]
    ];
    
    foreach ($products as $id => $data) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, ingredients = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $data['ingredients'], $id]);
        echo "<p style='color: green;'>✓ Updated product ID $id: {$data['name']}</p>";
    }
    
    // Update variety pack if it exists
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, ingredients = ? WHERE name LIKE '%Pack%' OR name LIKE '%Variety%'");
    $stmt->execute([
        'BON Variety 6-Pack',
        "Experience all our flavors in one convenient pack. Perfect for sharing or discovering your favorite.\n\n• 6 different flavors\n• No sugar added\n• No artificial sweeteners\n• Made with pure alpine water\n• Real fruit juice",
        "Contains 6 cans of assorted BON flavors. See individual product labels for specific ingredients.\n\nPack includes:\n• Lemon\n• Apple\n• Mango\n• Cherry\n• Berry Mix\n• Pure Alpine"
    ]);
    echo "<p style='color: green;'>✓ Updated variety pack</p>";
    
    echo "<h2 style='color: green;'>✅ All products updated successfully!</h2>";
    echo "<p><a href='shop.php'>View Products</a> | <a href='index.php'>Go to Home</a></p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
