document.addEventListener('DOMContentLoaded', function () {
    const usePointsInput = document.getElementById('usePointsInput');
    const useAllPointsBtn = document.getElementById('useAllPointsBtn');
    const pointDiscountText = document.getElementById('pointDiscountText');
    const finalTotalText = document.getElementById('finalTotalText');

    if (!usePointsInput || !pointDiscountText || !finalTotalText) {
        return;
    }

    const total = Number(usePointsInput.dataset.total || 0);
    const pointValue = Number(usePointsInput.dataset.pointValue || 100);
    const maxUsablePoints = Number(usePointsInput.dataset.maxPoints || 0);

    function formatMoney(value) {
        return Number(value).toLocaleString('vi-VN') + '₫';
    }

    function updatePointDiscount() {
        let points = parseInt(usePointsInput.value || '0', 10);

        if (isNaN(points) || points < 0) {
            points = 0;
        }

        if (points > maxUsablePoints) {
            points = maxUsablePoints;
        }

        usePointsInput.value = points;

        const discount = points * pointValue;
        const finalTotal = Math.max(total - discount, 0);

        pointDiscountText.textContent = '-' + formatMoney(discount);
        finalTotalText.textContent = formatMoney(finalTotal);
    }

    usePointsInput.addEventListener('input', updatePointDiscount);

    if (useAllPointsBtn) {
        useAllPointsBtn.addEventListener('click', function () {
            usePointsInput.value = maxUsablePoints;
            updatePointDiscount();
        });
    }

    updatePointDiscount();
});