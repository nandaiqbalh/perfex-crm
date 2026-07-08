<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_order_model');
        $this->load->helper('otmain/otmain');
    }

    public function index()
    {
        if (staff_cant('view', 'otmain_purchase_order')) {
            access_denied('otmain_purchase_order');
        }

        $data['title'] = _l('otmain_purchase_orders');
        $this->load->view('purchase_order/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'otmain_purchase_order')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(OTMAIN_MODULE_NAME, 'purchase_order/table'));
    }

    public function purchase_order($id = '')
    {
        if ($this->input->post()) {
            if ($id === '') {
                if (staff_cant('create', 'otmain_purchase_order')) {
                    access_denied('otmain_purchase_order');
                }
                $id = $this->purchase_order_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('otmain_purchase_order')));
                    redirect(admin_url('otmain/purchase_order/purchase_order/' . $id));
                }
            } else {
                if (staff_cant('edit', 'otmain_purchase_order')) {
                    access_denied('otmain_purchase_order');
                }
                $this->purchase_order_model->update($this->input->post(), $id);
                set_alert('success', _l('updated_successfully', _l('otmain_purchase_order')));
                redirect(admin_url('otmain/purchase_order/purchase_order/' . $id));
            }
        }

        if ($id !== '') {
            if (staff_cant('view', 'otmain_purchase_order')) {
                access_denied('otmain_purchase_order');
            }
            $data['purchase_order'] = $this->purchase_order_model->get($id);
            if (!$data['purchase_order']) {
                show_404();
            }
            $data['title'] = _l('otmain_purchase_order') . ' #' . $data['purchase_order']->formatted_number;
        } else {
            if (staff_cant('create', 'otmain_purchase_order')) {
                access_denied('otmain_purchase_order');
            }
            $data['title'] = _l('otmain_new_purchase_order');
        }

        $this->load->model('currencies_model');
        $data['currencies']    = $this->currencies_model->get();
        $data['po_defaults']   = otmain_get_po_company_defaults();
        $data['next_po_number'] = otmain_preview_purchase_order_number();
        $this->load->view('purchase_order/purchase_order', $data);
    }

    public function delete($id)
    {
        if (staff_cant('delete', 'otmain_purchase_order')) {
            access_denied('otmain_purchase_order');
        }
        $this->purchase_order_model->delete($id);
        set_alert('success', _l('deleted', _l('otmain_purchase_order')));
        redirect(admin_url('otmain/purchase_order'));
    }

    public function pdf($id)
    {
        if (staff_cant('view', 'otmain_purchase_order')) {
            access_denied('otmain_purchase_order');
        }

        $po = $this->purchase_order_model->get($id);
        if (!$po) {
            show_404();
        }

        $type = 'D';
        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $this->load->library('otmain/pdf/Purchase_order_pdf');
        $pdf = new Purchase_order_pdf($po);
        $pdf->prepare();
        $pdf->Output(mb_strtoupper(otmain_purchase_order_pdf_filename($po), 'UTF-8') . '.pdf', $type);
    }
}
