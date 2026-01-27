<h2>Ledger for {{ $product->name }}</h2>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Reference</th>
            <th>In</th>
            <th>Out</th>
            <th>Balance</th>
            <th>Customer Name</th>
            <th>Product Code</th>

        </tr>
    </thead>
    <tbody>
        @foreach($product->ledgers as $entry)
            <tr>
                <td>{{ $entry->date }}</td>
                <td>{{ ucfirst($entry->type) }}</td>
                <td>{{ $entry->reference }}</td>
                <td>{{ $entry->quantity_in }}</td>
                <td>{{ $entry->quantity_out }}</td>
                <td>{{ $entry->balance }}</td>
                <td>{{ $entry->customer_name }}</td>
                <td>{{ $entry->product_code }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr>


