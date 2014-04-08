<?php namespace ninja\repositories;

use Invoice;
use InvoiceItem;
use Product;
use Utils;
use TaxRate;

class InvoiceRepository
{
	public function getInvoices($accountId, $clientPublicId = false, $filter = false)
	{
    	$query = \DB::table('invoices')
    				->join('clients', 'clients.id', '=','invoices.client_id')
  					->join('invoice_statuses', 'invoice_statuses.id', '=', 'invoices.invoice_status_id')
  					->join('contacts', 'contacts.client_id', '=', 'clients.id')
  					->where('invoices.account_id', '=', $accountId)
    				->where('clients.deleted_at', '=', null)
    				->where('invoices.is_recurring', '=', false)    			
    				->where('contacts.is_primary', '=', true)	
  					->select('clients.public_id as client_public_id', 'invoice_number', 'clients.name as client_name', 'invoices.public_id', 'amount', 'invoices.balance', 'invoice_date', 'due_date', 'invoice_statuses.name as invoice_status_name', 'clients.currency_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email');

      if (!\Session::get('show_trash'))
      {
        $query->where('invoices.deleted_at', '=', null);
      }

    	if ($clientPublicId) 
    	{
    		$query->where('clients.public_id', '=', $clientPublicId);
    	}

    	if ($filter)
    	{
    		$query->where(function($query) use ($filter)
            {
            	$query->where('clients.name', 'like', '%'.$filter.'%')
            		  ->orWhere('invoices.invoice_number', 'like', '%'.$filter.'%')
            		  ->orWhere('invoice_statuses.name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.first_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.last_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.email', 'like', '%'.$filter.'%');
            });
    	}

    	return $query;
	}

	public function getRecurringInvoices($accountId, $clientPublicId = false, $filter = false)
	{
    	$query = \DB::table('invoices')
    				->join('clients', 'clients.id', '=','invoices.client_id')
  					->join('frequencies', 'frequencies.id', '=', 'invoices.frequency_id')
	   				->join('contacts', 'contacts.client_id', '=', 'clients.id')
		  			->where('invoices.account_id', '=', $accountId)
            		->where('clients.deleted_at', '=', null)
    				->where('invoices.is_recurring', '=', true)
    				->where('contacts.is_primary', '=', true)	
			   		->select('clients.public_id as client_public_id', 'clients.name as client_name', 'invoices.public_id', 'amount', 'frequencies.name as frequency', 'start_date', 'end_date', 'clients.currency_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email');

    	if ($clientPublicId) 
    	{
    		$query->where('clients.public_id', '=', $clientPublicId);
    	}

      if (!\Session::get('show_trash'))
      {
        $query->where('invoices.deleted_at', '=', null);
      }

    	if ($filter)
    	{
    		$query->where(function($query) use ($filter)
            {
            	$query->where('clients.name', 'like', '%'.$filter.'%')
            		  ->orWhere('invoices.invoice_number', 'like', '%'.$filter.'%');
            });
    	}

    	return $query;
	}

	public function getDeletedInvoices($accountId, $clientPublicId = false, $filter = false)
	{
    	$query = \DB::table('invoices')
    				->join('clients', 'clients.id', '=','invoices.client_id')
  					->join('invoice_statuses', 'invoice_statuses.id', '=', 'invoices.invoice_status_id')
  					->join('contacts', 'contacts.client_id', '=', 'clients.id')
  					->where('invoices.account_id', '=', $accountId)
            		->whereNotNull('invoices.deleted_at')
    				->where('invoices.is_deleted', '=', true)
    				->where('invoices.is_recurring', '=', false)    			
    				->where('contacts.is_primary', '=', true)	
  					->select('clients.public_id as client_public_id', 'invoice_number', 'clients.name as client_name', 'invoices.public_id', 'amount', 'invoices.balance', 'invoice_date', 'due_date', 'invoice_statuses.name as invoice_status_name', 'clients.currency_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email');

    	if ($clientPublicId) 
    	{
    		$query->where('clients.public_id', '=', $clientPublicId);
    	}

    	if ($filter)
    	{
    		$query->where(function($query) use ($filter)
            {
            	$query->where('clients.name', 'like', '%'.$filter.'%')
            		  ->orWhere('invoices.invoice_number', 'like', '%'.$filter.'%')
            		  ->orWhere('invoice_statuses.name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.first_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.last_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.email', 'like', '%'.$filter.'%');
            });
    	}

    	return $query;
	}

