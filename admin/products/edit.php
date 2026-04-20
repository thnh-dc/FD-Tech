<?php include('../../includes/header.php'); ?>

<div class="admin-wrapper">
    <div class="admin-main" style="max-width: 1000px; margin: 0 auto;">
        
        <header style="margin-bottom: 30px;">
            <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">← Quay lại danh sách</a>
            <h1 style="margin-top: 10px; color: var(--primary);">Cập nhật sản phẩm</h1>
        </header>

        <form action="process_edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="1"> 

            <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px;">
                
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Thông tin cơ bản</h3>
                        <div class="form-group">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" name="name" class="form-control" value="Laptop Asus ROG Strix G15">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="10">Cấu hình chi tiết...</textarea>
                        </div>
                    </section>

                    <section class="card">
                        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Dữ liệu bán hàng</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Giá bán lẻ (VNĐ)</label>
                                <input type="number" name="price" class="form-control" value="28500000">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số lượng kho</label>
                                <input type="number" name="stock" class="form-control" value="15">
                            </div>
                        </div>
                    </section>
                </div>

                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <section class="card">
                        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Hình ảnh hiện tại</h3>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <img src="/FD-Tech/assets/images/sample.jpg" style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                        </div>
                        <div class="upload-box" style="padding: 15px;">
                            <label style="font-size: 12px; font-weight: 600;">Thay đổi ảnh mới:</label>
                            <input type="file" name="image" style="font-size: 12px; margin-top: 10px;">
                        </div>
                    </section>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="padding: 15px; font-weight: 700; background: var(--secondary);">LƯU THAY ĐỔI</button>
                        <a href="list.php" class="btn" style="background: #eee; text-align: center; text-decoration: none; color: #333; padding: 10px;">Hủy bỏ</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>