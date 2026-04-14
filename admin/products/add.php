<?php include('../../includes/header.php'); ?>
<div class="admin-wrapper" style="padding: 40px; background: var(--bg-light); min-height: 100vh;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <header style="margin-bottom: 30px;">
            <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">← Quay lại danh sách</a>
            <h1 style="margin-top: 10px; color: var(--primary);">Thêm sản phẩm mới</h1>
        </header>

        <form action="" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px;">
                
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Thông tin cơ bản</h3>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Tên sản phẩm *</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm (Ví dụ: iPhone 15 Pro Max 256GB)">
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label class="form-label" style="font-weight: 600;">Mô tả chi tiết sản phẩm</label>
                            <textarea name="description" class="form-control" rows="12" placeholder="Nội dung mô tả, thông số kỹ thuật..."></textarea>
                        </div>
                    </section>

                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Dữ liệu bán hàng</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Giá bán lẻ (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" placeholder="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Giá khuyến mãi (Nếu có)</label>
                                <input type="number" name="sale_price" class="form-control" placeholder="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số lượng nhập kho *</label>
                                <input type="number" name="stock" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mã SKU (Tự định nghĩa)</label>
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
                                <option>Laptop</option>
                                <option>Bàn phím</option>
                                <option>Chuột</option>
                            </select>
                        </div>
                    </section>

                    <section class="card">
                        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Hình ảnh sản phẩm</h3>
                        <div style="border: 2px dashed var(--secondary); padding: 30px; text-align: center; border-radius: var(--radius-md); background: #f0faff;">
                            <span style="font-size: 40px; display: block; margin-bottom: 10px;">🖼️</span>
                            <input type="file" name="image" style="font-size: 12px; margin-bottom: 10px;">
                            <p style="font-size: 11px; color: var(--text-muted);">Dung lượng tối đa 2MB (JPG, PNG, WEBP)</p>
                        </div>
                        <div id="preview-box" style="margin-top: 15px; text-align: center; display: none;">
                            <img id="img-preview" src="#" style="max-width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                        </div>
                    </section>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="padding: 18px; font-size: 16px; font-weight: 700; width: 100%;">
                            XÁC NHẬN ĐĂNG BÀI
                        </button>
                        <button type="reset" class="btn" style="background: #e0e0e0; color: #666; padding: 12px; width: 100%;">
                            Hủy & Nhập lại
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>