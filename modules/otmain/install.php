<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!isset($CI) || !is_object($CI)) {
    $CI = &get_instance();
}

$CI->load->helper(OTMAIN_MODULE_NAME . '/otmain');

$estimateColumns = [
    'client_ref'             => "VARCHAR(191) NULL DEFAULT NULL",
    'shipment_terms'         => "TEXT NULL",
    'delivery_time'          => "TEXT NULL",
    'availability'           => "TEXT NULL",
    'payment_terms_text'     => "TEXT NULL",
    'quote_title'            => "VARCHAR(191) NULL DEFAULT NULL",
    'expiry_days'            => "INT(11) NULL DEFAULT NULL",
    'otmain_contact_id'      => "INT(11) NULL DEFAULT NULL",
    'contact_person_name'    => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_email'   => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_phone'   => "VARCHAR(50) NULL DEFAULT NULL",
];

foreach ($estimateColumns as $column => $definition) {
    if (!$CI->db->field_exists($column, db_prefix() . 'estimates')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'estimates` ADD `' . $column . '` ' . $definition);
    }
}

$invoiceColumns = [
    'quote_ref'              => "INT(11) NULL DEFAULT NULL",
    'invoice_title'          => "VARCHAR(191) NULL DEFAULT NULL",
    'expiry_days'            => "INT(11) NULL DEFAULT NULL",
    'document_title'         => "VARCHAR(191) NULL DEFAULT NULL",
    'payment_terms_text'     => "TEXT NULL",
    'delivery_terms'         => "TEXT NULL",
    'lead_time'              => "TEXT NULL",
    'delivery_address'       => "TEXT NULL",
    'availability'           => "TEXT NULL",
    'notes'                  => "TEXT NULL",
    'otmain_contact_id'      => "INT(11) NULL DEFAULT NULL",
    'contact_person_name'    => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_email'   => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_phone'   => "VARCHAR(50) NULL DEFAULT NULL",
    'total_usd_display'      => "VARCHAR(191) NULL DEFAULT NULL",
    'total_gold_display'     => "VARCHAR(191) NULL DEFAULT NULL",
    'packing_items'          => "JSON DEFAULT NULL",
    'total_gw'               => "DECIMAL(15,2) DEFAULT 0.00",
    'total_nw'               => "DECIMAL(15,2) DEFAULT 0.00",
    'total_cbm'              => "DECIMAL(15,2) DEFAULT 0.00",
    'conversion_rate'        => "DECIMAL(15,6) NULL DEFAULT NULL",
    'conversion_currency'    => "INT(11) NULL DEFAULT NULL",
    'bank_account'           => "VARCHAR(10) NULL DEFAULT NULL",
];

foreach ($invoiceColumns as $column => $definition) {
    if (!$CI->db->field_exists($column, db_prefix() . 'invoices')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `' . $column . '` ' . $definition);
    }
}

$proposalColumns = [
    'terms'                => "TEXT NULL",
    'document_title'       => "VARCHAR(191) NULL DEFAULT NULL",
    'client_ref'           => "VARCHAR(191) NULL DEFAULT NULL",
    'quote_title'          => "VARCHAR(191) NULL DEFAULT NULL",
    'expiry_days'          => "INT(11) NULL DEFAULT NULL",
    'otmain_contact_id'    => "INT(11) NULL DEFAULT NULL",
    'contact_person_name'  => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_email' => "VARCHAR(191) NULL DEFAULT NULL",
    'contact_person_phone' => "VARCHAR(50) NULL DEFAULT NULL",
    'payment_terms_text'   => "TEXT NULL",
    'shipment_terms'       => "TEXT NULL",
    'delivery_time'        => "TEXT NULL",
    'availability'         => "TEXT NULL",
    'notes'                => "TEXT NULL",
    'total_usd_display'    => "VARCHAR(191) NULL DEFAULT NULL",
    'total_gold_display'   => "VARCHAR(191) NULL DEFAULT NULL",
];

foreach ($proposalColumns as $column => $definition) {
    if (!$CI->db->field_exists($column, db_prefix() . 'proposals')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'proposals` ADD `' . $column . '` ' . $definition);
    }
}

