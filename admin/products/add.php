<?php include('../../includes/header.php'); ?>

<div class="admin-wrapper">
    <div class="admin-main" style="max-width: 1000px; margin: 0 auto;">
        
        <header style="margin-bottom: 30px;">
            <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">← Quay lại danh sách</a>
            <h1 style="margin-top: 10px; color: var(--primary);">Thêm sản phẩm mới</h1>
        </header>

        <form action="process_add.php" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px;">
                
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Thông tin cơ bản</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: iPhone 15 Pro Max 256GB" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô tả chi tiết sản phẩm</label>
                            <textarea name="description" class="form-control" rows="10" placeholder="Nội dung mô tả, thông số kỹ thuật..."></textarea>
                        </div>
                    </section>

                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Dữ liệu bán hàng</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Giá bán lẻ (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" placeholder="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Giá khuyến mãi</label>
                                <input type="number" name="sale_price" class="form-control" placeholder="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số lượng nhập kho *</label>
                                <input type="number" name="stock" class="form-control" value="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mã SKU</label>
                                <input type="text" name="sku" class="form-control" placeholder="ABC-123">
                            </div>
                        </div>
                    </section>
                </div>

                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <section class="card">
                        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Phân loại</h3>
                        <div class="form-group">
                            <label class="form-label">Danh mục sản phẩm</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Chọn danh mục --</option>
                                <option value="1">Laptop</option>
                                <option value="2">Bàn phím</option>
                                <option value="3">Chuột</option>
                            </select>
                        </div>
                    </section>

                    <section class="card">
                        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Hình ảnh sản phẩm</h3>
                        <div class="upload-box">
                            <span style="font-size: 40px; display: block; margin-bottom: 10px;">🖼️</span>
                            <input type="file" name="image" style="font-size: 12px; margin-bottom: 10px;">
                            <p style="font-size: 11px; color: var(--text-muted);">Dung lượng tối đa 2MB</p>
                        </div>
                    </section>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="padding: 15px; font-weight: 700;">XÁC NHẬN THÊM</button>
                        <button type="reset" class="btn" style="background: #e0e0e0; color: #666; padding: 10px;">Hủy & Nhập lại</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>