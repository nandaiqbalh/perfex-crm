(function ($) {
  'use strict';

  function $paymentModeSelect() {
    return $('#paymentModeField select[name="paymentmode"], select[name="paymentmode"]').first();
  }

  function isPaidSelected() {
    return $('input[name="otmain_expense_is_paid"]:checked').val() === '1';
  }

  function ensureClearedHidden(on) {
    var $select = $paymentModeSelect();
    $('#otmain_paymentmode_cleared').remove();
    if (on && $select.length) {
      $select.after('<input type="hidden" name="paymentmode" id="otmain_paymentmode_cleared" value="">');
    }
  }

  function refreshPaymentModeUi() {
    var paid = isPaidSelected();
    var $field = $('#paymentModeField');
    var $select = $paymentModeSelect();
    var $addLink = $('a[onclick*="paymentModeField"]');

    $field.show();
    $addLink.hide();

    if (!$select.length) {
      return;
    }

    if (paid) {
      ensureClearedHidden(false);
      $select.prop('disabled', false);
      if ($select.hasClass('selectpicker')) {
        $select.selectpicker('refresh');
      }
    } else {
      $select.val('').prop('disabled', true);
      if ($select.hasClass('selectpicker')) {
        $select.selectpicker('val', '');
        $select.selectpicker('refresh');
      }
      ensureClearedHidden(true);
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

        if (isPaidSelected()) {
          ensureClearedHidden(false);
          $select.prop('disabled', false);
          if ($select.hasClass('selectpicker')) {
            $select.selectpicker('refresh');
          }
          if (!$select.val()) {
            alert_float('danger', 'Payment method is required when the expense is marked as Paid.');
            refreshPaymentModeUi();
            return false;
          }
        } else {
          $select.prop('disabled', false).val('');
          if ($select.hasClass('selectpicker')) {
            $select.selectpicker('val', '');
            $select.selectpicker('refresh');
          }
          ensureClearedHidden(true);
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
    wrapExpenseSubmitHandler();

    $(document).on('change', 'input[name="otmain_expense_is_paid"]', refreshPaymentModeUi);
  });
})(jQuery);
