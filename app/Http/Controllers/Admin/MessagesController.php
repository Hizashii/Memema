<?php
/**
 * Admin Messages Controller
 */

class MessagesController extends Controller {
    private $contactMessageRepository;
    
    public function __construct() {
        parent::__construct();
        $this->contactMessageRepository = new ContactMessageRepository();
    }
    
    public function index() {
        try {
            $messages = $this->contactMessageRepository->findAll();
        } catch (Exception $e) {
            $messages = [];
            $error = "Unable to load messages.";
        }
        
        $this->view('admin/messages/index', [
            'messages' => $messages,
            'error' => $error ?? null,
            'currentPage' => 'messages'
        ]);
    }
    
    public function show() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/messages');
        }
        
        $message = $this->contactMessageRepository->findById($id);
        if (!$message) {
            $this->redirect('/admin/messages?error=' . urlencode('Message not found'));
        }
        
        if ($message['status'] === 'new') {
            $this->contactMessageRepository->updateStatus($id, 'read');
            $message['status'] = 'read';
        }
        
        $this->view('admin/messages/view', [
            'message' => $message,
            'currentPage' => 'messages'
        ]);
    }
    
    public function delete() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/messages');
        }
        
        try {
            $this->contactMessageRepository->delete($id);
            $this->redirect('/admin/messages?success=' . urlencode('Message deleted successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/messages?error=' . urlencode($e->getMessage()));
        }
    }
}

