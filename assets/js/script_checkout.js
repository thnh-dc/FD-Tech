document.addEventListener('DOMContentLoaded', function () {
    const usePointsInput = document.getElementById('usePointsInput');
    const useAllPointsBtn = document.getElementById('useAllPointsBtn');
    const pointDiscountText = document.getElementById('pointDiscountText');
    const memberDiscountText = document.getElementById('memberDiscountText');
    const finalTotalText = document.getElementById('finalTotalText');

    if (!usePointsInput || !pointDiscountText || !finalTotalText) return;

    const total = Number(usePointsInput.dataset.total || 0);
    const pointValue = Number(usePointsInput.dataset.pointValue || 100);
    const maxUsablePoints = Number(usePointsInput.dataset.maxPoints || 0);
    const memberDiscountPercent = Number(usePointsInput.dataset.memberDiscountPercent || 0);

    function formatMoney(value) {
        return Number(value).toLocaleString('vi-VN') + '₫';
    }

    function updateCheckoutTotal() {
        let points = parseInt(usePointsInput.value || '0', 10);
        if (isNaN(points) || points < 0) points = 0;
        if (points > maxUsablePoints) points = maxUsablePoints;

        usePointsInput.value = points;

        const pointDiscount = points * pointValue;
        const afterPointTotal = Math.max(total - pointDiscount, 0);
        const memberDiscount = Math.floor(afterPointTotal * memberDiscountPercent / 100);
        const finalTotal = Math.max(afterPointTotal - memberDiscount, 0);

        pointDiscountText.textContent = '-' + formatMoney(pointDiscount);
        if (memberDiscountText) memberDiscountText.textContent = '-' + formatMoney(memberDiscount);
        finalTotalText.textContent = formatMoney(finalTotal);
    }

    usePointsInput.addEventListener('input', updateCheckoutTotal);

    if (useAllPointsBtn) {
        useAllPointsBtn.addEventListener('click', function () {
            usePointsInput.value = maxUsablePoints;
            updateCheckoutTotal();
        });
    }

    updateCheckoutTotal();
});