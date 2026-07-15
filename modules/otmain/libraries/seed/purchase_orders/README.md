# Seed purchase orders (OT-Main)

Satu file per PO. Daftarkan di `manifest.php` → `purchase_orders`.

| Key | PDF # | Supplier | Currency | Total |
|-----|-------|----------|----------|-------|
| `po_100_pov_valves` | 2026-PO-100 | POV Fluid (Wuhu) | USD | 3.084,00 |
| `po_101_interfilter` | 2026-PO-101 | Interfilter | EUR | 134,16 |
| `po_102_distrimex` | 2026-PO-102 | Distrimex | EUR | 361,39 |
| `po_103_nanjing_hose` | 2026-PO-103 | Nanjing Deers | USD | 10.169,20 |
| `po_104_nanjing_expansion` | 2026-PO-104 | Nanjing Deers | USD | 3.281,60 |
| `po_105_handelsmij_spt` | 2026-OTMPO-105 | Handelsmij SPT | EUR | 7.395,52 |
| `po_106_hydraunica` | 2026-PO-106 | Hydraunica → OTMSQ-114 | EUR | 211,17 |
| `po_107_rr_holland` | 2026-PO-107 | RR Holland → OTMSQ-114 | EUR | 466,00 |
| `po_108_agung_mccb` | 2026-PO-108 | PT.Agung Buana → SQ-109 | IDR | 9.668.100 |

Fields: `supplier_company`, `currency_code`, `number` / optional `prefix`, `related_proposal_key` (opsional), `purchase_order` + `items` (`description`, `qty`, `unit_price`, `taxrate`).

Lihat: `customize/docs/SEED-FROM-PDF.md`, `customize/docs/SEED-DEMO-DATA.md`.