	public function getArchivedInvoices($accountId, $clientPublicId = false, $filter = false)
	{
    	$query = \DB::table('invoices')
    				->join('clients', 'clients.id', '=','invoices.client_id')
  					->join('invoice_statuses', 'invoice_statuses.id', '=', 'invoices.invoice_status_id')
  					->join('contacts', 'contacts.client_id', '=', 'clients.id')
  					->where('invoices.account_id', '=', $accountId)
            		->whereNotNull('invoices.deleted_at')
    				->where('invoices.is_deleted', '=', false)
    				->where('invoices.is_recurring', '=', false)    			
    				->where('contacts.is_primary', '=', true)	
  					->select('clients.public_id as client_public_id', 'invoice_number', 'clients.name as client_name', 'invoices.public_id', 'amount', 'invoices.balance', 'invoice_date', 'due_date', 'invoice_statuses.name as invoice_status_name', 'clients.currency_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email');

    	if ($clientPublicId) 
    	{
    		$query->where('clients.public_id', '=', $clientPublicId);
    	}

    	if ($filter)
    	{
    		$query->where(function($query) use ($filter)
            {
            	$query->where('clients.name', 'like', '%'.$filter.'%')
            		  ->orWhere('invoices.invoice_number', 'like', '%'.$filter.'%')
            		  ->orWhere('invoice_statuses.name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.first_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.last_name', 'like', '%'.$filter.'%')
                  ->orWhere('contacts.email', 'like', '%'.$filter.'%');
            });
    	}

    	return $query;
	}

	public function getErrors($input)
	{
		$contact = (array) $input->client->contacts[0];
		$rules = ['email' => 'required|email'];
    	$validator = \Validator::make($contact, $rules);

    	if ($validator->fails())
    	{
    		return $validator;
    	}

    	$invoice = (array) $input;
    	$invoiceId = isset($invoice['public_id']) && $invoice['public_id'] ? Invoice::getPrivateId($invoice['public_id']) : null;
    	$rules = ['invoice_number' => 'required|unique:invoices,invoice_number,' . $invoiceId . ',id,account_id,' . \Auth::user()->account_id];    	

    	if ($invoice['is_recurring'] && $invoice['start_date'] && $invoice['end_date'])
    	{
    		$rules['end_date'] = 'after:' . $invoice['start_date'];
    	}

    	$validator = \Validator::make($invoice, $rules);

    	if ($validator->fails())
    	{
    		return $validator;
    	}

    	return false;
	}

