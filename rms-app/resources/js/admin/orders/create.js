const dataElement = document.getElementById('orderCreateData');

if (!dataElement) {
    console.warn('Order create data not found.');
} else {
    const pageData = JSON.parse(dataElement.textContent || '{}');
    const menuMeta = pageData.menuMeta || {};
    const inventoryMeta = pageData.inventoryMeta || {};
    const serverTotal = pageData.serverTotal ?? null;
    const serverChange = pageData.serverChange ?? null;
    const messages = {
        validation: {
            tableRequired: pageData.messages?.validation?.table_required || 'Please select a table from the list before submitting the order.',
            menuRequired: pageData.messages?.validation?.menu_required || 'Please select at least one menu item before submitting the order.',
            cashPaidAmount: pageData.messages?.validation?.cash_paid_amount || 'Paid amount must be at least the total amount.',
            paymentReferenceRequired: pageData.messages?.validation?.payment_reference_required || 'Please enter the payment reference (Txn ID).',
            inventoryShortage: pageData.messages?.validation?.inventory_shortage || 'Some selected items are out of stock. Please reduce the quantity.',
        },
        ui: {
            menuEmpty: pageData.messages?.ui?.menu_empty || 'No recipe configured for selected items.',
            alertAutoHideMs: pageData.messages?.ui?.alert_autohide_ms || 3000,
        },
    };

    const state = {
        selectedItems: {},
        inventoryHasShortage: false,
    };

    const els = {
        paymentMethod: document.getElementById('paymentMethod'),
        paymentReferenceBox: document.getElementById('paymentReferenceBox'),
        paymentReference: document.getElementById('paymentReference'),
        paidAmountBox: document.getElementById('paidAmountBox'),
        paidAmount: document.getElementById('paidAmount'),
        changeBox: document.getElementById('changeBox'),
        changeAmount: document.getElementById('changeAmount'),
        menuSearch: document.getElementById('menuSearch'),
        menuSearchEmpty: document.getElementById('menuSearchEmpty'),
        orderForm: document.getElementById('orderForm'),
        orderFormAlert: document.getElementById('orderFormAlert'),
        totalAmount: document.getElementById('totalAmount'),
        totalDivider: document.getElementById('totalDivider'),
        totalRow: document.getElementById('totalRow'),
        submitBtn: document.getElementById('submitBtn'),
        emptyMsg: document.getElementById('emptyMsg'),
        inventoryNeedBox: document.getElementById('inventoryNeedBox'),
        inventoryNeedList: document.getElementById('inventoryNeedList'),
        orderItems: document.getElementById('orderItems'),
        hiddenInputs: document.getElementById('hiddenInputs'),
    };
    let alertTimer = null;

    const showAlert = (message) => {
        if (!els.orderFormAlert) return;
        if (alertTimer) {
            clearTimeout(alertTimer);
            alertTimer = null;
        }
        els.orderFormAlert.textContent = message;
        els.orderFormAlert.classList.remove('d-none');
        els.orderFormAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        alertTimer = window.setTimeout(() => {
            hideAlert();
        }, messages.ui.alertAutoHideMs);
    };

    const hideAlert = () => {
        if (!els.orderFormAlert) return;
        els.orderFormAlert.textContent = '';
        els.orderFormAlert.classList.add('d-none');
    };

    const computeChange = () => {
        const totalText = els.totalAmount?.textContent || '৳0.00';
        const total = parseFloat(totalText.replace(/[^0-9.-]+/g, '')) || 0;
        const paid = parseFloat(els.paidAmount && els.paidAmount.value ? els.paidAmount.value : 0) || 0;
        const change = Math.max(0, paid - total);

        if (els.changeBox && els.changeAmount) {
            els.changeAmount.textContent = '৳' + change.toFixed(2);
        }

        return { total, paid, change };
    };

    const validateSubmit = () => {
        const keys = Object.keys(state.selectedItems);
        let ok = keys.length > 0;

        if (ok) {
            ok = ok && validatePaymentFields(false).valid;
            ok = ok && !state.inventoryHasShortage;
        }

        if (els.submitBtn) els.submitBtn.disabled = !ok;
        return ok;
    };

    const syncSelectedCard = (id) => {
        const card = document.querySelector(`[onclick="toggleItem(${id}, this)"]`);
        if (!card) return;

        const selectedIndicator = card.querySelector('.selected-indicator');
        const selectedQty = card.querySelector('.selected-qty');
        const qty = state.selectedItems[id]?.qty || 0;

        if (state.selectedItems[id]) {
            selectedIndicator?.classList.remove('d-none');
            if (selectedQty) selectedQty.textContent = qty > 1 ? `× ${qty}` : '';
        } else {
            selectedIndicator?.classList.add('d-none');
            if (selectedQty) selectedQty.textContent = '';
        }
    };

    const updateOrderSummary = () => {
        const keys = Object.keys(state.selectedItems);
        let html = '';
        let hidden = '';
        let total = 0;
        const inventoryNeed = {};
        state.inventoryHasShortage = false;

        els.emptyMsg?.classList.toggle('d-none', keys.length > 0);
        els.totalDivider?.classList.toggle('d-none', keys.length === 0);
        els.totalRow?.classList.toggle('d-none', keys.length === 0);
        if (els.submitBtn) els.submitBtn.disabled = keys.length === 0;
        els.inventoryNeedBox?.classList.toggle('d-none', keys.length === 0);

        keys.forEach((id, index) => {
            const item = state.selectedItems[id];
            // Parse qty and ensure it's at least 1 (prevent negative values from text input)
            let parsedQty = parseInt(document.getElementById('qty-' + id)?.value, 10);
            if (Number.isNaN(parsedQty) || parsedQty < 1) parsedQty = 1;
            item.qty = parsedQty;
            const sub = item.qty * item.price;
            total += sub;

            html += `<div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px"><div><strong>${item.name}</strong> × ${item.qty}</div><div>৳${sub.toFixed(2)}</div></div>`;
            hidden += `<input type="hidden" name="items[${index}][menu_id]" value="${id}"><input type="hidden" name="items[${index}][quantity]" value="${item.qty}">`;

            item.ingredients.forEach((ingredient) => {
                const needed = parseFloat(ingredient.qty_per_dish || 0) * item.qty;
                if (needed <= 0) return;

                if (!inventoryNeed[ingredient.inventory_id]) {
                    inventoryNeed[ingredient.inventory_id] = {
                        item_name: ingredient.item_name,
                        unit: ingredient.unit,
                        qty: 0,
                        available: inventoryMeta?.[ingredient.inventory_id]?.quantity ?? 0,
                    };
                }

                inventoryNeed[ingredient.inventory_id].qty += needed;
            });
        });

        if (els.orderItems) els.orderItems.innerHTML = html;
        if (els.totalAmount) els.totalAmount.textContent = '৳' + total.toFixed(2);
        if (els.hiddenInputs) els.hiddenInputs.innerHTML = hidden;

        computeChange();
        validateSubmit();

        const needLines = Object.values(inventoryNeed)
            .map((row) => {
                const remaining = row.available - row.qty;
                const danger = remaining < 0;
                if (danger) state.inventoryHasShortage = true;
                return `<div class="mb-1 ${danger ? 'text-danger fw-semibold' : ''}">${row.item_name}: ${danger ? 'out of stock by' : 'will remain'} <strong>${Math.abs(remaining).toFixed(2)} ${row.unit}</strong></div>`;
            })
            .join('');

        if (els.inventoryNeedList) {
            els.inventoryNeedList.innerHTML = needLines || messages.ui.menuEmpty;
        }
    };

    const toggleItem = (id, el) => {
        const meta = menuMeta[id];
        if (!meta || !meta.ingredients || meta.ingredients.length === 0) return;

        if (state.selectedItems[id]) {
            delete state.selectedItems[id];
            el.classList.remove('selected');
            el.querySelector('.qty-control')?.classList.add('d-none');
        } else {
            state.selectedItems[id] = {
                name: meta.name,
                price: meta.price,
                qty: 1,
                ingredients: meta.ingredients || [],
            };
            el.classList.add('selected');
            el.querySelector('.qty-control')?.classList.remove('d-none');
        }

        syncSelectedCard(id);
        updateOrderSummary();
    };

    const changeQty = (id, delta) => {
        if (!state.selectedItems[id]) return;

        const input = document.getElementById('qty-' + id);
        const newQty = Math.max(1, parseInt(input?.value || '1') + delta);
        if (input) input.value = newQty;
        state.selectedItems[id].qty = newQty;
        syncSelectedCard(id);
        updateOrderSummary();
    };

    const validateOrderForm = () => {
        const tableId = document.querySelector('select[name="table_id"]');

        hideAlert();

        if (!tableId || !tableId.value) {
            showAlert(messages.validation.tableRequired);
            tableId?.focus();
            return false;
        }

        if (!Object.keys(state.selectedItems).length) {
            showAlert(messages.validation.menuRequired);
            return false;
        }

        const paymentValidation = validatePaymentFields(true);
        if (!paymentValidation.valid) {
            if (paymentValidation.reason === 'cash') {
                els.paidAmount?.focus();
            } else if (paymentValidation.reason === 'reference') {
                els.paymentReference?.focus();
            }
            return false;
        }

        if (state.inventoryHasShortage) {
            showAlert(messages.validation.inventoryShortage);
            return false;
        }

        return true;
    };

    const validatePaymentFields = (showMessage) => {
        const method = els.paymentMethod.value;

        if (method === 'cash') {
            const { total, paid } = computeChange();
            if (paid < total || total <= 0) {
                if (showMessage) showAlert(messages.validation.cashPaidAmount);
                return { valid: false, reason: 'cash' };
            }
        }

        if (['rocket', 'card'].includes(method) && !els.paymentReference.value.trim()) {
            if (showMessage) showAlert(messages.validation.paymentReferenceRequired);
            return { valid: false, reason: 'reference' };
        }

        return { valid: true };
    };

    const updatePaymentFields = () => {
        const needsReference = ['rocket', 'card'].includes(els.paymentMethod.value);
        const isCash = els.paymentMethod.value === 'cash';

        els.paymentReferenceBox?.classList.toggle('d-none', !needsReference);
        if (els.paymentReference) {
            els.paymentReference.required = needsReference;
            if (!needsReference) els.paymentReference.value = '';
        }

        els.paidAmountBox?.classList.toggle('d-none', !isCash);
        els.changeBox?.classList.toggle('d-none', !isCash);
        if (els.paidAmount) {
            els.paidAmount.required = isCash;
            if (!isCash) {
                els.paidAmount.value = '';
            }
        }

        if (!isCash && els.changeAmount) {
            els.changeAmount.textContent = '৳0.00';
        }

        validateSubmit();
    };

    const filterMenuItems = () => {
        const query = (els.menuSearch?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.menu-category-group').forEach((group) => {
            let categoryHasMatch = false;

            group.querySelectorAll('.menu-item-tile').forEach((tile) => {
                const matches = !query || tile.dataset.search.includes(query);
                tile.classList.toggle('d-none', !matches);
                categoryHasMatch = categoryHasMatch || matches;
                if (matches) visibleCount++;
            });

            group.classList.toggle('d-none', !categoryHasMatch);
        });

        els.menuSearchEmpty?.classList.toggle('d-none', visibleCount > 0);
    };

    const init = () => {
        document.querySelectorAll('[data-auto-hide]').forEach((element) => {
            const delay = parseInt(element.getAttribute('data-auto-hide') || '0', 10);
            if (delay > 0) {
                window.setTimeout(() => {
                    element.classList.add('d-none');
                }, delay);
            }
        });

        els.paymentMethod?.addEventListener('change', updatePaymentFields);
        els.menuSearch?.addEventListener('input', filterMenuItems);
        els.paidAmount?.addEventListener('input', () => {
            computeChange();
            validateSubmit();
        });

        // Ensure quantity inputs cannot be set below 1 (keyboard or paste)
        document.addEventListener('input', (event) => {
            const target = event.target;
            if (!target || !target.id) return;
            if (typeof target.id === 'string' && target.id.startsWith('qty-')) {
                let v = parseInt(target.value, 10);
                if (Number.isNaN(v) || v < 1) {
                    target.value = '1';
                }
                // Keep summary in sync when user types
                try { updateOrderSummary(); } catch (e) { /* ignore */ }
            }
        });

        els.orderForm?.addEventListener('submit', (event) => {
            if (!validateOrderForm()) {
                event.preventDefault();
            }
        });

        updatePaymentFields();

        if (serverTotal !== null) {
            if (els.totalAmount) els.totalAmount.textContent = '৳' + serverTotal;
            els.totalDivider?.classList.remove('d-none');
            els.totalRow?.classList.remove('d-none');
            if (els.submitBtn) els.submitBtn.disabled = false;
        }

        if (serverChange !== null) {
            if (els.changeAmount) els.changeAmount.textContent = '৳' + serverChange;
            els.changeBox?.classList.remove('d-none');
            els.paidAmountBox?.classList.remove('d-none');
        }
    };

    window.toggleItem = (id, el) => toggleItem(id, el);
    window.changeQty = (id, delta) => changeQty(id, delta);
    window.updateOrderSummary = () => updateOrderSummary();

    init();
}
