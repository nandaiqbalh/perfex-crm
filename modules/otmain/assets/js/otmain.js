(function($) {
    'use strict';

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
        var currencyId = $('select[name="currency"]').val();
        if (!currencyId || !$('#otmain-bank-details-preview').length) {
            return;
        }
        $.get(admin_url + 'otmain/get_bank_details', { currency_id: currencyId }, function(response) {
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

    function otmainBuildPackingRow(item, index) {
        return '<tr class="item-row">' +
            '<td><input type="number" step="any" name="items[' + index + '][qty]" class="form-control otmain-packing-qty" value="' + (item.qty || 1) + '"></td>' +
            '<td><input type="text" name="items[' + index + '][description]" class="form-control" value="' + (item.description || '') + '"></td>' +
            '<td><input type="text" name="items[' + index + '][hs_code]" class="form-control" value="' + (item.hs_code || '') + '"></td>' +
            '<td><input type="number" step="any" name="items[' + index + '][unit_price]" class="form-control otmain-packing-rate" value="' + (item.rate || item.unit_price || 0) + '"></td>' +
            '<td><input type="text" class="form-control otmain-packing-line-total" readonly value="0"></td>' +
            '<td><input type="text" name="items[' + index + '][packing_detail]" class="form-control" value="' + (item.packing_detail || '') + '"></td>' +
            '<td><input type="number" step="any" name="items[' + index + '][gross_weight]" class="form-control otmain-packing-gross-weight" value="' + (item.gross_weight || '') + '"></td>' +
            '<td><input type="number" step="any" name="items[' + index + '][net_weight]" class="form-control" value="' + (item.net_weight || '') + '"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
    }

    function otmainRecalculatePackingRow($row) {
        var qty = parseFloat($row.find('.otmain-packing-qty').val()) || 0;
        var rate = parseFloat($row.find('.otmain-packing-rate').val()) || 0;
        $row.find('.otmain-packing-line-total').val((qty * rate).toFixed(2));
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
        return name.toUpperCase();
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
        var totalWeight = 0;
        $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').each(function() {
            otmainRecalculatePackingRow($(this));
            subtotal += parseFloat($(this).find('.otmain-packing-line-total').val()) || 0;
            totalWeight += parseFloat($(this).find('.otmain-packing-gross-weight').val()) || 0;
        });
        var currencyName = otmainSelectedCurrencyName('#otmain-packing-list-form');
        var rate = parseFloat($('#otmain-eur-usd-rate').val());
        var showUsd = (!currencyName || currencyName === 'EUR') && !isNaN(rate) && rate > 0;
        var subtotalUsd = showUsd ? subtotal * rate : 0;
        $('#otmain-packing-subtotal-label').text(currencyName ? ('Subtotal ' + currencyName) : 'Subtotal');
        $('#otmain-packing-subtotal-eur').text(subtotal.toFixed(2));
        $('#otmain-packing-subtotal-usd').text(subtotalUsd > 0 ? subtotalUsd.toFixed(2) : '-');
        $('#otmain-packing-usd-row').toggle(showUsd || subtotalUsd > 0);
        $('#otmain-packing-total-weight').text(totalWeight > 0 ? totalWeight.toFixed(2) + ' KGS' : '-');
    }

    function otmainAppendPackingItems(items) {
        if (!items || !items.length) {
            return;
        }
        var $tbody = $('#otmain-packing-items tbody');
        var startIndex = $tbody.find('tr.item-row').length;
        $.each(items, function(offset, item) {
            $tbody.append(otmainBuildPackingRow(item, startIndex + offset));
        });
        otmainRecalculatePackingTotals();
    }

    function otmainFillPackingItems(items) {
        var $tbody = $('#otmain-packing-items tbody');
        $tbody.find('tr.item-row').remove();
        $.each(items, function(i, item) {
            $tbody.append(otmainBuildPackingRow(item, i));
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

    $(function() {
        otmainEnsureCurrencySelectable();

        // Client change may refresh currency selectpicker; keep it editable.
        $('body').on('changed.bs.select change', '.f_client_id select[name="clientid"], #clientid, #rel_type, #rel_id', function() {
            setTimeout(otmainEnsureCurrencySelectable, 50);
        });

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
            init_ajax_search('estimate', '#quote_ref.ajax-search', undefined, admin_url + 'misc/get_relation_data');

            $('#expiry_days, input[name="date"]').on('change keyup', function() {
                otmainCalculateExpiryDate('input[name="date"]', '#expiry_days', 'input[name="duedate"]');
            });

            $('#quote_ref').on('change', function() {
                var estimateId = $(this).val();
                if (!estimateId) {
                    return;
                }
                $.get(admin_url + 'otmain/get_estimate_data/' + estimateId, function(data) {
                    if (!data || !data.clientid) {
                        return;
                    }
                    otmainFillInvoiceFromEstimate(data);
                }, 'json');
            });

            $('select[name="currency"]').on('change', otmainLoadBankDetails);
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

            // Packing details handlers for invoice form
            function otmainRecalcInvoicePacking() {
                var totalGw = 0, totalNw = 0;
                $('#otmain-packing-items tbody tr.item-row').each(function() {
                    totalGw += parseFloat($(this).find('.otmain-packing-gw').val()) || 0;
                    totalNw += parseFloat($(this).find('.otmain-packing-nw').val()) || 0;
                });
                $('#otmain-total-gw').text(totalGw.toFixed(2));
                $('#otmain-total-nw').text(totalNw.toFixed(2));
            }

            $(document).on('input change', '#otmain-packing-items .otmain-packing-gw, #otmain-packing-items .otmain-packing-nw', otmainRecalcInvoicePacking);
            $(document).on('click', '#otmain-packing-details-panel .otmain-remove-packing-row', function() {
                $(this).closest('tr').remove();
                otmainRecalcInvoicePacking();
            });
            $(document).on('click', '#otmain-packing-details-panel #otmain-add-packing-row', function() {
                var $tbody = $('#otmain-packing-details-panel #otmain-packing-items tbody');
                var i = $tbody.find('tr.item-row').length;
                var row = '<tr class="item-row">' +
                    '<td><input type="number" step="any" name="packing_items[' + i + '][qty]" class="form-control otmain-packing-qty" value="1"></td>' +
                    '<td><textarea name="packing_items[' + i + '][dimensions]" class="form-control otmain-packing-dims" rows="2"></textarea></td>' +
                    '<td><input type="number" step="any" name="packing_items[' + i + '][gw]" class="form-control otmain-packing-gw" value="0"></td>' +
                    '<td><input type="number" step="any" name="packing_items[' + i + '][nw]" class="form-control otmain-packing-nw" value="0"></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm otmain-remove-packing-row"><i class="fa fa-times"></i></button></td>' +
                    '</tr>';
                $tbody.append(row);
                otmainRecalcInvoicePacking();
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

            $('body').on('input change', '.otmain-packing-qty, .otmain-packing-rate, .otmain-packing-gross-weight', otmainRecalculatePackingTotals);
            $('#otmain-packing-list-form select[name="currency"]').on('change', otmainRecalculatePackingTotals);

            otmainRecalculatePackingTotals();
        }
    });

    $(document).on('click', '#otmain-packing-list-form #otmain-add-packing-row', function() {
        var i = $('#otmain-packing-list-form #otmain-packing-items tbody tr.item-row').length;
        $('#otmain-packing-list-form #otmain-packing-items tbody').append(otmainBuildPackingRow({}, i));
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
        var vat21 = 0;
        var vat0 = 0;

        $('#otmain-po-items tbody tr.item-row').each(function() {
            otmainRecalcPoRow($(this));
            var qty = parseFloat($(this).find('.otmain-po-qty').val()) || 0;
            var rate = parseFloat($(this).find('.otmain-po-rate').val()) || 0;
            var tax = parseFloat($(this).find('.otmain-po-tax').val()) || 0;
            var line = qty * rate;
            subtotal += line;

            if (tax !== 0) {
                vat21 += line * (tax / 100);
            }
        });

        $('#otmain-po-subtotal').text(subtotal.toFixed(2));
        $('#otmain-po-vat21').text(vat21.toFixed(2));
        $('#otmain-po-vat0').text(vat0.toFixed(2));
        $('#otmain-po-total').html('<strong>' + (subtotal + vat21 + vat0).toFixed(2) + '</strong>');
        otmainUpdateCurrencyLabels($('#otmain-purchase-order-form'), '#otmain-po-subtotal-label', '#otmain-po-total-label');
    }

    if ($('#otmain-purchase-order-form').length) {
        init_ajax_search('customer', '#supplierid.ajax-search');

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
        $(this).closest('tr').remove();
        if ($('#otmain-packing-list-form').length) {
            otmainRecalculatePackingTotals();
        }
        if ($('#otmain-purchase-order-form').length) {
            otmainRecalcPoTotals();
        }
    });

})(jQuery);
