<tbody>
    @foreach ($clientss as $client)
        <tr>
            <td>{{ $client->name }}</td>
            <td><input type="checkbox" name="client[]" value="{{ $client->id }}"></td>
        </tr>
    @endforeach
</tbody>