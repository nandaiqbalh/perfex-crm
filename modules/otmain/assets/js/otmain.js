(function($) {
    'use strict';

    function otmainFormatTaxRateValue(rate) {
        rate = parseFloat(rate);
        if (isNaN(rate)) {
            rate = 0;
        }
        if (Math.abs(rate % 1) < 1e-9) {
            return String(parseInt(rate, 10));
        }
        return String(parseFloat(rate.toFixed(4)));
    }

    /** CBM from L×W×H in mm × qty → m³ */
    function otmainCalcCbmMm(length, width, height, qty) {
        length = parseFloat(length) || 0;
        width = parseFloat(width) || 0;
        height = parseFloat(height) || 0;
        qty = parseFloat(qty) || 0;
        if (length <= 0 || width <= 0 || height <= 0 || qty <= 0) {
            return 0;
        }
        return (length * width * height * qty) / 1000000000;
    }

    function otmainPackingUnitOptionsHtml(selected) {
        selected = (selected || 'box').toString().toLowerCase();
        var opts = [
            { v: 'box', t: 'Box' },
            { v: 'pallet', t: 'Pallet' },
            { v: 'other', t: 'Other' }
        ];
        var html = '';
        opts.forEach(function(o) {
            html += '<option value="' + o.v + '"' + (selected === o.v ? ' selected' : '') + '>' + o.t + '</option>';
        });
        return html;
    }

    function otmainTogglePackingUnitLabel($row) {
        var isOther = ($row.find('.otmain-packing-unit-type').val() || '') === 'other';
        $row.find('.otmain-packing-unit-label').toggle(isOther);
    }

    function otmainRecalcPackingRowCbm($row) {
        var qty = parseFloat($row.find('.otmain-packing-qty').val()) || 0;
        // Packing list uses separate packing qty
        var $packQty = $row.find('.otmain-packing-pack-qty');
        if ($packQty.length) {
            qty = parseFloat($packQty.val()) || 0;
        }
        var cbm = otmainCalcCbmMm(
            $row.find('.otmain-packing-length').val(),
            $row.find('.otmain-packing-width').val(),
            $row.find('.otmain-packing-height').val(),
            qty
        );
        $row.find('.otmain-packing-cbm-display').val(cbm > 0 ? cbm.toFixed(3) : '0.00');
        $row.find('.otmain-packing-volume-hidden').val(cbm > 0 ? (cbm.toFixed(3) + ' CBM') : '');
        return cbm;
    }

    function otmainSyncTaxWrap($wrap) {
        if (!$wrap || !$wrap.length) {
            return null;
        }
        var rate = parseFloat($wrap.find('.otmain-tax-rate').val());
        if (isNaN(rate) || rate < 0) {
            rate = 0;
        }
        var rateLabel = otmainFormatTaxRateValue(rate);
        var taxname = 'VAT|' + rateLabel;
        var $sel = $wrap.find('select.otmain-tax-select');
        $sel.empty().append(
            $('<option></option>')
                .attr('value', taxname)
                .attr('data-taxrate', rateLabel)
                .prop('selected', true)
                .text(rateLabel + '%')
        );
        $sel.val([taxname]);
        return taxname;
    }

    function otmainConvertLegacyTaxSelect($select) {
        if (!$select.length || $select.hasClass('otmain-tax-select') || $select.closest('.otmain-tax-input-wrap').length) {
            return;
        }

        var name = $select.attr('name') || 'taxname';
        var rate = 0;
        var current = null;

        try {
            if ($select.data('selectpicker') || $select.parent().hasClass('bootstrap-select')) {
                current = $select.selectpicker('val');
            } else {
                current = $select.val();
            }
        } catch (e) {
            current = $select.val();
        }

        if ($.isArray(current) && current.length) {
            current = current[0];
        }
        if (current) {
            var parts = String(current).split('|');
            rate = parseFloat(parts.length > 1 ? parts[parts.length - 1] : current) || 0;
        } else {
            var $selected = $select.find('option:selected').first();
            if ($selected.length) {
                rate = parseFloat($selected.data('taxrate')) || 0;
            }
        }

        if ($select.data('selectpicker') || $select.parent().hasClass('bootstrap-select')) {
            try {
                $select.selectpicker('destroy');
            } catch (e2) {
                // ignore
            }
        }

        var rateLabel = otmainFormatTaxRateValue(rate);
        var isMain = $select.hasClass('main-tax') || name === 'taxname';
        var $wrap = $(
            '<div class="otmain-tax-input-wrap">' +
            '<div class="input-group">' +
            '<input type="number" step="any" min="0" class="form-control otmain-tax-rate" value="' + rateLabel + '" title="VAT %">' +
            '<span class="input-group-addon">%</span>' +
            '</div>' +
            '<select class="tax otmain-tax-select' + (isMain ? ' main-tax' : '') + '" name="' + name + '" multiple tabindex="-1" aria-hidden="true"></select>' +
            '</div>'
        );
        $select.replaceWith($wrap);
        otmainSyncTaxWrap($wrap);
    }

    function otmainInitFreeTaxInputs() {
        $('select.tax').not('.otmain-tax-select').each(function() {
            otmainConvertLegacyTaxSelect($(this));
        });

        $('select.otmain-tax-select').each(function() {
            var $sel = $(this);
            if ($sel.parent().hasClass('bootstrap-select')) {
                try {
                    $sel.selectpicker('destroy');
                } catch (e) {
                    // ignore
                }
            }
            otmainSyncTaxWrap($sel.closest('.otmain-tax-input-wrap'));
        });
    }

    // Make core calculate_total / preview helpers work with free-form VAT %.
    if ($.fn.selectpicker && !$.fn.selectpicker._otmainPatched) {
        var otmainOrigSelectpicker = $.fn.selectpicker;
        $.fn.selectpicker = function(option, val) {
            var $otmainOnly = this.filter('.otmain-tax-select');
            if ($otmainOnly.length && $otmainOnly.length === this.length) {
                if (option === 'val') {
                    if (arguments.length >= 2) {
                        return this.each(function() {
                            var $wrap = $(this).closest('.otmain-tax-input-wrap');
                            var raw = val;
                            var rate = 0;
                            if ($.isArray(raw) && raw.length) {
                                raw = raw[0];
                            }
                            if (raw) {
                                var parts = String(raw).split('|');
                                rate = parseFloat(parts.length > 1 ? parts[parts.length - 1] : raw) || 0;
                            }
                            $wrap.find('.otmain-tax-rate').val(otmainFormatTaxRateValue(rate));
                            otmainSyncTaxWrap($wrap);
                        });
                    }
                    var $first = this.first();
                    otmainSyncTaxWrap($first.closest('.otmain-tax-input-wrap'));
                    var currentVal = $first.val();
                    if (currentVal == null || currentVal === '') {
                        return null;
                    }
                    return $.isArray(currentVal) ? currentVal : [currentVal];
                }
                if (option === 'refresh' || option === 'destroy' || option === 'render') {
                    return this;
                }
                return this;
            }
            return otmainOrigSelectpicker.apply(this, arguments);
        };
        $.fn.selectpicker._otmainPatched = true;
    }

    $(function() {
        otmainInitFreeTaxInputs();

        $('body').on('input change', '.otmain-tax-rate', function() {
            otmainSyncTaxWrap($(this).closest('.otmain-tax-input-wrap'));
            if (typeof calculate_total === 'function') {
                calculate_total();
            }
        });

        $(document).on('item-added-to-table item-added-to-preview', function() {
            setTimeout(otmainInitFreeTaxInputs, 20);
        });
    });

    function otmainCalculateExpiryDate(dateField, daysField, targetField) {
        var days = parseInt($(daysField).val(), 10);
        var dateVal = $(dateField).val();
        if (!dateVal || isNaN(days)) {
            return;
        }
        var parts = dateVal.split(/[-\/]/);
        if (parts.length < 3) {
            return;
        }
        var dateObj;
        if (parts[0].length === 4) {
            dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
        } else {
            dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
        }
        dateObj.setDate(dateObj.getDate() + days);
        var formatted = ('0' + dateObj.getDate()).slice(-2) + '-' + ('0' + (dateObj.getMonth() + 1)).slice(-2) + '-' + dateObj.getFullYear();
        $(targetField).val(formatted).trigger('change');
    }

    function otmainLoadContacts(clientId, selectField, selectedId) {
        if (!clientId) {
            return;
        }
        $.get(admin_url + 'otmain/get_client_contacts/' + clientId, function(contacts) {
            var $select = $(selectField);
            $select.empty().append('<option value=""></option>');
            $.each(contacts, function(i, contact) {
                var name = $.trim((contact.firstname || '') + ' ' + (contact.lastname || ''));
                var label = name;
                if (contact.email) {
                    label += ' (' + contact.email + ')';
                }
                var selected = selectedId && String(contact.id) === String(selectedId) ? ' selected' : '';
                $select.append(
                    '<option value="' + contact.id + '" data-name="' + name + '" data-email="' + (contact.email || '') + '" data-phone="' + (contact.phonenumber || '') + '"' + selected + '>' + label + '</option>'
                );
            });
            if (selectedId) {
                $select.val(selectedId);
            }
            $select.selectpicker('refresh');
        }, 'json');
    }

    function otmainLoadBankDetails() {
        var bankAccount = ($('#otmain_bank_account').val() || '').toString().toUpperCase();
        if (!$('#otmain-bank-details-preview').length) {
            return;
        }
        if (bankAccount !== 'EUR' && bankAccount !== 'USD') {
            bankAccount = '';
        }
        var params = {};
        if (bankAccount) {
            params.bank_account = bankAccount;
        } else {
            params.currency_id = $('select[name="currency"]').val();
        }
        $.get(admin_url + 'otmain/get_bank_details', params, function(response) {
            $('#otmain-bank-details-preview').html(response.html || '');
        }, 'json');
    }

    function otmainPlainText(value) {
        if (!value) {
            return '';
        }
        return $('<div>').html(value).text().replace(/\u00a0/g, ' ').trim();
    }

    function otmainSetFieldValue(selector, value) {
        if (value === undefined || value === null || value === '') {
            return;
        }
        $(selector).val(otmainPlainText(value)).trigger('change');
    }

    function otmainFillInvoiceFromProposal(data) {
        if (!data) {
            return;
        }

        otmainSetFieldValue('input[name="invoice_title"]', data.invoice_title);
        otmainSetFieldValue('textarea[name="payment_terms_text"]', data.payment_terms_text);
        otmainSetFieldValue('input[name="lead_time"]', data.delivery_time);
        otmainSetFieldValue('input[name="availability"]', data.availability);
        otmainSetFieldValue('input[name="delivery_terms"]', data.shipment_terms);

        if (data.contact_person_name) {
            otmainSetFieldValue('input[name="contact_person_name"]', data.contact_person_name);
        }
        if (data.contact_person_email) {
            otmainSetFieldValue('input[name="contact_person_email"]', data.contact_person_email);
        }
        if (data.contact_person_phone) {
            otmainSetFieldValue('input[name="contact_person_phone"]', data.contact_person_phone);
        }

        if (data.items && data.items.length) {
            otmainFillItemsFromEstimate(data.items);
        }

        if (data.clientid) {
            var $client = $('.f_client_id #clientid');
            if ($client.length && (!$client.val() || $client.val() === '')) {
                $client.selectpicker('val', data.clientid);
                $client.trigger('change');
            } else if ($client.val() == data.clientid) {
                otmainLoadContacts(data.clientid, '#otmain_contact_id');
            }
        }
    }

    function otmainFillInvoiceFromEstimate(data) {
        if (!data) {
            return;
        }

        otmainSetFieldValue('input[name="invoice_title"]', data.invoice_title);
        otmainSetFieldValue('textarea[name="payment_terms_text"]', data.payment_terms_text);
        otmainSetFieldValue('input[name="lead_time"]', data.delivery_time);
        otmainSetFieldValue('input[name="availability"]', data.availability);
        otmainSetFieldValue('input[name="delivery_terms"]', data.shipment_terms);
        otmainFillItemsFromEstimate(data.items);

        if (data.clientid) {
            var $client = $('.f_client_id #clientid');
            if ($client.length && (!$client.val() || $client.val() === '')) {
                $client.selectpicker('val', data.clientid);
                $client.trigger('change');
            } else if ($client.val() == data.clientid) {
                otmainLoadContacts(data.clientid, '#otmain_contact_id');
            }
        }
    }

    function otmainFillItemsFromEstimate(items) {
        if (!items || !items.length) {
            return;
        }
        $('.invoice-items-table tbody .item').remove();
        $('.estimate-items-table tbody .item').remove();

        $.each(items, function(index, item) {
            add_item_to_table(item, undefined, index === 0);
        });
        calculate_total();
    }

    function otmainBuildPackingItemRow(item, index) {
        var taxrate = (item.taxrate !== undefined && item.taxrate !== null && item.taxrate !== '')
            ? item.taxrate
            : 0;
        if (item.taxname && !taxrate) {
            var rawTax = $.isArray(item.taxname) ? item.taxname[0] : item.taxname;
            if (rawTax) {
                var parts = String(rawTax).split('|');
                taxrate = parseFloat(parts.length > 1 ? parts[parts.length - 1] : rawTax) || 0;
            }
        }
        return '<tr class="item-row" data-row-index="' + index + '">' +
            '<td><input type="number" step="any" name="items[' + index + '][qty]" class="form-control otmain-packing-qty" value="' + (item.qty || 1) + '"></td>' +
            '<td><input type="text" name="items[' + index + '][description]" class="form-control otmain-packing-description" value="' + (item.description || '') + '"></td>' +
            '<td><input type="text" name="items[' + index + '][hs_code]" class="form-control" value="' + (item.hs_code || '') + '"></td>' +
            '<td><input type="number" step="any" name="items[' + index + '][unit_price]" class="form-control otmain-packing-rate" value="' + (item.rate || item.unit_price || 0) + '"></td>' +
            '<td><input type="number" step="any" min="0" name="items[' + index + '][taxrate]" class="form-control otmain-packing-tax" value="' + taxrate + '"></td>' +
            '<td><input type="text" class="form-control otmain-packing-line-total" readonly value="0"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
    }

    function otmainBuildPackingDetailRow(item, index) {
        var unitType = (item.unit_type || 'box').toString().toLowerCase();
        var packingQty = item.packing_qty !== undefined && item.packing_qty !== null && item.packing_qty !== ''
            ? item.packing_qty
            : 1;
        var length = item.length || '';
        var width = item.width || '';
        var height = item.height || '';
        var cbm = otmainCalcCbmMm(length, width, height, packingQty);
        var cbmDisplay = cbm > 0 ? cbm.toFixed(3) : '0.00';
        var volume = cbm > 0 ? (cbm.toFixed(3) + ' CBM') : (item.volume || '');
        return '<tr class="item-row packing-detail-row" data-row-index="' + index + '">' +
            '<td><input type="text" name="packing_items[' + index + '][description]" class="form-control otmain-packing-detail-item-label" value="' + (item.description || '') + '"></td>' +
            '<td>' +
                '<select name="packing_items[' + index + '][unit_type]" class="form-control otmain-packing-unit-type">' +
                    otmainPackingUnitOptionsHtml(unitType) +
                '</select>' +
                '<input type="text" name="packing_items[' + index + '][unit_label]" class="form-control otmain-packing-unit-label mtop5" placeholder="Unit label" value="' + (item.unit_label || '') + '" style="' + (unitType === 'other' ? '' : 'display:none;') + '">' +
            '</td>' +
            '<td><input type="number" step="any" min="0" name="packing_items[' + index + '][packing_qty]" class="form-control otmain-packing-pack-qty" value="' + packingQty + '"></td>' +
            '<td><input type="number" step="any" min="0" name="packing_items[' + index + '][length]" class="form-control otmain-packing-length" value="' + length + '"></td>' +
            '<td><input type="number" step="any" min="0" name="packing_items[' + index + '][width]" class="form-control otmain-packing-width" value="' + width + '"></td>' +
            '<td><input type="number" step="any" min="0" name="packing_items[' + index + '][height]" class="form-control otmain-packing-height" value="' + height + '"></td>' +
            '<td>' +
                '<input type="text" class="form-control otmain-packing-cbm-display" readonly value="' + cbmDisplay + '">' +
                '<input type="hidden" name="packing_items[' + index + '][volume]" class="otmain-packing-volume-hidden" value="' + volume + '">' +
            '</td>' +
            '<td><input type="number" step="any" name="packing_items[' + index + '][gross_weight]" class="form-control otmain-packing-gross-weight" value="' + (item.gross_weight || '') + '"></td>' +
            '<td><input type="number" step="any" name="packing_items[' + index + '][net_weight]" class="form-control" value="' + (item.net_weight || '') + '"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
    }

    function otmainBuildPackingRow(item, index) {
        // Legacy helper: invoice item only (packing is independent).
        $('#otmain-packing-list-form #otmain-packing-items tbody').append(otmainBuildPackingItemRow(item, index));
        return '';
    }

    function otmainAppendInvoiceItem(item, index) {
        $('#otmain-packing-list-form #otmain-packing-items tbody').append(otmainBuildPackingItemRow(item, index));
    }

    function otmainAppendPackingDetail(item, index) {
        $('#otmain-packing-list-form #otmain-packing-details-items tbody').append(otmainBuildPackingDetailRow(item, index));
    }

    function otmainReindexPackingRows() {
        $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').each(function(i) {
            $(this).attr('data-row-index', i);
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/items\[\d+]/, 'items[' + i + ']'));
                }
            });
        });
        $('#otmain-packing-list-form #otmain-packing-details-items tbody tr.item-row').each(function(i) {
            $(this).attr('data-row-index', i);
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/packing_items\[\d+]/, 'packing_items[' + i + ']'));
                }
            });
        });
    }

    function otmainRecalculatePackingRow($row) {
        var qty = parseFloat($row.find('.otmain-packing-qty').val()) || 0;
        var rate = parseFloat($row.find('.otmain-packing-rate').val()) || 0;
        $row.find('.otmain-packing-line-total').val((qty * rate).toFixed(2));
    }

    function otmainCurrencyDisplayCode(name) {
        name = (name || '').toString().trim().toUpperCase();
        if (!name || name === 'EUR' || name === '€' || name === 'EURO') {
            return '€';
        }
        return name.replace(/^€\s*/, '').replace(/^EURO\s*/, '');
    }

    function otmainSelectedCurrencyName($formSelector) {
        var $select = $(formSelector).find('select[name="currency"]');
        if (!$select.length) {
            return '';
        }
        var $opt = $select.find('option:selected');
        var name = ($opt.data('subtext') || $opt.text() || '').toString().trim();
        // Prefer currency code from option text like "USD" or "Euro"
        var raw = ($opt.text() || '').toString().trim();
        if (raw) {
            // Perfex options usually: name in text, symbol in subtext
            name = raw.split(/\s|\(/)[0];
        }
        return otmainCurrencyDisplayCode(name);
    }

    function otmainUpdateCurrencyLabels($form, subtotalLabelId, totalLabelId) {
        var currencyName = otmainSelectedCurrencyName('#' + $form.attr('id'));
        if (!currencyName) {
            return;
        }
        if (subtotalLabelId) {
            $(subtotalLabelId).text('Subtotal ' + currencyName);
        }
        if (totalLabelId) {
            $(totalLabelId).text('TOTAL ' + currencyName);
        }
    }

    function otmainRecalculatePackingTotals() {
        var subtotal = 0;
        var totalTax = 0;
        var totalWeight = 0;
        var totalCbm = 0;
        var byRate = {};

        $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').each(function() {
            otmainRecalculatePackingRow($(this));
            var line = parseFloat($(this).find('.otmain-packing-line-total').val()) || 0;
            var tax = parseFloat($(this).find('.otmain-packing-tax').val());
            if (isNaN(tax)) {
                tax = 0;
            }
            var taxAmount = line * (tax / 100);
            subtotal += line;
            totalTax += taxAmount;
            var key = String(tax);
            if (!byRate[key]) {
                byRate[key] = 0;
            }
            byRate[key] += taxAmount;
        });

        $('#otmain-packing-list-form #otmain-packing-details-items tbody tr.item-row').each(function() {
            totalWeight += parseFloat($(this).find('.otmain-packing-gross-weight').val()) || 0;
            totalCbm += otmainRecalcPackingRowCbm($(this));
        });

        var currencyName = otmainSelectedCurrencyName('#otmain-packing-list-form');
        var rate = parseFloat($('#otmain-conversion-rate').val());
        if (isNaN(rate) || rate <= 0) {
            rate = parseFloat($('#otmain-conversion-rate-default').val());
        }
        var docCurrencyId = String($('#otmain-packing-list-form select[name="currency"]').val() || '');
        var targetCurrencyId = String($('#otmain-packing-list-form select[name="conversion_currency"]').val() || '');
        var targetName = '';
        var $targetOpt = $('#otmain-packing-list-form select[name="conversion_currency"] option:selected');
        if ($targetOpt.length) {
            targetName = otmainCurrencyDisplayCode(($targetOpt.text() || '').toString().trim().split(/\s|\(/)[0]);
        }
        var showConverted = targetCurrencyId !== '' && docCurrencyId !== '' && targetCurrencyId !== docCurrencyId && !isNaN(rate) && rate > 0;
        var subtotalConverted = showConverted ? subtotal * rate : 0;

        var $vatRows = [];
        Object.keys(byRate).sort(function(a, b) {
            return parseFloat(a) - parseFloat(b);
        }).forEach(function(key) {
            $vatRows.push(
                '<tr class="otmain-packing-vat-row"><td><strong>VAT ' + key + '%</strong></td><td>' + byRate[key].toFixed(2) + '</td></tr>'
            );
        });
        $('#otmain-packing-totals-table tr.otmain-packing-vat-row').remove();
        $('#otmain-packing-total-row').before($vatRows.join(''));

        $('#otmain-packing-subtotal-label').text(currencyName ? ('Subtotal ' + currencyName) : 'Subtotal');
        $('#otmain-packing-total-label').text(currencyName ? ('TOTAL ' + currencyName) : 'TOTAL');
        $('#otmain-packing-subtotal-eur').text(subtotal.toFixed(2) + (currencyName ? (' ' + currencyName) : ''));
        $('#otmain-packing-converted-label').text(targetName ? ('Subtotal ' + targetName) : 'Subtotal converted');
        $('#otmain-packing-subtotal-converted').text(subtotalConverted > 0 ? (subtotalConverted.toFixed(2) + (targetName ? (' ' + targetName) : '')) : '-');
        $('#otmain-packing-converted-row').toggle(showConverted || subtotalConverted > 0);
        var rateLabelFrom = currencyName || 'DOC';
        var rateLabelTo = targetName || 'TARGET';
        $('#otmain-conversion-rate-label').text('Conversion rate (' + rateLabelFrom + ' → ' + rateLabelTo + ')');
        $('#otmain-packing-total').html('<strong>' + (subtotal + totalTax).toFixed(2) + (currencyName ? (' ' + currencyName) : '') + '</strong>');
        $('#otmain-packing-total-weight').text(totalWeight > 0 ? totalWeight.toFixed(2) + ' KGS' : '-');
        $('#otmain-packing-total-cbm').text(totalCbm > 0 ? totalCbm.toFixed(3) : '0.00');
    }

    function otmainAppendPackingItems(items) {
        if (!items || !items.length) {
            return;
        }
        // Quote import: commercial lines only — packing is added manually.
        var startIndex = $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').length;
        $.each(items, function(offset, item) {
            otmainAppendInvoiceItem(item, startIndex + offset);
        });
        otmainRecalculatePackingTotals();
    }

    function otmainItemHasPackagingData(item) {
        if (!item) {
            return false;
        }
        var length = parseFloat(item.length) || 0;
        var width = parseFloat(item.width) || 0;
        var height = parseFloat(item.height) || 0;
        if (length > 0 && width > 0 && height > 0) {
            return true;
        }
        if (item.packing_detail || item.volume) {
            return true;
        }
        if ((parseFloat(item.gross_weight) || 0) > 0 || (parseFloat(item.net_weight) || 0) > 0) {
            return true;
        }
        return false;
    }

    function otmainFillPackingItems(items) {
        $('#otmain-packing-list-form #otmain-packing-items tbody').find('tr.item-row').remove();
        $('#otmain-packing-list-form #otmain-packing-details-items tbody').find('tr.item-row').remove();
        var invoiceIdx = 0;
        var packingIdx = 0;
        $.each(items, function(i, item) {
            var qty = parseFloat(item.qty);
            var rate = parseFloat(item.rate !== undefined ? item.rate : item.unit_price);
            var hasPackaging = otmainItemHasPackagingData(item);
            var isPackingOnly = hasPackaging && (isNaN(qty) || qty === 0) && (isNaN(rate) || rate === 0);
            if (!isPackingOnly) {
                otmainAppendInvoiceItem(item, invoiceIdx++);
            }
            if (hasPackaging) {
                otmainAppendPackingDetail(item, packingIdx++);
            }
        });
        otmainRecalculatePackingTotals();
    }

    function otmainEnsureCurrencySelectable() {
        var $currency = $('select[name="currency"]');
        if (!$currency.length) {
            return;
        }
        $currency.prop('disabled', false);
        if ($currency.hasClass('selectpicker') || $currency.parent().hasClass('bootstrap-select')) {
            $currency.selectpicker('refresh');
        }
    }

    function otmainCalcSellingRate(purchaseAmount, profitPercent) {
        var purchase = parseFloat(purchaseAmount);
        if (isNaN(purchase)) {
            return null;
        }
        var profit = parseFloat(profitPercent);
        if (isNaN(profit)) {
            profit = 0;
        }
        return purchase * (1 + profit / 100);
    }

    function otmainQuoteUsesPurchaseProfit() {
        return $('body').find('.proposal-form').length > 0 || $('body').find('.estimate-form').length > 0;
    }

    function otmainRecalcRowSellingRate($row) {
        if (!$row || !$row.length) {
            return;
        }
        var purchase = $row.find('.otmain-purchase-amount, .otmain-purchase-amount-preview, input[name="purchase_amount"]').filter(':visible').first().val();
        if (purchase === undefined || purchase === null || purchase === '') {
            purchase = $row.find('.otmain-purchase-amount').val();
        }
        var profit = $row.find('.otmain-profit-percent, .otmain-profit-percent-preview').first().val();
        var rate = otmainCalcSellingRate(purchase, profit);
        if (rate === null) {
            return;
        }
        var rounded = (Math.round(rate * 10000) / 10000);
        $row.find('.otmain-selling-rate, .otmain-selling-rate-preview, input[name="rate"]').val(rounded);
    }

    function otmainRecalcPreviewSellingRate() {
        if (!otmainQuoteUsesPurchaseProfit()) {
            return;
        }
        var $main = $('tr.main');
        var purchase = $main.find('.otmain-purchase-amount-preview, input[name="purchase_amount"]').val();
        var profit = $main.find('.otmain-profit-percent-preview').val();
        var rate = otmainCalcSellingRate(purchase, profit);
        if (rate === null) {
            $main.find('.otmain-selling-rate-preview, input[name="rate"]').val('');
            return;
        }
        $main.find('.otmain-selling-rate-preview, input[name="rate"]').val(Math.round(rate * 10000) / 10000);
    }

    $(function() {
        otmainEnsureCurrencySelectable();

        // Client change may refresh currency selectpicker; keep it editable.
        $('body').on('changed.bs.select change', '.f_client_id select[name="clientid"], #clientid, #rel_type, #rel_id', function() {
            setTimeout(otmainEnsureCurrencySelectable, 50);
        });

        if (otmainQuoteUsesPurchaseProfit()) {
            $('body').on(
                'input change',
                '.otmain-purchase-amount, .otmain-purchase-amount-preview, .otmain-profit-percent, .otmain-profit-percent-preview, tr.main input[name="purchase_amount"]',
                function() {
                    var $row = $(this).closest('tr');
                    if ($row.hasClass('main')) {
                        otmainRecalcPreviewSellingRate();
                    } else {
                        otmainRecalcRowSellingRate($row);
                        if (typeof calculate_total === 'function') {
                            calculate_total();
                        }
                    }
                }
            );

            // Item catalog fills rate — treat as purchase amount on quotations.
            $(document).on('item-added-to-preview', function() {
                var $main = $('tr.main');
                var $rate = $main.find('input[name="rate"]');
                var catalogRate = $rate.val();
                if (catalogRate === undefined || catalogRate === '') {
                    return;
                }
                $main.find('.otmain-purchase-amount-preview, input[name="purchase_amount"]').val(catalogRate);
                otmainRecalcPreviewSellingRate();
            });
        }

        if ($('body').find('.estimate-form').length) {
            $('#expiry_days, input[name="date"]').on('change keyup', function() {
                otmainCalculateExpiryDate('input[name="date"]', '#expiry_days', 'input[name="expirydate"]');
            });

            var estimateSelectedContact = $('#otmain_contact_id').val();

            $('.f_client_id #clientid').on('change', function() {
                otmainLoadContacts($(this).val(), '#otmain_contact_id');
            });

            if ($('.f_client_id #clientid').val()) {
                otmainLoadContacts($('.f_client_id #clientid').val(), '#otmain_contact_id', estimateSelectedContact);
            }

            $('body').on('change', '#otmain_contact_id', function() {
                var $opt = $(this).find('option:selected');
                var name = ($opt.data('name') || '').toString().trim();
                var email = ($opt.data('email') || '').toString().trim();
                var phone = ($opt.data('phone') || '').toString().trim();
                if (name) {
                    $('input[name="contact_person_name"]').val(name);
                }
                if (email) {
                    $('input[name="contact_person_email"]').val(email);
                }
                if (phone) {
                    $('input[name="contact_person_phone"]').val(phone);
                }
            });
        }

        if ($('body').find('.proposal-form').length) {
            $('#expiry_days, input[name="date"]').on('change keyup', function() {
                otmainCalculateExpiryDate('input[name="date"]', '#expiry_days', 'input[name="open_till"]');
            });

            function proposalMaybeLoadContacts() {
                var relType = $('#rel_type').val();
                var relId = $('#rel_id').val();
                if (relType !== 'customer' || !relId) {
                    return;
                }
                otmainLoadContacts(relId, '#otmain_contact_id');
            }

            $('body').on('change', '#rel_type,#rel_id', proposalMaybeLoadContacts);
            proposalMaybeLoadContacts();

            $('body').on('change', '#otmain_contact_id', function() {
                var $opt = $(this).find('option:selected');
                var name = ($opt.data('name') || '').toString().trim();
                var email = ($opt.data('email') || '').toString().trim();
                var phone = ($opt.data('phone') || '').toString().trim();
                if (name) {
                    $('input[name="contact_person_name"]').val(name);
                }
                if (email) {
                    $('input[name="contact_person_email"]').val(email);
                }
                if (phone) {
                    $('input[name="contact_person_phone"]').val(phone);
                }
            });
        }

        if ($('body').find('.invoice-form').length) {
            init_ajax_search('proposal', '#proposal_id.ajax-search', undefined, admin_url + 'misc/get_relation_data');

            $('#expiry_days, input[name="date"]').on('change keyup', function() {
                otmainCalculateExpiryDate('input[name="date"]', '#expiry_days', 'input[name="duedate"]');
            });

            $('#proposal_id').on('change', function() {
                var proposalId = $(this).val();
                if (!proposalId) {
                    return;
                }
                $.get(admin_url + 'otmain/get_proposal_invoice_data/' + proposalId, function(data) {
                    if (!data) {
                        return;
                    }
                    otmainFillInvoiceFromProposal(data);
                }, 'json');
            });

            $('#otmain_bank_account').on('changed.bs.select change', otmainLoadBankDetails);
            otmainLoadBankDetails();

            $('.f_client_id #clientid').on('change', function() {
                otmainLoadContacts($(this).val(), '#otmain_contact_id');
            });

            if ($('.f_client_id #clientid').val()) {
                otmainLoadContacts($('.f_client_id #clientid').val(), '#otmain_contact_id');
            }

            $('body').on('change', '#otmain_contact_id', function() {
                var $opt = $(this).find('option:selected');
                var name = ($opt.data('name') || '').toString().trim();
                var email = ($opt.data('email') || '').toString().trim();
                var phone = ($opt.data('phone') || '').toString().trim();
                if (name) {
                    $('input[name="contact_person_name"]').val(name);
                }
                if (email) {
                    $('input[name="contact_person_email"]').val(email);
                }
                if (phone) {
                    $('input[name="contact_person_phone"]').val(phone);
                }
            });
        }

        if ($('#otmain-packing-list-form').length) {
            init_ajax_search('proposal', '#packing_quote_ref.ajax-search', undefined, admin_url + 'misc/get_relation_data');

            var packingLoadedProposals = {};
            var plSelectedContact = $('#otmain_pl_contact_id').val();

            $('select[name="clientid"]').on('change', function() {
                var clientId = $(this).val();
                otmainLoadContacts(clientId, '#otmain_pl_contact_id');
                if (!clientId) {
                    return;
                }
                $.get(admin_url + 'otmain/get_client_packing_data/' + clientId, function(data) {
                    if (!data || !data.company) {
                        return;
                    }
                    $('input[name="consignee_name"], input[name="purchaser_name"]').val(data.company);
                    $('textarea[name="consignee_address"], textarea[name="purchaser_address"]').val(data.address || '');
                    $('input[name="consignee_phone"], input[name="purchaser_phone"]').val(data.phone || '');
                    $('input[name="consignee_email"], input[name="purchaser_email"]').val(data.email || '');
                    if (data.default_currency) {
                        var $cur = $('#otmain-packing-list-form select[name="currency"]');
                        if ($cur.length) {
                            $cur.selectpicker('val', String(data.default_currency));
                            $cur.trigger('change');
                        }
                    }
                }, 'json');
            });

            if ($('select[name="clientid"]').val()) {
                otmainLoadContacts($('select[name="clientid"]').val(), '#otmain_pl_contact_id', plSelectedContact);
            }

            $('body').on('change', '#otmain_pl_contact_id', function() {
                var $opt = $(this).find('option:selected');
                var name = ($opt.data('name') || '').toString().trim();
                var email = ($opt.data('email') || '').toString().trim();
                var phone = ($opt.data('phone') || '').toString().trim();
                if (name) {
                    $('input[name="contact_person_name"]').val(name);
                }
                if (email) {
                    $('input[name="contact_person_email"]').val(email);
                }
                if (phone) {
                    $('input[name="contact_person_phone"]').val(phone);
                }
            });

            $('#packing_quote_ref').on('change', function() {
                var ids = $(this).val() || [];
                ids.forEach(function(proposalId) {
                    if (!proposalId || packingLoadedProposals[proposalId]) {
                        return;
                    }
                    packingLoadedProposals[proposalId] = true;
                    $.get(admin_url + 'otmain/get_proposal_data/' + proposalId, function(data) {
                        if (!data || !data.proposal_number) {
                            return;
                        }
                        var line = (data.proposal_number || '').toString().trim();
                        if (line) {
                            var $quoteRef = $('textarea[name="quote_ref"]');
                            var current = ($quoteRef.val() || '').toString().trim();
                            $quoteRef.val(current === '' ? line : current + '\n' + line);
                        }
                        if (data.vessel && !$('input[name="vessel"]').val()) {
                            $('input[name="vessel"]').val(data.vessel);
                        }
                        otmainAppendPackingItems(data.items || []);
                    }, 'json');
                });
            });

            $('body').on(
                'input change',
                '#otmain-packing-list-form .otmain-packing-qty, #otmain-packing-list-form .otmain-packing-pack-qty, #otmain-packing-list-form .otmain-packing-rate, #otmain-packing-list-form .otmain-packing-tax, #otmain-packing-list-form .otmain-packing-gross-weight, #otmain-packing-list-form .otmain-packing-length, #otmain-packing-list-form .otmain-packing-width, #otmain-packing-list-form .otmain-packing-height, #otmain-packing-list-form #otmain-conversion-rate',
                otmainRecalculatePackingTotals
            );
            $('body').on('change', '#otmain-packing-list-form .otmain-packing-unit-type', function() {
                otmainTogglePackingUnitLabel($(this).closest('tr'));
            });
            $('#otmain-packing-list-form select[name="currency"], #otmain-packing-list-form select[name="conversion_currency"]').on('change', otmainRecalculatePackingTotals);
            $('#otmain-packing-list-form select[name="currency"]').on('change', otmainRecalculatePackingTotals);

            otmainRecalculatePackingTotals();
        }
    });

    $(document).on('click', '#otmain-packing-list-form #otmain-add-packing-row', function() {
        var i = $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').length;
        otmainAppendInvoiceItem({}, i);
        otmainRecalculatePackingTotals();
    });

    $(document).on('click', '#otmain-packing-list-form #otmain-add-packing-detail-row', function() {
        var i = $('#otmain-packing-list-form #otmain-packing-details-items tbody tr.item-row').length;
        otmainAppendPackingDetail({}, i);
        otmainRecalculatePackingTotals();
    });

    function otmainLoadPoContacts(clientId, selectedId) {
        if (!clientId) {
            return;
        }

        $.get(admin_url + 'otmain/get_supplier_data/' + clientId, function(data) {
            if (!data || !data.company) {
                return;
            }

            var $address = $('textarea[name="supplier_address"]');
            if (!$address.val()) {
                $address.val(data.address || '');
            }

            var $select = $('#otmain_po_contact_id');
            $select.empty().append('<option value=""></option>');

            $.each(data.contacts || [], function(i, contact) {
                var name = $.trim((contact.firstname || '') + ' ' + (contact.lastname || ''));
                var label = name;
                if (contact.email) {
                    label += ' (' + contact.email + ')';
                }
                var selected = selectedId && String(contact.id) === String(selectedId) ? ' selected' : '';
                $select.append(
                    '<option value="' + contact.id + '" data-name="' + name + '" data-email="' + (contact.email || '') + '" data-phone="' + (contact.phonenumber || '') + '"' + selected + '>' + label + '</option>'
                );
            });

            if (selectedId) {
                $select.val(selectedId);
            } else if (data.primary_contact_id) {
                $select.val(data.primary_contact_id);
                $('input[name="contact_person"]').val(data.contact_person || '');
                $('input[name="email"]').val(data.email || '');
                $('input[name="phone"]').val(data.phone || '');
            }

            $select.selectpicker('refresh');
        }, 'json');
    }

    function otmainRecalcPoRow($row) {
        var qty = parseFloat($row.find('.otmain-po-qty').val()) || 0;
        var rate = parseFloat($row.find('.otmain-po-rate').val()) || 0;
        $row.find('.otmain-po-line-total').val((qty * rate).toFixed(2));
    }

    function otmainRecalcPoTotals() {
        var subtotal = 0;
        var byRate = {};
        var totalTax = 0;

        $('#otmain-po-items tbody tr.item-row').each(function() {
            otmainRecalcPoRow($(this));
            var qty = parseFloat($(this).find('.otmain-po-qty').val()) || 0;
            var rate = parseFloat($(this).find('.otmain-po-rate').val()) || 0;
            var tax = parseFloat($(this).find('.otmain-po-tax').val());
            if (isNaN(tax)) {
                tax = 0;
            }
            var line = qty * rate;
            subtotal += line;
            var taxAmount = line * (tax / 100);
            totalTax += taxAmount;
            var key = String(tax);
            if (!byRate[key]) {
                byRate[key] = 0;
            }
            byRate[key] += taxAmount;
        });

        var $vatRows = [];
        Object.keys(byRate).sort(function(a, b) {
            return parseFloat(a) - parseFloat(b);
        }).forEach(function(key) {
            $vatRows.push(
                '<tr class="otmain-po-vat-row"><td><strong>VAT ' + key + '%</strong></td><td>' + byRate[key].toFixed(2) + '</td></tr>'
            );
        });
        $('#otmain-po-totals-table tr.otmain-po-vat-row').remove();
        $('#otmain-po-subtotal-row').after($vatRows.join(''));

        $('#otmain-po-subtotal').text(subtotal.toFixed(2));
        $('#otmain-po-total').html('<strong>' + (subtotal + totalTax).toFixed(2) + '</strong>');
        otmainUpdateCurrencyLabels($('#otmain-purchase-order-form'), '#otmain-po-subtotal-label', '#otmain-po-total-label');
    }

    if ($('#otmain-purchase-order-form').length) {
        init_ajax_search('customer', '#supplierid.ajax-search');
        init_ajax_search('proposal', '#po_proposal_id.ajax-search', undefined, admin_url + 'misc/get_relation_data');

        var poSelectedContact = $('#otmain_po_contact_id').val();

        $('#supplierid').on('change', function() {
            otmainLoadPoContacts($(this).val());
        });

        if ($('#supplierid').val()) {
            otmainLoadPoContacts($('#supplierid').val(), poSelectedContact);
        }

        $('body').on('change', '#otmain_po_contact_id', function() {
            var $opt = $(this).find('option:selected');
            $('input[name="contact_person"]').val($opt.data('name') || '');
            $('input[name="email"]').val($opt.data('email') || '');
            $('input[name="phone"]').val($opt.data('phone') || '');
        });

        // Optional: fill Supplier Quote Ref text from proposal number when empty
        $('#po_proposal_id').on('change', function() {
            var $sel = $(this);
            var text = ($sel.find('option:selected').text() || '').toString().trim();
            var $ref = $('input[name="supplier_quote_ref"]');
            if (text && $ref.length && !$ref.val()) {
                $ref.val(text);
            }
        });

        $('body').on('input change', '.otmain-po-qty, .otmain-po-rate, .otmain-po-tax', otmainRecalcPoTotals);
        $('#otmain-purchase-order-form select[name="currency"]').on('change', otmainRecalcPoTotals);

        $('#otmain-add-po-row').on('click', function() {
            var i = $('#otmain-po-items tbody tr').length;
            $('#otmain-po-items tbody').append(
                '<tr class="item-row">' +
                '<td><input type="number" step="any" name="items[' + i + '][qty]" class="form-control otmain-po-qty" value="1"></td>' +
                '<td><input type="text" name="items[' + i + '][description]" class="form-control"></td>' +
                '<td><input type="number" step="any" name="items[' + i + '][unit_price]" class="form-control otmain-po-rate" value="0"></td>' +
                '<td><input type="number" step="any" name="items[' + i + '][taxrate]" class="form-control otmain-po-tax" value="21"></td>' +
                '<td><input type="text" class="form-control otmain-po-line-total" readonly value="0.00"></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>' +
                '</tr>'
            );
            otmainRecalcPoTotals();
        });

        otmainRecalcPoTotals();
    }

    $(document).on('click', '.otmain-remove-row', function() {
        var $row = $(this).closest('tr');
        if ($('#otmain-packing-list-form').length && (
            $row.closest('#otmain-packing-items').length ||
            $row.closest('#otmain-packing-details-items').length
        )) {
            $row.remove();
            otmainReindexPackingRows();
            otmainRecalculatePackingTotals();
            return;
        }
        $row.remove();
        if ($('#otmain-packing-list-form').length) {
            otmainRecalculatePackingTotals();
        }
        if ($('#otmain-purchase-order-form').length) {
            otmainRecalcPoTotals();
        }
    });

})(jQuery);
