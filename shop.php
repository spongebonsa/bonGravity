<?php
define('PAGE_TITLE', 'Shop');
require_once 'includes/header.php';

// Get filter parameters
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
$categories = isset($_GET['categories']) ? (is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']]) : [];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build SQL query with JOIN to categories table
$has_reviews = false;
try {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'reviews'");
    $chk->execute([DB_NAME]);
    $has_reviews = $chk->fetchColumn() > 0;
} catch (Exception $e) {
    $has_reviews = false;
}

if ($has_reviews) {
    $sql = "SELECT p.*, c.name as category_name,
        (SELECT IFNULL(ROUND(AVG(r.rating),2),0) FROM reviews r WHERE r.product_id = p.id) as avg_rating,
        (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id) as review_count
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'active'";
} else {
    // Fallback query when reviews table is missing
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";
}
$params = [];

// Apply search filter
if (!empty($search_query)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

// Apply price filter only when user provided values
if ($min_price !== null || $max_price !== null) {
    // Provide defaults if one side is missing
    $min = $min_price !== null ? $min_price : 0;
    $max = $max_price !== null ? $max_price : 9999999;
    $sql .= " AND p.price BETWEEN ? AND ?";
    $params[] = $min;
    $params[] = $max;
}

// Apply category filter
if (!empty($categories) && is_array($categories)) {
    $placeholders = str_repeat('?,', count($categories) - 1) . '?';
    $sql .= " AND p.category_id IN ($placeholders)";
    $params = array_merge($params, $categories);
}

// Apply sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_az':
        $sql .= " ORDER BY p.name ASC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY p.created_at DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories for filter
$categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order");
$all_categories = $categories_stmt->fetchAll();
?>

<!-- Shop Hero Section -->
<div class="shop-hero">
    <div class="container">
        <h1>Our Products</h1>
        <p>Explore our full range of natural, refreshing beverages. No sugar, no sweeteners, just pure goodness.</p>
        
        <div class="shop-controls">
            <div class="search-bar">
                <form method="GET" action="shop.php" id="searchForm" style="margin: 0; display: flex; gap: 10px;">
                    <input type="text" name="search" 
                           placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($search_query); ?>"
                           style="flex: 1;">
                    
                    <!-- Include current filters as hidden fields -->
                    <?php if (!empty($categories) && is_array($categories)): ?>
                        <?php foreach ($categories as $cat_id): ?>
                            <input type="hidden" name="categories[]" value="<?php echo $cat_id; ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($min_price !== null): ?>
                        <input type="hidden" name="min_price" value="<?php echo $min_price; ?>">
                    <?php endif; ?>
                    
                    <?php if ($max_price !== null): ?>
                        <input type="hidden" name="max_price" value="<?php echo $max_price; ?>">
                    <?php endif; ?>
                    
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <button type="submit" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px;">
                        Search
                    </button>
                </form>
            </div>
            
            <select class="sort-dropdown" onchange="updateSorting(this.value)">
                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="name_az" <?php echo $sort == 'name_az' ? 'selected' : ''; ?>>Name: A-Z</option>
            </select>
            
            <div class="view-toggle">
                <button class="active" onclick="toggleView('grid')" title="Grid View">⊞</button>
                <button onclick="toggleView('list')" title="List View">☰</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="shop-container">
        <!-- Sidebar Filters -->
        <aside class="shop-sidebar">
            <form method="GET" action="shop.php" id="filterForm">
                <!-- Hidden fields to preserve search and sort -->
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                
                <!-- Categories Filter -->
                <div class="filter-section">
                    <h3>Categories</h3>
                    <div class="filter-option">
                        <input type="checkbox" id="cat-all" 
                               onclick="toggleAllCategories(this)" 
                               <?php echo empty($categories) ? 'checked' : ''; ?>>
                        <label for="cat-all">All Products</label>
                    </div>
                    <?php foreach ($all_categories as $cat): ?>
                    <div class="filter-option">
                        <input type="checkbox" name="categories[]" 
                               value="<?php echo $cat['id']; ?>" 
                               id="cat-<?php echo $cat['id']; ?>"
                               class="category-checkbox"
                               <?php echo in_array($cat['id'], $categories) ? 'checked' : ''; ?>>
                        <label for="cat-<?php echo $cat['id']; ?>"><?php echo sanitize($cat['name']); ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-section">
                    <h3>Price Range</h3>
                    <div class="price-inputs">
                        <div class="price-input">
                            <label>Min</label>
                            <input type="number" name="min_price" 
                                   value="<?php echo htmlspecialchars($min_price ?? ''); ?>" 
                                   min="0" step="0.01" placeholder="0">
                        </div>
                        <div class="price-input">
                            <label>Max</label>
                            <input type="number" name="max_price" 
                                   value="<?php echo htmlspecialchars($max_price ?? ''); ?>" 
                                   min="0" step="0.01" placeholder="1000">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Apply Filters
                </button>
                <button type="button" class="clear-filters" onclick="window.location.href='shop.php'">
                    Clear All Filters
                </button>
            </form>
        </aside>

        <!-- Products Grid -->
        <div class="shop-main">
            <div class="products-header">
                <p class="products-count">Showing <?php echo count($products); ?> products</p>
            </div>

            <?php if (count($products) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <div class="product-image">
                                    <?php if (!empty($product['image_url'])): ?>
                                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo $product['image_url']; ?>" alt="<?php echo sanitize($product['name']); ?>">
                                    <?php else: ?>
                                        <span>🥤</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <span class="product-category"><?php echo ucfirst(sanitize($product['category_name'] ?? 'Uncategorized')); ?></span>
                                    <h3><?php echo sanitize($product['name']); ?></h3>
                                    <span class="product-price"><?php echo format_product_price($product['price'], $product['currency'] ?? null); ?></span>
                                    <?php if ($has_reviews): ?>
                                    <div class="product-rating" style="margin-top:6px; font-size:0.95rem; color:#FFB02E;">
                                        <?php $ar = isset($product['avg_rating']) ? (float)$product['avg_rating'] : 0; $rc = $product['review_count'] ?? 0; ?>
                                        <?php for ($s=1;$s<=5;$s++): ?>
                                            <?php if ($ar >= $s-0.25): ?>
                                                <span>★</span>
                                            <?php else: ?>
                                                <span style="color:#E5E7EB">★</span>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span style="color:#64748B; font-size:0.85rem; margin-left:6px;">(<?php echo $rc; ?>)</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or search terms.</p>
                    <a href="shop.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Update sorting while preserving all filters
function updateSorting(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

function toggleView(view) {
    const productGrid = document.querySelector('.product-grid');
    const buttons = document.querySelectorAll('.view-toggle button');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (view === 'list') {
        productGrid.style.gridTemplateColumns = '1fr';
    } else {
        productGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
    }
}

// Category filter functions
function toggleAllCategories(checkbox) {
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Update "All Categories" checkbox state
function updateAllCategoriesCheckbox() {
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const allChecked = Array.from(categoryCheckboxes).every(cb => cb.checked);
    const noneChecked = Array.from(categoryCheckboxes).every(cb => !cb.checked);
    const allCategoriesCheckbox = document.getElementById('cat-all');
    
    if (allChecked) {
        allCategoriesCheckbox.checked = true;
        allCategoriesCheckbox.indeterminate = false;
    } else if (noneChecked) {
        allCategoriesCheckbox.checked = false;
        allCategoriesCheckbox.indeterminate = false;
    } else {
        allCategoriesCheckbox.checked = false;
        allCategoriesCheckbox.indeterminate = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update "All Categories" checkbox
    updateAllCategoriesCheckbox();
    
    // Add event listeners to category checkboxes
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateAllCategoriesCheckbox);
    });
    
    // Fix for form submission - ensure all data is included
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.querySelector('#searchForm input[name="search"]');
    const sortSelect = document.querySelector('.sort-dropdown');
    
    if (filterForm && searchInput) {
        // Copy search value to hidden input when typing
        searchInput.addEventListener('input', function() {
            document.querySelector('#filterForm input[name="search"]').value = this.value;
        });
        
        // Copy sort value to hidden input when changing
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                document.querySelector('#filterForm input[name="sort"]').value = this.value;
            });
        }
    }
    
    // Price validation
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    
    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('change', function() {
            const min = parseFloat(this.value) || 0;
            const max = parseFloat(maxPriceInput.value) || 0;
            if (max > 0 && min > max) {
                alert('Minimum price cannot be greater than maximum price');
                this.value = max;
            }
        });
        
        maxPriceInput.addEventListener('change', function() {
            const max = parseFloat(this.value) || 0;
            const min = parseFloat(minPriceInput.value) || 0;
            if (min > 0 && max < min) {
                alert('Maximum price cannot be less than minimum price');
                this.value = min;
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>