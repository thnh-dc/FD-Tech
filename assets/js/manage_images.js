// Hàm hiển thị tên file khi người dùng chọn ảnh
function displayFileName(input) {
    const textElement = document.getElementById('file-name-text');
    if (input.files && input.files.length > 0) {
        textElement.innerHTML = `<span style="color:#007bff; font-weight:600;">${input.files[0].name}</span>`;
    } else {
        textElement.innerText = "Chọn tệp hoặc kéo thả vào đây";
    }
}

function toggleStatus(id, currentStatus) {
    let formData = new FormData();
    formData.append('id', id);
    formData.append('status', currentStatus);
    let currentPath = window.location.pathname;
    let adminFolder = currentPath.substring(0, currentPath.indexOf('/admin/') + 7);
    let apiUrl = adminFolder + 'api_images.php?action=toggle_status';
    
    fetch(apiUrl, { 
        method: 'POST', 
        body: formData 
    })
    .then(res => res.json())
    .then(data => { 
        if(data.success) {
            location.reload(); 
        } else {
            alert('Có lỗi xảy ra khi cập nhật trạng thái!');
        }
    })
    .catch(err => console.error('Lỗi kết nối API:', err));
}

function deleteImage(id) {
    if(confirm('Bạn có chắc chắn muốn xóa vĩnh viễn hình ảnh này không?')) {
        let formData = new FormData();
        formData.append('id', id);
 
        let currentPath = window.location.pathname;
        let adminFolder = currentPath.substring(0, currentPath.indexOf('/admin/') + 7);
        let apiUrl = adminFolder + 'api_images.php?action=delete';
        
        fetch(apiUrl, { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.json())
        .then(data => { 
            if(data.success) {
                location.reload(); 
            } else {
                alert('Có lỗi xảy ra khi xóa hình ảnh!');
            }
        })
        .catch(err => console.error('Lỗi kết nối API:', err));
    }
}