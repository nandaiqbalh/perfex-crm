<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * OT-Main production seed data
 *
 * Otmain_seed.php          = orchestrator only
 * seed/manifest.php        = load order
 * seed/customers.php       = client catalog
 * seed/proposals/*.php     = one quotation per file (production PDF migration)
 * seed/packing_lists/*.php = one packing list per file
 * seed/purchase_orders/    = one PO per file
 * seed/invoices/           = Perfex invoices (rare)
 *
 * See customize/docs/SEED-FROM-PDF.md and customize/docs/SEED-DEMO-DATA.md
 * Current production inventory is listed in SEED-DEMO-DATA.md (keep in sync with manifest.php).
 */
