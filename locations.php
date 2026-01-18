<?php
define('PAGE_TITLE', 'Locations');
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM store_locations");
$locations = $stmt->fetchAll();
?>

<div class="container" style="padding: 4rem 1.5rem;">
    <h1 class="text-center mb-2">Our Locations</h1>
    <p class="text-center mb-4" style="color: var(--text-light);">Find BON products near you.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
        <?php foreach ($locations as $loc): ?>
            <div style="background: white; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm);">
                <div style="height: 200px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if ($loc['image_url']): ?>
                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo $loc['image_url']; ?>" alt="<?php echo sanitize($loc['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size: 4rem;">🏪</span>
                    <?php endif; ?>
                </div>
                <div style="padding: 1.5rem;">
                    <h3 class="mb-1" style="color: var(--primary-color);"><?php echo sanitize($loc['name']); ?></h3>
                    <p class="mb-2" style="color: var(--text-dark); font-weight: 500; min-height: 48px;"><?php echo sanitize($loc['address']) . '<br>' . sanitize($loc['city']); ?></p>
                    
                    <div style="margin-bottom: 0.5rem; font-size: 0.9rem;">
                        <strong>Phone:</strong> <?php echo sanitize($loc['phone']); ?>
                    </div>
                    <div style="font-size: 0.9rem;">
                         <strong>Hours:</strong> <?php echo sanitize($loc['hours']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
