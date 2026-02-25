<script>
    // Form digit and negative value limiter
    window.limitDigits = function(input, maxDigits) {
        if (input.value < 0) {
            input.value = Math.abs(input.value);
        }
        const parts = input.value.toString().split('.');
        if (parts[0].length > maxDigits) {
            parts[0] = parts[0].slice(0, maxDigits);
            input.value = parts.join('.');
        }
        if (parseFloat(input.value) > 99999) {
            input.value = 99999;
        }
    };

    // UI Helpers
    window.openModal = function(id) { 
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('active'); 
    };
    window.closeModal = function(id) { 
        const modal = document.getElementById(id);
        if (modal) {
            // Safety check: Don't allow closing while saving
            if (id === 'menuModal' && window.isSaveInProgress && window.isSaveInProgress()) {
                alert("Please wait until saving is finished.");
                return;
            }
            modal.classList.remove('active'); 
        }
    };
    


    // Payment Helpers
    window.openPaymentModal = (id) => {
        const orderData = window.ordersData && window.ordersData[id];
        if (!orderData) return alert('Order data not found!');

        const total = orderData.totalAmount;
        const proofImage = orderData.paymentDetails?.proofImage || '';

        document.getElementById('payOrderId').value = id;
        document.getElementById('payTotalAmount').value = total;
        document.getElementById('paymentModalOrderInfo').textContent = `Order #${id.slice(0,6)} - Total: ₱${total.toLocaleString()}`;
        
        document.getElementById('paymentMethod').value = 'Cash';
        document.getElementById('amountReceived').value = '';
        document.getElementById('referenceNumber').value = '';
        
        const proofContainer = document.getElementById('proofContainer');
        const viewProofBtn = document.getElementById('viewProofBtn');
        if (proofImage && proofImage !== '') {
            document.getElementById('paymentMethod').value = 'GCash';
            if (orderData.paymentDetails?.referenceNumber) {
                document.getElementById('referenceNumber').value = orderData.paymentDetails.referenceNumber;
            }
            proofContainer.style.display = 'block';
            viewProofBtn.onclick = () => window.viewProof(proofImage);
        } else {
            proofContainer.style.display = 'none';
        }

        window.togglePaymentView();
        window.calculateChange();
        window.openModal('paymentModal');
    };

    window.togglePaymentView = () => {
        const method = document.getElementById('paymentMethod').value;
        const cashSection = document.getElementById('cashSection');
        const onlineSection = document.getElementById('onlineSection');
        if (cashSection) cashSection.style.display = method === 'Cash' ? 'block' : 'none';
        if (onlineSection) onlineSection.style.display = method === 'GCash' ? 'block' : 'none';
    };

    window.calculateChange = () => {
        const total = parseFloat(document.getElementById('payTotalAmount').value) || 0;
        const amountInput = document.getElementById('amountReceived');
        if (!amountInput) return;
        let received = parseFloat(amountInput.value) || 0;
        
        if (amountInput.value.length > 7) {
            amountInput.value = amountInput.value.slice(0, 7);
            received = parseFloat(amountInput.value) || 0;
        }
        if (received > 1000000) {
            amountInput.value = 1000000;
            received = 1000000;
        }

        const change = received - total;
        const label = document.getElementById('changeLabel');
        if (label) {
            if (received >= total) {
                label.textContent = '₱' + change.toFixed(2);
                label.style.color = 'var(--primary-gold)';
            } else {
                label.textContent = 'Insufficient';
                label.style.color = '#dc3545';
            }
        }
    };
</script>

<script type="module" src="../assets/js/admin-auth.js"></script>
<script type="module" src="../assets/js/admin-dashboard.js?v=2.3"></script>
