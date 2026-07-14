<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * OT-Main production seed data
 *
 * Otmain_seed.php          = orchestrator only
 *   seedProposal / seedPackingList / seedInvoice / seedPurchaseOrder
 * seed/manifest.php        = load order
 * seed/customers.php       = client catalog
 * seed/proposals/*.php     = one quotation per file (production PDF migration)
 * seed/packing_lists/*.php = one packing list per file
 * seed/purchase_orders/    = one PO per file
 * seed/invoices/*.php      = Perfex invoices (e.g. cx_inv_101_t2.php)
 *
 * See customize/docs/SEED-FROM-PDF.md, SEED-AI-PROMPT.md, SEED-DEMO-DATA.md
 * Keep inventory in SEED-DEMO-DATA.md in sync with manifest.php.
 */
