<aside class="sidebar">
    <h2 class="sidebar-title">BỘ LỌC CHI TIẾT</h2>
    <form action="product_list.php" method="GET" id="filterForm">
        
        <h3 style="font-size: 14px; margin-bottom: 8px;">Tìm kiếm</h3>
        <input type="text" name="search" class="search-input" placeholder="Tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">

        <h3 style="font-size: 14px; margin: 16px 0 8px;">Danh mục</h3>
        <label class="custom-radio"><input type="radio" name="category" value="0" <?php echo ($category_id==0)?'checked':''; ?>> Tất cả</label>
        <?php foreach ($categories as $cat): ?>
            <label class="custom-radio">
                <input type="radio" name="category" value="<?php echo $cat['id']; ?>" <?php echo ($category_id==$cat['id'])?'checked':''; ?>> 
                <?php echo htmlspecialchars($cat['name']); ?>
            </label>
        <?php endforeach; ?>

        <h3 style="font-size: 14px; margin: 16px 0 8px;">Khoảng giá</h3>
        <label class="custom-radio"><input type="radio" name="price" value="" <?php echo ($price_range=='')?'checked':''; ?>> Tất cả</label>
        <label class="custom-radio"><input type="radio" name="price" value="under_1m" <?php echo ($price_range=='under_1m')?'checked':''; ?>> Dưới 1 triệu</label>
        <label class="custom-radio"><input type="radio" name="price" value="1m_to_2m" <?php echo ($price_range=='1m_to_2m')?'checked':''; ?>> 1 triệu - 2 triệu</label>
        <label class="custom-radio"><input type="radio" name="price" value="over_2m" <?php echo ($price_range=='over_2m')?'checked':''; ?>> Trên 2 triệu</label>

        <h3 style="font-size: 14px; margin: 16px 0 8px;">Sắp xếp</h3>
        <select name="sort" class="search-input">
            <option value="new" <?php echo ($sort=='new')?'selected':''; ?>>Mới nhất</option>
            <option value="price_asc" <?php echo ($sort=='price_asc')?'selected':''; ?>>Giá thấp đến cao</option>
            <option value="price_desc" <?php echo ($sort=='price_desc')?'selected':''; ?>>Giá cao đến thấp</option>
        </select>
        
        <input type="hidden" name="page" id="pageInput" value="<?php echo $page; ?>">
    </form>
</aside>