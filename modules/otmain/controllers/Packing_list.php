<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Packing_list extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('packing_list_model');
        $this->load->helper('otmain/otmain');
    }

    public function index()
    {
        if (staff_cant('view', 'otmain_packing_list')) {
            access_denied('otmain_packing_list');
        }

        $data['title'] = _l('otmain_packing_lists');
        $this->load->view('packing_list/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'otmain_packing_list')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(OTMAIN_MODULE_NAME, 'packing_list/table'));
    }

    public function packing_list($id = '')
    {
        if ($this->input->post()) {
            if ($id === '') {
                if (staff_cant('create', 'otmain_packing_list')) {
                    access_denied('otmain_packing_list');
                }
                $id = $this->packing_list_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('otmain_packing_list')));
                    redirect(admin_url('otmain/packing_list/packing_list/' . $id));
                }
            } else {
                if (staff_cant('edit', 'otmain_packing_list')) {
                    access_denied('otmain_packing_list');
                }
                $this->packing_list_model->update($this->input->post(), $id);
                set_alert('success', _l('updated_successfully', _l('otmain_packing_list')));
                redirect(admin_url('otmain/packing_list/packing_list/' . $id));
            }
        }

        if ($id !== '') {
            if (staff_cant('view', 'otmain_packing_list')) {
                access_denied('otmain_packing_list');
            }
            $data['packing_list'] = $this->packing_list_model->get($id);
            if (!$data['packing_list']) {
                show_404();
            }
            $data['title'] = _l('otmain_packing_list') . ' #' . $data['packing_list']->formatted_number;
        } else {
            if (staff_cant('create', 'otmain_packing_list')) {
                access_denied('otmain_packing_list');
            }
            $data['title'] = _l('otmain_new_packing_list');
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $this->load->view('packing_list/packing_list', $data);
    }

    public function delete($id)
    {
        if (staff_cant('delete', 'otmain_packing_list')) {
            access_denied('otmain_packing_list');
        }
        $this->packing_list_model->delete($id);
        set_alert('success', _l('deleted', _l('otmain_packing_list')));
        redirect(admin_url('otmain/packing_list'));
    }

    public function pdf($id)
    {
        if (staff_cant('view', 'otmain_packing_list')) {
            access_denied('otmain_packing_list');
        }

        $packing = $this->packing_list_model->get($id);
        if (!$packing) {
            show_404();
        }

        $type = 'D';
        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $this->load->library('otmain/pdf/Packing_list_pdf');
        $pdf = new Packing_list_pdf($packing);
        $pdf->prepare();
        $pdf->Output(mb_strtoupper(otmain_packing_list_pdf_filename($packing), 'UTF-8') . '.pdf', $type);
    }
}
