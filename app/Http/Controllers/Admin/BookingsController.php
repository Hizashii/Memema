<?php

class BookingsController extends Controller {
    private $bookingRepository;
    private $invoiceService;
    
    public function __construct() {
        parent::__construct();
        $this->bookingRepository = new BookingRepository();
        $this->invoiceService = new InvoiceService();
    }
    
    public function index() {
        try {
            $bookings = $this->bookingRepository->findAll();
        } catch (Exception $e) {
            $bookings = [];
            $error = "Unable to load bookings.";
        }
        
        $this->view('admin/bookings/index', [
            'bookings' => $bookings,
            'error' => $error ?? null,
            'currentPage' => 'bookings'
        ]);
    }
    
    public function invoice() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/bookings');
        }
        
        try {
            $invoice = $this->invoiceService->generateInvoice($id);
            $this->view('admin/bookings/invoice', [
                'invoice' => $invoice,
                'currentPage' => 'bookings'
            ]);
        } catch (Exception $e) {
            $this->redirect('/admin/bookings?error=' . urlencode($e->getMessage()));
        }
    }
}

