(function ($) {
  'use strict';

  function $paymentModeSelect() {
    return $('#paymentModeField select[name="paymentmode"], select[name="paymentmode"]').first();
  }

  function parseAmountPaid() {
    var raw = ($('#otmain_expense_amount_paid').val() || '').toString().trim().replace(',', '.');
    if (raw === '') {
      return 0;
    }
    var n = parseFloat(raw);
    return isNaN(n) || n < 0 ? 0 : n;
  }

  function parseExpenseTotal() {
    var amount = parseFloat(($('input[name="amount"]').val() || '0').toString().replace(',', '.')) || 0;
    var tax1 = 0;
    var tax2 = 0;
    var $tax = $('select[name="tax"]');
    var $tax2 = $('select[name="tax2"]');
    if ($tax.length) {
      var t1 = parseFloat($tax.find('option:selected').data('taxrate'));
      if (!isNaN(t1)) {
        tax1 = t1;
      }
    }
    if ($tax2.length) {
      var t2 = parseFloat($tax2.find('option:selected').data('taxrate'));
      if (!isNaN(t2)) {
        tax2 = t2;
      }
    }
    return amount + (amount * tax1 / 100) + (amount * tax2 / 100);
  }

  function statusKeyFromAmounts(paid, total) {
    if (paid <= 0) {
      return 'not_paid';
    }
    if (total > 0 && paid + 0.00001 >= total) {
      return 'paid';
    }
    if (total <= 0 && paid > 0) {
      return 'paid';
    }
    return 'partially_paid';
  }

  function statusBadgeHtml(key) {
    var $prev = $('#otmain-expense-payment-status-preview');
    var paid = $prev.data('label-paid') || 'Paid';
    var partial = $prev.data('label-partial') || 'Partially paid';
    var unpaid = $prev.data('label-unpaid') || 'Not paid';
    if (key === 'paid') {
      return '<span class="label label-success">' + paid + '</span>';
    }
    if (key === 'partially_paid') {
      return '<span class="label label-warning">' + partial + '</span>';
    }
    return '<span class="label label-default">' + unpaid + '</span>';
  }

  function refreshStatusPreview() {
    var key = statusKeyFromAmounts(parseAmountPaid(), parseExpenseTotal());
    $('#otmain-expense-payment-status-preview').html(statusBadgeHtml(key));
  }

  function refreshPaymentModeUi() {
    var hasPaid = parseAmountPaid() > 0;
    var $field = $('#paymentModeField');
    var $select = $paymentModeSelect();
    var $addLink = $('a[onclick*="paymentModeField"]');

    $addLink.hide();
    $field.show();

    if (!$select.length) {
      return;
    }

    // Always keep select enabled so the value posts correctly.
    $select.prop('disabled', false);
    if ($select.hasClass('selectpicker')) {
      $select.selectpicker('refresh');
    }

    if (hasPaid) {
      $field.find('label').first().addClass('text-danger');
    } else {
      $field.find('label').first().removeClass('text-danger');
    }
  }

  function placeFieldsNearPaymentMode() {
    var $block = $('#otmain-expense-payment-fields');
    var $field = $('#paymentModeField');
    if (!$block.length || !$field.length) {
      return;
    }

    var $addLink = $('a[onclick*="paymentModeField"]');
    if ($addLink.length) {
      $block.insertBefore($addLink);
    } else {
      $block.insertBefore($field);
    }
  }

  function wrapExpenseSubmitHandler() {
    var $form = $('#expense-form');
    if (!$form.length) {
      return;
    }

    var tryWrap = function () {
      var validator = $form.data('validator');
      if (!validator || !validator.settings || typeof validator.settings.submitHandler !== 'function') {
        return false;
      }

      if (validator.settings._otmainExpenseWrapped) {
        return true;
      }

      var prev = validator.settings.submitHandler;
      validator.settings.submitHandler = function (form) {
        var $select = $paymentModeSelect();
        var paid = parseAmountPaid();

        if ($select.length) {
          $select.prop('disabled', false);
          if ($select.hasClass('selectpicker')) {
            $select.selectpicker('refresh');
          }
        }

        if (paid > 0) {
          if (!$select.length || !$select.val()) {
            var msg = $('#otmain-expense-payment-status-preview').data('msg-mode-required')
              || 'Payment method is required when amount paid is greater than zero.';
            alert_float('danger', msg);
            refreshPaymentModeUi();
            return false;
          }
        }

        return prev.call(this, form);
      };
      validator.settings._otmainExpenseWrapped = true;
      return true;
    };

    if (!tryWrap()) {
      var attempts = 0;
      var timer = setInterval(function () {
        attempts += 1;
        if (tryWrap() || attempts > 40) {
          clearInterval(timer);
        }
      }, 50);
    }
  }

  $(function () {
    if (!$('#otmain-expense-payment-fields').length) {
      return;
    }

    placeFieldsNearPaymentMode();
    refreshPaymentModeUi();
    refreshStatusPreview();
    wrapExpenseSubmitHandler();

    $(document).on(
      'input change',
      '#otmain_expense_amount_paid, input[name="amount"], select[name="tax"], select[name="tax2"]',
      function () {
        refreshPaymentModeUi();
        refreshStatusPreview();
      }
    );
  });
})(jQuery);