if (!$CI->db->table_exists(db_prefix() . 'otmain_packing_lists')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "otmain_packing_lists` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `number` int(11) NOT NULL,
        `prefix` varchar(50) DEFAULT NULL,
        `formatted_number` varchar(100) DEFAULT NULL,
        `date` date NOT NULL,
        `clientid` int(11) NOT NULL DEFAULT 0,
        `quote_ref` text DEFAULT NULL,
        `consignee_name` varchar(191) DEFAULT NULL,
        `consignee_address` text,
        `consignee_phone` varchar(50) DEFAULT NULL,
        `consignee_email` varchar(100) DEFAULT NULL,
        `purchaser_name` varchar(191) DEFAULT NULL,
        `purchaser_address` text,
        `purchaser_phone` varchar(50) DEFAULT NULL,
        `purchaser_email` varchar(100) DEFAULT NULL,
        `vessel` varchar(191) DEFAULT NULL,
        `currency` int(11) NOT NULL DEFAULT 0,
        `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
        `subtotal_usd` decimal(15,2) NOT NULL DEFAULT 0.00,
        `adminnote` text,
        `addedfrom` int(11) NOT NULL DEFAULT 0,
        `datecreated` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `clientid` (`clientid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

$packingColumns = [
    'document_title'       => "VARCHAR(191) NULL DEFAULT 'Packing List & Invoice'",
    'quote_ref_ids'        => 'TEXT NULL',
    'total_weight'         => 'DECIMAL(15,2) NOT NULL DEFAULT 0.00',
    'otmain_contact_id'    => 'INT(11) NULL DEFAULT NULL',
    'contact_person_name'  => 'VARCHAR(191) NULL DEFAULT NULL',
    'contact_person_email' => 'VARCHAR(191) NULL DEFAULT NULL',
    'contact_person_phone' => 'VARCHAR(50) NULL DEFAULT NULL',
];

foreach ($packingColumns as $column => $definition) {
    if ($CI->db->table_exists(db_prefix() . 'otmain_packing_lists') && !$CI->db->field_exists($column, db_prefix() . 'otmain_packing_lists')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'otmain_packing_lists` ADD `' . $column . '` ' . $definition);
    }
}

if (!$CI->db->table_exists(db_prefix() . 'otmain_packing_list_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "otmain_packing_list_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `packing_list_id` int(11) NOT NULL,
        `description` text,
        `hs_code` varchar(100) DEFAULT NULL,
        `qty` decimal(15,2) NOT NULL DEFAULT 1.00,
        `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
        `taxrate` decimal(15,2) NOT NULL DEFAULT 0.00,
        `total` decimal(15,2) NOT NULL DEFAULT 0.00,
        `packing_qty` decimal(15,2) NOT NULL DEFAULT 1.00,
        `unit_type` varchar(20) NOT NULL DEFAULT 'box',
        `unit_label` varchar(100) DEFAULT NULL,
        `length` decimal(15,2) DEFAULT NULL,
        `width` decimal(15,2) DEFAULT NULL,
        `height` decimal(15,2) DEFAULT NULL,
        `packing_detail` text,
        `gross_weight` decimal(15,2) DEFAULT NULL,
        `net_weight` decimal(15,2) DEFAULT NULL,
        `volume` varchar(100) DEFAULT NULL,
        `item_order` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `packing_list_id` (`packing_list_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

$packingItemColumns = [
    'taxrate'      => 'DECIMAL(15,2) NOT NULL DEFAULT 0.00',
    'packing_qty'  => 'DECIMAL(15,2) NOT NULL DEFAULT 1.00',
    'unit_type'    => "VARCHAR(20) NOT NULL DEFAULT 'box'",
    'unit_label'   => 'VARCHAR(100) NULL DEFAULT NULL',
    'length'       => 'DECIMAL(15,2) NULL DEFAULT NULL',
    'width'        => 'DECIMAL(15,2) NULL DEFAULT NULL',
    'height'       => 'DECIMAL(15,2) NULL DEFAULT NULL',
];

foreach ($packingItemColumns as $column => $definition) {
    if ($CI->db->table_exists(db_prefix() . 'otmain_packing_list_items') && !$CI->db->field_exists($column, db_prefix() . 'otmain_packing_list_items')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'otmain_packing_list_items` ADD `' . $column . '` ' . $definition);
    }
}

$packingHeaderColumns = [
    'total_tax'         => 'DECIMAL(15,2) NOT NULL DEFAULT 0.00',
    'total'             => 'DECIMAL(15,2) NOT NULL DEFAULT 0.00',
    'total_cbm'         => 'DECIMAL(15,2) NOT NULL DEFAULT 0.00',
    'conversion_rate'   => 'DECIMAL(15,6) NULL DEFAULT NULL',
    'conversion_currency' => 'INT(11) NULL DEFAULT NULL',
];

foreach ($packingHeaderColumns as $column => $definition) {
    if ($CI->db->table_exists(db_prefix() . 'otmain_packing_lists') && !$CI->db->field_exists($column, db_prefix() . 'otmain_packing_lists')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'otmain_packing_lists` ADD `' . $column . '` ' . $definition);
    }
}

