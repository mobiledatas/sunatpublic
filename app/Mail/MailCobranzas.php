<?php

namespace App\Mail;

use App\Http\Controllers\SharepointController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailCobranzas extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $customer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice,$customer)
    {
        //
        $this->invoice = $invoice;
        $this->customer = $customer;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Mail Cobranzas',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.Customer',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    public function build(){
        return $this->subject("FACTURA ELECTRONICA MDS # {$this->invoice->serie}-{$this->invoice->correlative}")
        ->view('mail.Customer')->attach(storage_path('/app/xmls/'.$this->invoice->document_xml),[
            'as'=>"FACTURA-".$this->invoice->serie.$this->invoice->correlative.".xml",
            'mime'=>'text/xml'
        ])
        ->attach(storage_path('/app/reports/'.$this->invoice->document_pdf),[
            'as'=>"FACTURA-".$this->invoice->serie.$this->invoice->correlative.".pdf",
            'mime'=>'application/pdf'
        ]);
    }
}
