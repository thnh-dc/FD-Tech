<?php 
    include '../includes/header.php'; 
    include '../includes/db.php'; 

    // Lấy từ khóa tìm kiếm
    $search = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';
?>

<div class="container" style="margin-top: 30px;">
    <h2 class="section-title">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search); ?>"</h2>
    
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        if ($search != '') {
            // Tìm sản phẩm có tên giống với từ khóa
            $sql = "SELECT * FROM products WHERE name LIKE '%$search%'"; 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration: none; color: #333;">';
                    echo '<img src="data:image/jpeg;base64,'.base64_encode($row['image_data']).'" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
                    echo '<h3 style="font-size: 14px; margin-bottom: 8px;">'.$row['name'].'</h3>';
                    echo '<p style="color: #ee4d2d; font-weight: bold;">'.number_format($row['price']).' ₫</p>';
                    echo '</a></div>';
                }
            } else {
                echo '<p>Không tìm thấy sản phẩm nào phù hợp.</p>';
            }
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>