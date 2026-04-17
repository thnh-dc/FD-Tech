<div style="background: white; padding: 32px; border: 1px solid var(--bg-light); border-radius: var(--radius-md); margin-top: 40px;">
    <div class="detail-tabs">
        <button type="button" class="tab-btn active" data-tab="tabDesc">Mô tả sản phẩm</button>
        <button type="button" class="tab-btn" data-tab="tabSpec">Thông số kỹ thuật</button>
    </div>
    <div id="tabDesc" class="tab-content active">
        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
    </div>
    <div id="tabSpec" class="tab-content">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="border-bottom: 1px solid var(--bg-light);">
                <th style="padding: 12px; width: 30%;">Danh mục</th>
                <td style="padding: 12px;"><?php echo htmlspecialchars($product['category_name']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--bg-light);">
                <th style="padding: 12px;">Bảo hành</th>
                <td style="padding: 12px;">12 Tháng chính hãng</td>
            </tr>
        </table>
    </div>
</div>