# OT-Main production seed data

Orchestrator: `../Otmain_seed.php`  
Docs: `customize/docs/SEED-DEMO-DATA.md` · `SEED-FROM-PDF.md` · `SEED-AI-PROMPT.md`

| Path | Peran |
|------|--------|
| `manifest.php` | Load order |
| `customers.php` | Client / supplier catalog (48) |
| `proposals/*.php` | One quotation per file (33) |
| `packing_lists/*.php` | One packing list per file (8) |
| `invoices/*.php` | One Perfex invoice per file (47) |
| `purchase_orders/*.php` | One PO per file (9) |

**Marker:** `otmain_prod_v24` (bump in `Otmain_seed.php` when dataset structure changes).

**Run:** `/admin/otmain/seed` · `?force=1` · `?repair=1` · `?customers=1` · `?resync_tracker=1` (all trackers ↔ proposals, including non-seed)

Keep inventory in `SEED-DEMO-DATA.md` in sync with `manifest.php`.