	public function save($publicId, $data)
	{
		if ($publicId) 
		{
			$invoice = Invoice::scope($publicId)->firstOrFail();
		} 
		else 
		{				
			$invoice = Invoice::createNew();			
		}			
		
		$invoice->client_id = $data['client_id'];
		$invoice->discount = Utils::parseFloat($data['discount']);
		$invoice->invoice_number = trim($data['invoice_number']);
		$invoice->invoice_date = Utils::toSqlDate($data['invoice_date']);
		$invoice->due_date = Utils::toSqlDate($data['due_date']);					

		$invoice->is_recurring = $data['is_recurring'] ? true : false;
		$invoice->frequency_id = $data['frequency_id'] ? $data['frequency_id'] : 0;
		$invoice->start_date = Utils::toSqlDate($data['start_date']);
		$invoice->end_date = Utils::toSqlDate($data['end_date']);
		$invoice->terms = trim($data['terms']);
		$invoice->public_notes = trim($data['public_notes']);
		$invoice->po_number = trim($data['po_number']);
    $invoice->invoice_design_id = $data['invoice_design_id'];

		if (isset($data['tax_rate']) && Utils::parseFloat($data['tax_rate']) > 0)
		{
			$invoice->tax_rate = Utils::parseFloat($data['tax_rate']);
			$invoice->tax_name = trim($data['tax_name']);
		} 
		else
		{
			$invoice->tax_rate = 0;
			$invoice->tax_name = '';
		}
		
		$total = 0;						
		
		foreach ($data['invoice_items'] as $item) 
		{
			if (!$item->cost && !$item->qty && !$item->product_key && !$item->notes)
			{
				continue;
			}

			$invoiceItemCost = Utils::parseFloat($item->cost);
			$invoiceItemQty = Utils::parseFloat($item->qty);
			$invoiceItemTaxRate = 0;

			if (isset($item->tax_rate) && Utils::parseFloat($item->tax_rate) > 0)
			{
				$invoiceItemTaxRate = Utils::parseFloat($item->tax_rate);				
			}

			$lineTotal = $invoiceItemCost * $invoiceItemQty;
			$total += $lineTotal + ($lineTotal * $invoiceItemTaxRate / 100);
		}

		if ($invoice->discount > 0)
		{
			$total *= (100 - $invoice->discount) / 100;
		}

		$total += $total * $invoice->tax_rate / 100;

    if ($publicId)    
    {
		  $invoice->balance = $total - ($invoice->amount - $invoice->balance);
    }
    else
    {
      $invoice->balance = $total; 
    }

    $invoice->amount = $total;
		$invoice->save();

    $invoice->invoice_items()->forceDelete();
    
    foreach ($data['invoice_items'] as $item) 
    {
      if (!$item->cost && !$item->qty && !$item->product_key && !$item->notes)
      {
        continue;
      }

      if ($item->product_key)
      {
        $product = Product::findProductByKey(trim($item->product_key));

        if (!$product)
        {
          $product = Product::createNew();            
          $product->product_key = trim($item->product_key);
        }

        /*
        $product->notes = $item->notes;
        $product->cost = $item->cost;
        $product->qty = $item->qty;
        */
        
        $product->save();
      }

      $invoiceItem = InvoiceItem::createNew();
      $invoiceItem->product_id = isset($product) ? $product->id : null;
      $invoiceItem->product_key = trim($item->product_key);
      $invoiceItem->notes = trim($item->notes);
      $invoiceItem->cost = Utils::parseFloat($item->cost);
      $invoiceItem->qty = Utils::parseFloat($item->qty);
      $invoiceItem->tax_rate = 0;

      if (isset($item->tax_rate) && Utils::parseFloat($item->tax_rate) > 0)
      {
        $invoiceItem->tax_rate = Utils::parseFloat($item->tax_rate);
        $invoiceItem->tax_name = trim($item->tax_name);
      }

      $invoice->invoice_items()->save($invoiceItem);
    }

		if ($data['set_default_terms'])
		{
			$account = \Auth::user()->account;
			$account->invoice_terms = $invoice->terms;
			$account->save();
		}

		return $invoice;
	}

	public function bulk($ids, $action)
	{
		if (!$ids)
		{
			return 0;
		}

		$invoices = Invoice::withTrashed()->scope($ids)->get();

		foreach ($invoices as $invoice) 
		{
			switch($action)
			{
				case 'delete':
					$invoice->is_deleted = true;
					$invoice->save();
					$invoice->delete();
					break;
				case 'archive':
					$invoice->delete();
					break;
				case 'undelete':
				case 'unarchive':
					$invoice->is_deleted = false;
					$invoice->deleted_at = null;
					$invoice->save();
					break;
			}
		}

		return count($invoices);
	}
	public function getPastDueInvoices()
	{
		$datediff = \DB::raw('datediff(now(), invoice_date) IN(1,2,3,7)');
    	$query = \DB::table('invoices')
    				->join('clients', 'clients.id', '=','invoices.client_id')
  					->join('invoice_statuses', 'invoice_statuses.id', '=', 'invoices.invoice_status_id')
  					->join('contacts', 'contacts.client_id', '=', 'clients.id')
    				->where('clients.deleted_at', '=', null)
					->where('invoices.deleted_at', '=', null)
    				->where('invoices.is_recurring', '=', false)    			
    				->where('contacts.is_primary', '=', true)	
					->where($datediff, '=', true)
  					->select('clients.public_id as client_public_id', 'invoice_number', 'clients.name as client_name', 'invoices.public_id', 'amount', 'invoices.balance', 'invoice_date', 'due_date', 'invoice_statuses.name as invoice_status_name', 'clients.currency_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email');

		return $query;
	}
}