// Proposal (and shared sales) line-item profit % — admin-only UI on proposals
if ($CI->db->table_exists(db_prefix() . 'itemable') && !$CI->db->field_exists('profit_percent', db_prefix() . 'itemable')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'itemable` ADD `profit_percent` DECIMAL(15,2) NULL DEFAULT NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'otmain_purchase_orders')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "otmain_purchase_orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `number` int(11) NOT NULL,
        `prefix` varchar(50) DEFAULT NULL,
        `formatted_number` varchar(100) DEFAULT NULL,
        `date` date NOT NULL,
        `supplierid` int(11) NOT NULL DEFAULT 0,
        `supplier_quote_ref` varchar(191) DEFAULT NULL,
        `contact_person` varchar(191) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `iban` varchar(100) DEFAULT NULL,
        `currency` int(11) NOT NULL DEFAULT 0,
        `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
        `total_tax` decimal(15,2) NOT NULL DEFAULT 0.00,
        `total` decimal(15,2) NOT NULL DEFAULT 0.00,
        `adminnote` text,
        `addedfrom` int(11) NOT NULL DEFAULT 0,
        `datecreated` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `supplierid` (`supplierid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

$poColumns = [
    'document_title'         => "VARCHAR(191) NULL DEFAULT 'Purchase Order'",
    'supplier_address'       => 'TEXT NULL',
    'otmain_contact_id'      => 'INT(11) NULL DEFAULT NULL',
    'company_name'           => "VARCHAR(191) NULL DEFAULT 'OT-MAIN'",
    'company_address'        => "VARCHAR(191) NULL DEFAULT 'Bajonetstraat 52'",
    'company_postal_code'    => "VARCHAR(50) NULL DEFAULT '3014ZK'",
    'company_city'           => "VARCHAR(100) NULL DEFAULT 'Rotterdam'",
    'company_country'        => "VARCHAR(100) NULL DEFAULT 'The Netherlands'",
    'company_phone'          => "VARCHAR(50) NULL DEFAULT '+31618228651'",
    'company_email_invoices' => "VARCHAR(100) NULL DEFAULT 'inv@otmain.com'",
    'company_website'        => "VARCHAR(100) NULL DEFAULT 'www.otmain.com'",
    'company_vat'            => "VARCHAR(50) NULL DEFAULT 'NL004830818B51'",
    'company_coc'            => "VARCHAR(50) NULL DEFAULT '90597427'",
];

foreach ($poColumns as $column => $definition) {
    if ($CI->db->table_exists(db_prefix() . 'otmain_purchase_orders') && !$CI->db->field_exists($column, db_prefix() . 'otmain_purchase_orders')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'otmain_purchase_orders` ADD `' . $column . '` ' . $definition);
    }
}

if (!$CI->db->table_exists(db_prefix() . 'otmain_purchase_order_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "otmain_purchase_order_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `purchase_order_id` int(11) NOT NULL,
        `description` text,
        `qty` decimal(15,2) NOT NULL DEFAULT 1.00,
        `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
        `taxname` varchar(100) DEFAULT NULL,
        `taxrate` decimal(15,2) DEFAULT NULL,
        `total` decimal(15,2) NOT NULL DEFAULT 0.00,
        `item_order` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `purchase_order_id` (`purchase_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

$options = [
    'next_otmain_packing_list_number'   => '1',
    'next_otmain_purchase_order_number' => '100',
    'otmain_packing_list_prefix'      => 'PL-',
    'otmain_purchase_order_prefix'      => 'PO-',
    'otmain_bank_details_eur'           => json_encode([
        'account_holder' => 'OT-Main',
        'iban'           => 'BE46 9675 4582 6036',
        'swift'          => 'TRWIBEB1XXX',
        'bank'           => 'Wise',
        'address'        => 'Rue du Trône 100, 3rd floor, Brussels, 1050, Belgium',
    ]),
    'otmain_bank_details_usd' => json_encode([
        'account_holder' => 'OT-Main',
        'account_number' => '192552059816660',
        'routing_number' => '084009519',
        'swift'          => 'TRWIUS35XXX',
        'bank'           => 'Wise US Inc',
        'address'        => '108 W 13th St, Wilmington, DE, 19801, United States',
    ]),
    // Optional rates for auto-calculated totals in PDF
    'otmain_eur_to_usd_rate'             => '',
    'otmain_default_conversion_currency' => '',
    'otmain_gold_price_eur_per_gram'     => '',
];

foreach ($options as $name => $value) {
    if (get_option($name) === false || get_option($name) === '') {
        add_option($name, $value);
    }
}

update_option('estimate_prefix', 'OTMSQ-');
update_option('invoice_prefix', 'INV-');
update_option('proposal_number_prefix', 'OTPSQ');
update_option('estimate_number_format', '5');
update_option('invoice_number_format', '6');
update_option('predefined_terms_estimate', otmain_get_quotation_terms());
update_option('predefined_terms_invoice', otmain_get_invoice_terms());
